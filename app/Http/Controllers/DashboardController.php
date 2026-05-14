<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\People;
use App\Models\Teacher;
use App\Models\Workplaces;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\Auth;
use App\Models\DivisionalEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\ProvincialMinistryOfEducationOffice;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Auth user + roles
        $user = Auth::user();
        $user_roles = $user->roles;

        // People record
        $people = People::where('people_id', $user->people_id)->first();

        if (!$people || !$people->currentAppointment) {
             return view('dashboard', [
                'user' => $user,
                'user_roles' => $user_roles,
                'people' => $people,
                'institutionCount' => 0,
                'teachersCount' => 0,
                'officeLists' => collect(),
                'workplace_level' => 'N/A',
                'selectedDate' => now(),
                'monthLabel' => now()->format('F Y'),
                'startOfWeek' => now()->startOfWeek(Carbon::SUNDAY),
                'endOfWeek' => now()->startOfWeek(Carbon::SUNDAY)->addDays(6)->endOfDay(),
                'weekDays' => collect(range(0, 6))->map(fn($i) => now()->startOfWeek(Carbon::SUNDAY)->addDays($i)),
                'prevWeek' => now()->subWeek()->toDateString(),
                'nextWeek' => now()->addWeek()->toDateString(),
                'todayEvents' => collect(),
            ]);
        }

        $workplaceId = $people->currentAppointment->workplace_id;
        $workplace = Workplaces::where('workplace_id', $workplaceId)->first();

        if (!$workplace) {
            // Similar fallback if workplace is missing but appointment exists (shouldn't happen with FKs but for safety)
             return view('dashboard', [
                'user' => $user,
                'user_roles' => $user_roles,
                'people' => $people,
                'institutionCount' => 0,
                'teachersCount' => 0,
                'officeLists' => collect(),
                'workplace_level' => 'N/A',
                'selectedDate' => now(),
                'monthLabel' => now()->format('F Y'),
                'startOfWeek' => now()->startOfWeek(Carbon::SUNDAY),
                'endOfWeek' => now()->startOfWeek(Carbon::SUNDAY)->addDays(6)->endOfDay(),
                'weekDays' => collect(range(0, 6))->map(fn($i) => now()->startOfWeek(Carbon::SUNDAY)->addDays($i)),
                'prevWeek' => now()->subWeek()->toDateString(),
                'nextWeek' => now()->addWeek()->toDateString(),
                'todayEvents' => collect(),
            ]);
        }

        $chilWorkpalceList = $workplace->getAllChildWorkplaces();
        //dd($chilWorkpalceList);

        // Counts
        $institutionCount = Institution::whereIn('workplace_id', $chilWorkpalceList)->active()->count();
        $teachersCount = People::whereHas('currentAppointment', function ($q) use ($chilWorkpalceList) {
            $q->where('service_id', 'SER001')
              ->whereIn('workplace_id', $chilWorkpalceList);
        })->active()->count();

        //dd($workplace->office_level_id);

        if($workplace->office_level_id == 'OLID001'){
            $officeLists = ProvincialMinistryOfEducationOffice::query()
                            ->whereIn('provincial_ministry_of_education_offices.workplace_id', $chilWorkpalceList)
                            ->leftJoin(
                                'provincial_education_offices',
                                'provincial_education_offices.pmoe_wp_id',
                                '=',
                                'provincial_ministry_of_education_offices.workplace_id'
                            )
                            ->leftJoin(
                                'zonal_education_offices',
                                'zonal_education_offices.peo_wp_id',
                                '=',
                                'provincial_education_offices.workplace_id'
                            )
                            ->leftJoin(
                                'divisional_education_offices',
                                'divisional_education_offices.zeo_wp_id',
                                '=',
                                'zonal_education_offices.workplace_id'
                            )
                            ->leftJoin(
                                'institutions',
                                'institutions.deo_wp_id',
                                '=',
                                'divisional_education_offices.workplace_id'
                            )

                            ->select([
                                'provincial_ministry_of_education_offices.short_name',
                                'provincial_ministry_of_education_offices.id',
                                DB::raw('COUNT(DISTINCT zonal_education_offices.id) as total_zeo'),
                                DB::raw('COUNT(DISTINCT divisional_education_offices.id) as total_deo'),
                                DB::raw('COUNT(DISTINCT institutions.id) as total_institutions'),
                            ])

                            ->groupBy('provincial_ministry_of_education_offices.short_name')
                            ->groupBy('provincial_ministry_of_education_offices.id')
                            ->orderBy('total_institutions', 'desc')
                            ->get();


                            //dd($officeLists);
        }
        elseif($workplace->office_level_id == 'OLID002'){
            $officeLists = ProvincialEducationOffice::query()
                            ->whereIn('provincial_education_offices.workplace_id', $chilWorkpalceList)
                            ->leftJoin(
                                'zonal_education_offices',
                                'zonal_education_offices.peo_wp_id',
                                '=',
                                'provincial_education_offices.workplace_id'
                            )
                            ->leftJoin(
                                'divisional_education_offices',
                                'divisional_education_offices.zeo_wp_id',
                                '=',
                                'zonal_education_offices.workplace_id'
                            )
                            ->leftJoin(
                                'institutions',
                                'institutions.deo_wp_id',
                                '=',
                                'divisional_education_offices.workplace_id'
                            )

                            ->select([
                                'provincial_education_offices.short_name', 
                                'provincial_education_offices.id',
                                DB::raw('COUNT(DISTINCT zonal_education_offices.id) as total_zeo'),
                                DB::raw('COUNT(DISTINCT divisional_education_offices.id) as total_deo'),
                                DB::raw('COUNT(DISTINCT institutions.id) as total_institutions'),
                            ])

                            ->groupBy('provincial_education_offices.short_name')
                            ->groupBy('provincial_education_offices.id')
                            ->orderBy('total_institutions', 'desc')
                            ->get();
        }
        elseif($workplace->office_level_id == 'OLID003'){
            $officeLists = ZonalEducationOffice::query()
                            ->whereIn('zonal_education_offices.workplace_id', $chilWorkpalceList)
                            ->leftJoin(
                                'divisional_education_offices',
                                'divisional_education_offices.zeo_wp_id',
                                '=',
                                'zonal_education_offices.workplace_id'
                            )
                            ->leftJoin(
                                'institutions',
                                'institutions.deo_wp_id',
                                '=',
                                'divisional_education_offices.workplace_id'
                            )

                            ->select([
                                'zonal_education_offices.short_name', 
                                'zonal_education_offices.id',
                                DB::raw('COUNT(DISTINCT divisional_education_offices.id) as total_deo'),
                                DB::raw('COUNT(DISTINCT institutions.id) as total_institutions'),
                            ])

                            ->groupBy('zonal_education_offices.short_name')
                            ->groupBy('zonal_education_offices.id')
                            ->orderBy('total_institutions', 'desc')
                            ->get();
        }
        elseif($workplace->office_level_id == 'OLID004'){
            $officeLists = DivisionalEducationOffice::query()
                            ->whereIn('divisional_education_offices.workplace_id', $chilWorkpalceList)
                            ->leftJoin(
                                'institutions',
                                'institutions.deo_wp_id',
                                '=',
                                'divisional_education_offices.workplace_id'
                            )

                            ->select([
                                'divisional_education_offices.short_name', 
                                'divisional_education_offices.id',
                                DB::raw('COUNT(DISTINCT institutions.id) as total_institutions'),
                            ])

                            ->groupBy('divisional_education_offices.short_name')
                            ->groupBy('divisional_education_offices.id')
                            ->orderBy('total_institutions', 'desc')
                            ->get();
        }
        elseif($workplace->office_level_id == 'OLID005' || $workplace->office_level_id == 'OLID006'){
            $officeLists = Institution::query()
                            ->whereIn('institutions.workplace_id', $chilWorkpalceList)
                            ->leftJoin(
                                'employer_current_appointments',
                                'employer_current_appointments.workplace_id',
                                '=',
                                'institutions.workplace_id'
                            )
                            ->select([
                                'institutions.name', 
                                'institutions.id',
                                DB::raw('COUNT(DISTINCT employer_current_appointments.id) as total_staff'),
                            ])

                            ->groupBy('institutions.name')
                            ->groupBy('institutions.id')
                            ->orderBy('total_staff', 'desc')
                            ->get();
        }

        //dd($workplace->office_level_id);

        // $officeLists = DB::table('institutions')
        //     ->join('districts_lists', 'institutions.district_id', '=', 'districts_lists.district_id')
        //     ->join('provinces_lists', 'districts_lists.province_id', '=', 'provinces_lists.province_id')
        //     ->select('provinces_lists.province_name', DB::raw('COUNT(institutions.id) as total_institutions'))
        //     ->whereIn('institutions.workplace_id', $chilWorkpalceList)
        //     ->groupBy('provinces_lists.province_name')
        //     ->get();
            //dd($officeLists);

        /*
        |--------------------------------------------------------------------------
        | Selected Date (controls "Today's Briefing")
        |--------------------------------------------------------------------------
        | Priority:
        | 1) ?date=YYYY-MM-DD
        | 2) today()
        */
        $selectedDate = $request->query('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : now()->startOfDay();

        /*
        |--------------------------------------------------------------------------
        | Week Context (controls weekly row + prev/next)
        |--------------------------------------------------------------------------
        | Priority:
        | 1) ?week=YYYY-MM-DD
        | 2) selectedDate
        */
        $weekParam = $request->query('week');
        $baseDate = $weekParam ? Carbon::parse($weekParam) : $selectedDate;

        // Match your Blade: start week from Sunday
        $startOfWeek = $baseDate->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek   = $startOfWeek->copy()->addDays(6)->endOfDay();

        // 7 days for the top row
        $weekDays = collect(range(0, 6))->map(function ($i) use ($startOfWeek) {
            return $startOfWeek->copy()->addDays($i);
        });

        $monthLabel = $baseDate->format('F Y');

        // For prev/next arrow navigation (week changes)
        $prevWeek = $startOfWeek->copy()->subWeek()->toDateString();
        $nextWeek = $startOfWeek->copy()->addWeek()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Events (Demo data) - Filtered by selectedDate
        |--------------------------------------------------------------------------
        | Replace $allEvents with your DB query later.
        */
        $allEvents = collect([
            [
                'date' => '2026-01-28',
                'time' => '09:00 AM',
                'title' => 'National Audit Review',
                'location' => 'General Headquarters',
                'theme' => 'indigo',
            ],
        ]);

        // Events for the selected date
        $todayEvents = $allEvents->where('date', $selectedDate->toDateString())->values();

        return view('dashboard', [
            // Existing data
            'user' => $user,
            'user_roles' => $user_roles,
            'people' => $people,
            'institutionCount' => $institutionCount,
            'teachersCount' => $teachersCount,
            'officeLists' => $officeLists,
            'workplace_level' => $workplace->office_level_id,

            // Weekly schedule data
            'selectedDate' => $selectedDate,
            'monthLabel' => $monthLabel,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'weekDays' => $weekDays,
            'prevWeek' => $prevWeek,
            'nextWeek' => $nextWeek,
            'todayEvents' => $todayEvents,
        ]);
    }
}
