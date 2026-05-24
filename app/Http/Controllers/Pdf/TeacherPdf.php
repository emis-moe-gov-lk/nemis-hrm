<?php

namespace App\Http\Controllers\Pdf;

use App\Models\Family;
use App\Models\People;
use App\Models\FamilyMember;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use misterspelik\LaravelPdf\Facades\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class TeacherPdf extends Controller
{
    public function generateSimplePdf($id)
    {
        $people = People::findOrFail($id);

        return $this->streamProfilePdf($people);
    }

    public function downloadMyProfile()
    {
        $people = People::where('people_id', Auth::user()?->people_id)->firstOrFail();

        return $this->streamProfilePdf($people);
    }

    private function streamProfilePdf(People $people)
    {
        // Generate QR
        $svg = QrCode::format('svg')
            ->size(120)
            ->margin(1)
            ->generate($people->people_id);

        $qrCode = 'data:image/svg+xml;base64,' . base64_encode($svg);

        // Load PDF
        $pdf = Pdf::loadView('pdf.teacher-profile-pdf', [
            'people' => $people,
            'qrCode' => $qrCode,

        ]);

        $pdf->SetProtection(['copy', 'print'], '', 'pass');

        return $pdf->stream(($people->nic ?? $people->people_id) . '.pdf');
    }
}
