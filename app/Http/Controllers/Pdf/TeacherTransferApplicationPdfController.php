<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\TeacherTransferApplication;
use App\Support\Transfer\TransferAccess;
use misterspelik\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TeacherTransferApplicationPdfController extends Controller
{
    public function download($id)
    {
        $application = TeacherTransferApplication::with([
            'policy',
            'employee.title',
            'employee.gender',
            'targetProvince',
            'reason',
            'preferences.institution.institution',
            'preferences.zonalOffice.zonal',
            'boardRecommendation.recommendationList',
            'boardRecommendation.selectedZone',
            'boardRecommendation.selectedSchool',
            'boardRecommendation.creator',
        ])
            ->where('transfer_application_id', $id)
            ->firstOrFail();

        // Security check: allow the applicant and scoped transfer/board users only.
        if (!TransferAccess::canViewTeacherTransferApplication(Auth::user(), $application)) {
            abort(403);
        }

        // Generate QR code for Application ID
        $svg = QrCode::format('svg')
            ->size(100)
            ->margin(1)
            ->generate($application->transfer_application_id);

        $qrCode = 'data:image/svg+xml;base64,' . base64_encode($svg);

        $pdf = Pdf::loadView('pdf.teacher-transfer-application-pdf', [
            'application' => $application,
            'qrCode' => $qrCode,
        ]);

        return $pdf->stream('Transfer_Application_' . $application->transfer_application_id . '.pdf');
    }
}
