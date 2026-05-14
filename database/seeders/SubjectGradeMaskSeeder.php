<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectGradeMaskSeeder extends Seeder
{
    /**
     * Grade mask positions are grades 1-13.
     * 0 = not offered, 1 = offered/mandatory, 2 = offered/optional.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $allGrades = '1111111111111';
        $gradeOneToFive = '1111100000000';
        $gradeOneToEleven = '1111111111100';
        $gradeSixToNine = '0000011110000';
        $gradeSixToEleven = '0000011111100';
        $gradeTenToEleven = '0000000001100';
        $gradeTwelveToThirteen = '0000000000011';

        $masks = [
            'SUB0001' => $allGrades, // Buddhism
            'SUB0002' => $gradeSixToEleven, // Sinhala Language & Literature
            'SUB0003' => $gradeOneToEleven, // English Language
            'SUB0004' => $gradeOneToEleven, // Mathematics
            'SUB0005' => $gradeSixToEleven, // History
            'SUB0006' => $gradeTenToEleven, // Business & Accounting Studies
            'SUB0007' => $gradeSixToEleven, // Civic Education
            'SUB0008' => $gradeTenToEleven, // Entrepreneurship Studies
            'SUB0009' => $gradeSixToEleven, // Second Language (Sinhala)
            'SUB0010' => $gradeSixToEleven, // Second Language (Tamil)
            'SUB0011' => $gradeTenToEleven, // Music (Oriental)
            'SUB0012' => $gradeTenToEleven, // Music (Western)
            'SUB0013' => $gradeTenToEleven, // Music (Carnatic)
            'SUB0014' => $gradeTenToEleven, // Dancing (Oriental)
            'SUB0015' => $gradeTenToEleven, // Dancing (Bharata)
            'SUB0016' => $gradeTenToEleven, // Appreciation of English Literary Texts
            'SUB0017' => $gradeTenToEleven, // Appreciation of Sinhala Literary Texts
            'SUB0018' => $gradeTenToEleven, // Drama and Theatre (Sinhala)
            'SUB0019' => $gradeTenToEleven, // Drama and Theatre (Tamil)
            'SUB0020' => $gradeTenToEleven, // Drama and Theatre (English)
            'SUB0021' => $gradeSixToEleven, // Information & Communication Technology
            'SUB0022' => $gradeTenToEleven, // Agriculture & Food Technology
            'SUB0023' => $gradeTenToEleven, // Home Economics
            'SUB0024' => $gradeSixToEleven, // Health & Physical Education
            'SUB0025' => $gradeTenToEleven, // Communication & Media Studies
            'SUB0026' => $gradeTwelveToThirteen, // Physics
            'SUB0027' => $gradeTwelveToThirteen, // Chemistry
            'SUB0028' => $gradeTwelveToThirteen, // Agricultural Science
            'SUB0029' => $gradeTwelveToThirteen, // Biology
            'SUB0030' => $gradeTwelveToThirteen, // Combined Mathematics
            'SUB0031' => $gradeTwelveToThirteen, // Higher Mathematics
            'SUB0032' => $gradeTwelveToThirteen, // General English
            'SUB0033' => $gradeTwelveToThirteen, // Civil Technology
            'SUB0034' => $gradeTwelveToThirteen, // Mechanical Technology
            'SUB0035' => $gradeTwelveToThirteen, // Electrical, Electronic and Information Technology
            'SUB0036' => $gradeTwelveToThirteen, // Food Technology
            'SUB0037' => $gradeTwelveToThirteen, // Agriculture Technology
            'SUB0038' => $gradeTwelveToThirteen, // BioResource Technology
            'SUB0039' => $gradeTwelveToThirteen, // Economics
            'SUB0040' => $gradeTwelveToThirteen, // Political Science
            'SUB0041' => $gradeTwelveToThirteen, // Logic and Scientific Method
            'SUB0042' => $gradeTwelveToThirteen, // History of Sri Lanka
            'SUB0043' => $gradeTwelveToThirteen, // History of India
            'SUB0044' => $gradeTwelveToThirteen, // History of Europe
            'SUB0045' => $gradeTwelveToThirteen, // Modern World History
            'SUB0046' => $gradeTwelveToThirteen, // Business Statistics
            'SUB0047' => $gradeTwelveToThirteen, // Business Studies
            'SUB0048' => $gradeTwelveToThirteen, // Accountancy
            'SUB0049' => $allGrades, // Hinduism
            'SUB0050' => $gradeTwelveToThirteen, // Buddhist Civilization
            'SUB0051' => $gradeTenToEleven, // Art
            'SUB0052' => $gradeTenToEleven, // Dancing (Indigenous)
            'SUB0053' => $gradeTenToEleven, // Dancing (Bharatha)
            'SUB0054' => $gradeTenToEleven, // Music (Carnatic)
            'SUB0055' => $gradeTwelveToThirteen, // Engineering Technology
            'SUB0056' => $gradeTwelveToThirteen, // Biosystems Technology
            'SUB0057' => $gradeTwelveToThirteen, // Science for Technology
            'SUB0058' => $gradeOneToFive, // Sinhala
            'SUB0059' => $gradeOneToFive, // English
            'SUB0060' => $gradeOneToEleven, // Sinhala Language
            'SUB0061' => $gradeOneToEleven, // Tamil Language
            'SUB0062' => $gradeSixToEleven, // Science
            'SUB0063' => $gradeSixToEleven, // Geography
            'SUB0064' => $gradeSixToNine, // Life Skills & Citizenship Education
            'SUB0065' => $gradeSixToNine, // Practical & Technical Skills
            'SUB0066' => $gradeSixToEleven, // Music(Western)
            'SUB0067' => $gradeSixToEleven, // Music(Oriental)
        ];

        foreach ($masks as $subjectId => $gradeMask) {
            DB::table('subject_lists')
                ->where('subject_id', $subjectId)
                ->update([
                    'grade_mask' => $gradeMask,
                    'updated_at' => $now,
                ]);
        }
    }
}
