<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\People;
use App\Models\Workplaces;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        /** @var \App\Models\User $user */
        $user = Auth::user()->load('roles');
        $user_roles = $user->roles;

        // People record + current appointment
        $people = People::with('currentAppointment')
            ->where('people_id', $user->people_id)
            ->first();

        $isSuperAdmin = $user->hasRole('super admin');

        // Determine context workplace
        $workplaceId = ($people && $people->currentAppointment)
            ? $people->currentAppointment->workplace_id
            : ($isSuperAdmin ? 'MOE0000001' : null);

        $workplace = $workplaceId ? Workplaces::find($workplaceId) : null;

        if (!$workplace) {
            return view('dashboard', [
                'user' => $user,
                'user_roles' => $user_roles,
                'people' => $people,
                'institutionCount' => 0,
                'teachersCount' => 0,
                'principalsCount' => 0,
                'otherStaffCount' => 0,
                'zonalOfficeCount' => 0,
                'divisionalOfficeCount' => 0,
                'officeLists' => collect(),
                'totalStudentCount' => 0,
                'workplace_level' => 'N/A',
                'selectedDate' => now(),
                'monthLabel' => now()->format('F Y'),
                'startOfWeek' => now()->startOfWeek(Carbon::SUNDAY),
                'endOfWeek' => now()->startOfWeek(Carbon::SUNDAY)->addDays(6)->endOfDay(),
                'weekDays' => collect(range(0, 6))->map(fn($i) => now()->startOfWeek(Carbon::SUNDAY)->addDays($i)),
                'prevWeek' => now()->subWeek()->toDateString(),
                'nextWeek' => now()->addWeek()->toDateString(),
                'todayEvents' => collect(),
                'instByProv' => collect(),
                'teacherByProv' => collect(),
                'studentByProv' => collect(),
                'teacherByGender' => collect(),
            ]);
        }

        $chilWorkpalceList = Cache::remember("dashboard:child-workplaces:{$workplaceId}", 300, fn() => $workplace->getAllChildWorkplaces());

        // Counts
        $institutionCount = Cache::remember('dashboard:institution-count', 300, fn() => Institution::active()->count());
        $teachersCount = Cache::remember('dashboard:teachers-count', 300, fn() => People::whereHas('currentAppointment', function ($q) {
            $q->whereHas('appointment', fn($sq) => $sq->where('service_id', 'SER001'));
        })->active()->count());

        $principalsCount = Cache::remember('dashboard:principals-count', 300, fn() => People::whereHas('currentAppointment', function ($q) {
            $q->whereHas('appointment', fn($sq) => $sq->where('service_id', 'SER004'));
        })->active()->count());

        $otherStaffCount = Cache::remember('dashboard:other-staff-count', 300, fn() => People::whereHas('currentAppointment', function ($q) {
            $q->whereHas('appointment', fn($sq) => $sq->whereNotIn('service_id', ['SER001', 'SER004']));
        })->active()->count());

        $zonalOfficeCount = Cache::remember('dashboard:zonal-office-count', 300, fn() => ZonalEducationOffice::count());
        $divisionalOfficeCount = Cache::remember('dashboard:divisional-office-count', 300, fn() => DivisionalEducationOffice::count());

        // Nationwide Student Population (Male + Female) for current academic year
        $totalStudentCount = Cache::remember("dashboard:student-count:" . date('Y'), 300, fn() =>
            \App\Models\InstitutionStudentAdmission::where('academic_year', date('Y'))
                ->sum(DB::raw('male_count + female_count'))
        );

        //dd($workplace->office_level_id);

        $officeLists = Cache::remember("dashboard:office-lists:{$workplaceId}:{$workplace->office_level_id}", 300, function () use ($workplace, $chilWorkpalceList) {
            if ($workplace->office_level_id == 'OLID001') {
                return ProvincialMinistryOfEducationOffice::query()
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
            }

            if ($workplace->office_level_id == 'OLID002') {
                return ProvincialEducationOffice::query()
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

            if ($workplace->office_level_id == 'OLID003') {
                return ZonalEducationOffice::query()
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

            if ($workplace->office_level_id == 'OLID004') {
                return DivisionalEducationOffice::query()
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

            if (in_array($workplace->office_level_id, ['OLID005', 'OLID006'], true)) {
                return Institution::query()
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

            return collect();
        });

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

        // Provincial Analytics Chart Data
        $instByProv = Cache::remember('dashboard:inst-by-prov', 300, fn() => DB::table('institutions')
            ->join('districts_lists', 'institutions.district_id', '=', 'districts_lists.district_id')
            ->join('provinces_lists', 'districts_lists.province_id', '=', 'provinces_lists.province_id')
            ->select('provinces_lists.province_name as name', DB::raw('COUNT(institutions.id) as total'))
            ->groupBy('provinces_lists.province_name')
            ->get());

        $teacherByProv = Cache::remember('dashboard:teacher-by-prov', 300, fn() => DB::table('employer_current_appointments')
            ->join('employer_appointments', 'employer_current_appointments.appointment_id', '=', 'employer_appointments.appointment_id')
            ->join('people', 'employer_current_appointments.employee_id', '=', 'people.people_id')
            ->leftJoin('institutions', 'employer_current_appointments.workplace_id', '=', 'institutions.workplace_id')
            ->join('districts_lists', DB::raw('COALESCE(institutions.district_id, people.district_id)'), '=', 'districts_lists.district_id')
            ->join('provinces_lists', 'districts_lists.province_id', '=', 'provinces_lists.province_id')
            ->where('employer_appointments.service_id', 'SER001')
            ->where('people.active_status', 1)
            ->where('employer_appointments.active_status', 1)
            ->select('provinces_lists.province_name as name', DB::raw('COUNT(employer_current_appointments.id) as total'))
            ->groupBy('provinces_lists.province_name')
            ->get());

        $studentByProv = Cache::remember('dashboard:student-by-prov:' . date('Y'), 300, fn() => DB::table('institution_student_admissions')
            ->join('institution_classes', 'institution_student_admissions.institution_class_id', '=', 'institution_classes.id')
            ->join('institution_grades', 'institution_classes.institution_grade_id', '=', 'institution_grades.id')
            ->join('institutions', 'institution_grades.institution_id', '=', 'institutions.id')
            ->join('districts_lists', 'institutions.district_id', '=', 'districts_lists.district_id')
            ->join('provinces_lists', 'districts_lists.province_id', '=', 'provinces_lists.province_id')
            ->where('institution_student_admissions.academic_year', date('Y'))
            ->select('provinces_lists.province_name as name', DB::raw('SUM(institution_student_admissions.male_count + institution_student_admissions.female_count) as total'))
            ->groupBy('provinces_lists.province_name')
            ->get());

        $teacherByGender = Cache::remember('dashboard:teacher-by-gender', 300, fn() => DB::table('employer_current_appointments')
            ->join('employer_appointments', 'employer_current_appointments.appointment_id', '=', 'employer_appointments.appointment_id')
            ->join('people', 'employer_current_appointments.employee_id', '=', 'people.people_id')
            ->join('gender_lists', 'people.gender_id', '=', 'gender_lists.gender_id')
            ->where('employer_appointments.service_id', 'SER001')
            ->where('people.active_status', 1)
            ->where('employer_appointments.active_status', 1)
            ->select('gender_lists.gender_name as name', DB::raw('COUNT(people.id) as total'))
            ->groupBy('gender_lists.gender_name')
            ->get());

        return view('dashboard', [
            // Existing data
            'user' => $user,
            'user_roles' => $user_roles,
            'people' => $people,
            'institutionCount' => $institutionCount,
            'teachersCount' => $teachersCount,
            'principalsCount' => $principalsCount,
            'otherStaffCount' => $otherStaffCount,
            'zonalOfficeCount' => $zonalOfficeCount,
            'divisionalOfficeCount' => $divisionalOfficeCount,
            'officeLists' => $officeLists,
            'totalStudentCount' => $totalStudentCount,
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

            // Provincial analytics data
            'instByProv' => $instByProv,
            'teacherByProv' => $teacherByProv,
            'studentByProv' => $studentByProv,
            'teacherByGender' => $teacherByGender,
        ]);
    }
}
