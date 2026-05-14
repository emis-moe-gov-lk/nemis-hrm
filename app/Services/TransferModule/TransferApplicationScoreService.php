<?php

namespace App\Services\TransferModule;

use App\Models\EmployerAppointmentHistory;
use App\Models\Institution;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferScoreRouteDistance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransferApplicationScoreService
{
    public const DISTANCE = 'distance_current_workplace';

    public const CURRENT_DIFFICULTY = 'current_difficulty_years';

    public const PREVIOUS_DIFFICULTY = 'previous_difficulty_years';

    public const AGE = 'age';

    public const CURRENT_STATION_YEARS = 'current_station_years';

    public const ACHIEVEMENTS = 'achievements';

    public function score(TeacherTransferApplication $application): array
    {
        $application->loadMissing([
            'policy.scoreRules',
            'policy.facilityScoreRules.facility',
            'policy.achievementLevelScores',
            'employee',
            'currentWorkplace.institution.facilities',
            'achievements',
        ]);

        $policy = $application->policy;

        if (! $policy) {
            return $this->emptyResult(__('No transfer policy is attached to this application.'));
        }

        $rules = $policy->scoreRules
            ->where('active_status', true)
            ->keyBy('criteria_key');

        if ($rules->isEmpty()) {
            return $this->emptyResult(__('No scoring rules are configured for this transfer policy.'));
        }

        $asOfDate = $this->scoreAsOfDate($application);
        $breakdown = [];
        $warnings = [];
        $total = 0.0;

        foreach ([
            self::DISTANCE => fn () => $this->distanceScore($application, (float) $rules[self::DISTANCE]->score_per_unit),
            self::CURRENT_DIFFICULTY => fn () => $this->currentDifficultyScore($application, $policy, $asOfDate),
            self::PREVIOUS_DIFFICULTY => fn () => $this->previousDifficultyScore($application, $policy, $asOfDate),
            self::AGE => fn () => $this->ageScore($application, (float) $rules[self::AGE]->base_value, $asOfDate),
            self::CURRENT_STATION_YEARS => fn () => $this->currentStationYearsScore($application, (float) $rules[self::CURRENT_STATION_YEARS]->base_value, $asOfDate),
            self::ACHIEVEMENTS => fn () => $this->achievementScore($application, $policy),
        ] as $criteriaKey => $resolver) {
            if (! $rules->has($criteriaKey)) {
                continue;
            }

            $item = $resolver();
            $breakdown[] = $item;
            $warnings = array_merge($warnings, $item['warnings'] ?? []);
            $total += $item['score'];
        }

        $total = round($total, 2);

        return [
            'total' => $total,
            'formatted_total' => number_format($total, 2),
            'color' => $this->colorForTotal($total),
            'label' => $this->labelForTotal($total),
            'breakdown' => $breakdown,
            'warnings' => array_values(array_unique(array_filter($warnings))),
            'as_of_date' => $asOfDate->toDateString(),
            'has_rules' => true,
        ];
    }

    protected function emptyResult(string $message): array
    {
        return [
            'total' => 0.0,
            'formatted_total' => '0.00',
            'color' => 'zinc',
            'label' => __('Not Scored'),
            'breakdown' => [],
            'warnings' => [$message],
            'as_of_date' => now()->toDateString(),
            'has_rules' => false,
        ];
    }

    protected function scoreAsOfDate(TeacherTransferApplication $application): Carbon
    {
        return $this->parseDate($application->policy?->application_end_date) ?? now();
    }

    protected function distanceScore(TeacherTransferApplication $application, float $scorePerKm): array
    {
        $warnings = [];
        $origin = $this->coordinates($application->latitude, $application->longitude);
        $destination = $this->currentWorkplaceCoordinates($application);

        if (! $origin || ! $destination) {
            return $this->breakdownItem(
                self::DISTANCE,
                __('Distance to Current Workplace'),
                0,
                __('Application permanent-address coordinates and current workplace coordinates are both required.'),
                ['warnings' => [__('Distance score was skipped because coordinates are incomplete.')]]
            );
        }

        $distance = $this->roadDistanceKm($application, $origin, $destination, $warnings);
        $score = round($distance * $scorePerKm, 2);

        return $this->breakdownItem(
            self::DISTANCE,
            __('Distance to Current Workplace'),
            $score,
            __(':distance km x :rate score per km', [
                'distance' => number_format($distance, 2),
                'rate' => number_format($scorePerKm, 2),
            ]),
            ['value' => $distance, 'unit' => 'km', 'warnings' => $warnings]
        );
    }

    protected function currentDifficultyScore(TeacherTransferApplication $application, $policy, Carbon $asOfDate): array
    {
        $facilityId = $application->cwp_facilities_id ?: $application->currentWorkplace?->institution?->facilities_id;

        if (! $facilityId) {
            return $this->breakdownItem(
                self::CURRENT_DIFFICULTY,
                __('Current Workplace Difficulty'),
                0,
                __('Current workplace facility level is not available.'),
                ['warnings' => [__('Current difficulty score was skipped because the current workplace facility level is missing.')]]
            );
        }

        $facilityScore = $this->facilityScore($policy, self::CURRENT_DIFFICULTY, $facilityId);
        $years = $this->yearsBetween($application->current_workplace_join_date, $asOfDate);
        $score = round($facilityScore * $years, 2);

        return $this->breakdownItem(
            self::CURRENT_DIFFICULTY,
            __('Current Workplace Difficulty'),
            $score,
            __(':facilityScore facility score x :years years at current workplace', [
                'facilityScore' => number_format($facilityScore, 2),
                'years' => number_format($years, 2),
            ]),
            ['value' => $years, 'unit' => 'years', 'facility_id' => $facilityId]
        );
    }

    protected function previousDifficultyScore(TeacherTransferApplication $application, $policy, Carbon $asOfDate): array
    {
        $rows = EmployerAppointmentHistory::with('workplace.institution.facilities')
            ->where('employee_id', $application->employee_id)
            ->orderBy('appoint_date')
            ->get();

        $segments = [];
        $score = 0.0;

        foreach ($rows as $history) {
            if ((string) $history->workplace_id === (string) $application->current_workplace && blank($history->end_date)) {
                continue;
            }

            $facilityId = $history->workplace?->institution?->facilities_id;

            if (! $facilityId) {
                continue;
            }

            $start = $this->parseDate($history->appoint_date);
            $end = $this->parseDate($history->end_date) ?? $asOfDate;

            if (! $start || $end->lte($start)) {
                continue;
            }

            if ($end->gt($asOfDate)) {
                $end = $asOfDate->copy();
            }

            $years = $this->yearsBetween($start, $end);
            $facilityScore = $this->facilityScore($policy, self::PREVIOUS_DIFFICULTY, $facilityId);
            $segmentScore = round($facilityScore * $years, 2);
            $score += $segmentScore;

            $segments[] = [
                'workplace' => $history->workplace?->office_name ?? $history->workplace_id,
                'facility_id' => $facilityId,
                'years' => round($years, 2),
                'facility_score' => $facilityScore,
                'score' => $segmentScore,
            ];
        }

        return $this->breakdownItem(
            self::PREVIOUS_DIFFICULTY,
            __('Previous Difficult Area Service'),
            round($score, 2),
            $segments ? __('Sum of each previous workplace facility score x years served') : __('No previous difficult-area service rows were found.'),
            ['segments' => $segments]
        );
    }

    protected function ageScore(TeacherTransferApplication $application, float $baseAge, Carbon $asOfDate): array
    {
        $dob = $this->parseDate($application->employee?->date_of_birth);

        if (! $dob) {
            return $this->breakdownItem(
                self::AGE,
                __('Age'),
                0,
                __('Date of birth is not available.'),
                ['warnings' => [__('Age score was skipped because date of birth is missing.')]]
            );
        }

        $age = $dob->diffInYears($asOfDate);
        $score = $age < $baseAge ? 0 : 1 + (int) floor($age - $baseAge);

        return $this->breakdownItem(
            self::AGE,
            __('Age'),
            $score,
            __('Age :age years, base age :base', ['age' => $age, 'base' => number_format($baseAge, 0)]),
            ['value' => $age, 'unit' => 'years']
        );
    }

    protected function currentStationYearsScore(TeacherTransferApplication $application, float $baseYears, Carbon $asOfDate): array
    {
        $years = $this->yearsBetween($application->current_workplace_join_date, $asOfDate);
        $score = $years < $baseYears ? 0 : 1 + (int) floor($years - $baseYears);

        return $this->breakdownItem(
            self::CURRENT_STATION_YEARS,
            __('Current Station Years'),
            $score,
            __(':years years at current station, base :base years', [
                'years' => number_format($years, 2),
                'base' => number_format($baseYears, 2),
            ]),
            ['value' => $years, 'unit' => 'years']
        );
    }

    protected function achievementScore(TeacherTransferApplication $application, $policy): array
    {
        $levelScores = $policy->achievementLevelScores
            ->keyBy('achievement_level')
            ->map(fn ($row) => (float) $row->score_per_achievement);

        $rows = $application->achievements
            ->map(function ($achievement) use ($levelScores) {
                $score = $achievement->is_included
                    ? (float) ($levelScores->get($achievement->achievement_level, 0))
                    : 0.0;

                return [
                    'id' => $achievement->id,
                    'type' => Str::headline($achievement->achievement_type),
                    'level' => Str::headline($achievement->achievement_level),
                    'title' => $achievement->title,
                    'date' => $achievement->achievement_date?->format('Y-m-d'),
                    'is_included' => (bool) $achievement->is_included,
                    'review_remarks' => $achievement->review_remarks,
                    'score' => round($score, 2),
                ];
            })
            ->values()
            ->all();

        $score = round(collect($rows)->sum('score'), 2);

        return $this->breakdownItem(
            self::ACHIEVEMENTS,
            __('Achievements'),
            $score,
            $rows ? __('Included achievements scored by configured achievement level.') : __('No achievements were submitted with this application.'),
            ['achievements' => $rows]
        );
    }

    protected function roadDistanceKm(TeacherTransferApplication $application, array $origin, array $destination, array &$warnings): float
    {
        $hash = hash('sha256', implode('|', [
            $application->transfer_application_id,
            $application->current_workplace,
            $origin['lat'],
            $origin['lng'],
            $destination['lat'],
            $destination['lng'],
        ]));

        $cached = TeacherTransferScoreRouteDistance::where('route_hash', $hash)->first();

        if ($cached) {
            return (float) $cached->distance_km;
        }

        $distance = null;
        $provider = 'osrm';

        try {
            $baseUrl = rtrim((string) config('services.osrm.url', 'https://router.project-osrm.org'), '/');
            $url = sprintf(
                '%s/route/v1/driving/%s,%s;%s,%s',
                $baseUrl,
                $origin['lng'],
                $origin['lat'],
                $destination['lng'],
                $destination['lat']
            );

            $response = Http::timeout($this->osrmTimeout())
                ->connectTimeout($this->osrmConnectTimeout())
                ->withOptions(['verify' => $this->osrmVerifySsl()])
                ->acceptJson()
                ->get($url, [
                    'overview' => 'false',
                    'alternatives' => 'false',
                ]);

            $meters = $response->json('routes.0.distance');

            if ($response->ok() && is_numeric($meters)) {
                $distance = round(((float) $meters) / 1000, 2);
            }
        } catch (\Throwable $exception) {
            $this->logOsrmFailure($exception, [
                'transfer_application_id' => $application->transfer_application_id,
                'current_workplace_id' => $application->current_workplace,
            ]);
        }

        if ($distance === null) {
            $distance = $this->haversineDistanceKm($origin['lat'], $origin['lng'], $destination['lat'], $destination['lng']);
            $provider = 'straight_line_fallback';
            $warnings[] = __('Road distance could not be resolved, so straight-line distance was used.');
        }

        TeacherTransferScoreRouteDistance::create([
            'transfer_application_id' => $application->transfer_application_id,
            'current_workplace_id' => $application->current_workplace,
            'origin_latitude' => $origin['lat'],
            'origin_longitude' => $origin['lng'],
            'destination_latitude' => $destination['lat'],
            'destination_longitude' => $destination['lng'],
            'route_hash' => $hash,
            'distance_km' => $distance,
            'provider' => $provider,
            'calculated_at' => now(),
        ]);

        return $distance;
    }

    protected function currentWorkplaceCoordinates(TeacherTransferApplication $application): ?array
    {
        $office = $application->currentWorkplace?->office();

        if ($office) {
            return $this->coordinates($office->latitude ?? null, $office->longitude ?? null);
        }

        $institution = Institution::where('workplace_id', $application->current_workplace)->first();

        return $this->coordinates($institution?->latitude, $institution?->longitude);
    }

    protected function osrmTimeout(): int
    {
        return max(1, (int) config('services.osrm.timeout', 5));
    }

    protected function osrmConnectTimeout(): int
    {
        return max(1, (int) config('services.osrm.connect_timeout', 3));
    }

    protected function osrmVerifySsl(): bool
    {
        return filter_var(config('services.osrm.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);
    }

    protected function logOsrmFailure(\Throwable $exception, array $context = []): void
    {
        if (! filter_var(config('services.osrm.log_failures', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        Log::warning('OSRM road distance lookup failed; using straight-line fallback.', $context + [
            'message' => $exception->getMessage(),
        ]);
    }

    protected function facilityScore($policy, string $criteriaKey, string $facilityId): float
    {
        return (float) ($policy->facilityScoreRules
            ->first(fn ($rule) => $rule->criteria_key === $criteriaKey && $rule->facilities_id === $facilityId)
            ?->score_per_year ?? 0);
    }

    protected function breakdownItem(string $key, string $label, float $score, string $formula, array $extra = []): array
    {
        return array_merge([
            'key' => $key,
            'label' => $label,
            'score' => round($score, 2),
            'formatted_score' => number_format(round($score, 2), 2),
            'formula' => $formula,
            'warnings' => [],
        ], $extra);
    }

    protected function yearsBetween($start, $end): float
    {
        $start = $this->parseDate($start);
        $end = $this->parseDate($end);

        if (! $start || ! $end || $end->lte($start)) {
            return 0.0;
        }

        return round($start->diffInDays($end) / 365.25, 4);
    }

    protected function parseDate($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return $value instanceof Carbon ? $value->copy() : Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function coordinates($lat, $lng): ?array
    {
        if ($lat === null || $lng === null || $lat === '' || $lng === '') {
            return null;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        if ($lat > 50 && $lng < 20) {
            [$lat, $lng] = [$lng, $lat];
        }

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return null;
        }

        return ['lat' => round($lat, 7), 'lng' => round($lng, 7)];
    }

    protected function haversineDistanceKm(float $originLat, float $originLng, float $destinationLat, float $destinationLng): float
    {
        $earthRadiusKm = 6371;
        $deltaLat = deg2rad($destinationLat - $originLat);
        $deltaLng = deg2rad($destinationLng - $originLng);

        $a = sin($deltaLat / 2) ** 2
            + cos(deg2rad($originLat)) * cos(deg2rad($destinationLat)) * sin($deltaLng / 2) ** 2;

        return round($earthRadiusKm * (2 * atan2(sqrt($a), sqrt(1 - $a))), 2);
    }

    protected function colorForTotal(float $total): string
    {
        return match (true) {
            $total <= 0 => 'zinc',
            $total < 25 => 'amber',
            $total < 50 => 'blue',
            default => 'emerald',
        };
    }

    protected function labelForTotal(float $total): string
    {
        return match (true) {
            $total <= 0 => __('Not Scored'),
            $total < 25 => __('Low'),
            $total < 50 => __('Medium'),
            default => __('High'),
        };
    }
}
