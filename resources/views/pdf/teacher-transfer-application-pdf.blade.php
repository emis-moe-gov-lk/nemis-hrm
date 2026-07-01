<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Transfer Application - {{ $application->transfer_application_id }}</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
            margin: 30mm 15mm 20mm 15mm;
        }

        body {
            font-family: dejavusans, abhayalibre, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.5;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
        }

        .section {
            margin-bottom: 20px;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            border-left: 4px solid #4f46e5;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            width: 30%;
            text-align: left;
            padding: 6px 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-submitted {
            background-color: #e0e7ff;
            color: #4338ca;
        }

        .status-approved {
            background-color: #dcfce7;
            color: #15803d;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-processing {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-draft {
            background-color: #f1f5f9;
            color: #475569;
        }

        .preference-table {
            width: 100%;
            border-collapse: collapse;
        }

        .preference-table th {
            background-color: #1e293b;
            color: white;
            padding: 8px;
            font-size: 9px;
            text-align: left;
        }

        .preference-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
        }

        .compact-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .compact-table th {
            background-color: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 8px;
        }

        .compact-table td {
            border: 1px solid #e2e8f0;
            color: #1e293b;
            padding: 6px 8px;
            vertical-align: top;
        }

        .muted {
            color: #64748b;
            font-size: 8px;
        }

        .footer-content {
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $capitalizeData = function ($value, string $fallback = 'N/A'): string {
            $text = trim((string) $value);

            if ($text === '') {
                return $fallback;
            }

            return preg_replace_callback('/[^\s,\/\-]+/u', function (array $matches): string {
                $word = $matches[0];

                if (str_contains($word, '@') || preg_match('/\d/u', $word)) {
                    return $word;
                }

                if (preg_match('/^(?:\pL\.)+$/u', $word)) {
                    return mb_strtoupper($word, 'UTF-8');
                }

                if (mb_strlen($word, 'UTF-8') <= 4 && $word === mb_strtoupper($word, 'UTF-8')) {
                    return $word;
                }

                return mb_convert_case(mb_strtolower($word, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
            }, $text);
        };

        $boardDecision = $application->boardRecommendation;
        $boardDecisionLabel =
            $boardDecision?->recommendationList?->decision ??
            (string) ($boardDecision?->recommendation_status ?? '');
        $approvedSchoolName = $capitalizeData($boardDecision?->selectedSchool?->name);
        $approvedZoneName = $capitalizeData($boardDecision?->selectedZone?->name);
        $appointment = $application->appointment;
        $serviceId = $appointment?->service_id;
        $serviceName = $appointment?->service?->service_name ?? '';
        $isSlts = $serviceId === 'SER001' || str_contains(strtoupper($serviceName), 'SLTS');
        $teacher = $application->teacher;
        $workplaceHistory = $appointment?->workplaceHistory ?? collect();
        $applicantName = trim(($application->employee->title->title_name ?? '') . ' ' . ($application->employee->full_name ?? ''));
        $permanentAddress = $application->permanent_address ?:
            trim(
                collect([
                    $application->employee->address_line1 ?? null,
                    $application->employee->address_line2 ?? null,
                    $application->employee->address_line3 ?? null,
                    $application->employee->postal_code ?? null,
                ])->filter()->implode(', '),
            );
        $temporaryAddress = $application->temporary_address ?:
            trim(
                collect([
                    $application->employee->t_address_line1 ?? null,
                    $application->employee->t_address_line2 ?? null,
                    $application->employee->t_address_line3 ?? null,
                    $application->employee->t_postal_code ?? null,
                ])->filter()->implode(', '),
            );
    @endphp

    <htmlpageheader name="page-header">
        <table width="100%" style="vertical-align: bottom; border-bottom: 2px solid #1e293b; padding-bottom: 10px;">
            <tr>
                <td width="80%" valign="bottom" style="border: none;">
                    <span class="header-title">Teacher Transfer Application Form</span>
                </td>
                <td width="20%" align="right" valign="bottom" style="border: none;">
                    <img src="{{ $qrCode }}" style="width: 50px; height: 50px;" />
                </td>
            </tr>
        </table>
    </htmlpageheader>

    <div class="section">
        <div class="section-title">Application Overview</div>
        <table class="data-table">
            <tr>
                <th>Application ID</th>
                <td colspan="3">{{ $application->transfer_application_id }}</td>
            </tr>
            <tr>
                <th>Transfer Policy</th>
                <td colspan="3">{{ $capitalizeData($application->policy->title ?? null) }}</td>
            </tr>
            <tr>
                <th>Submission Date</th>
                <td>{{ $application->created_at->format('F d, Y') }}</td>
                <th>Status</th>
                <td>
                    <span class="status-badge status-{{ $application->status }}">
                        {{ $capitalizeData(str_replace('_', ' ', $application->status)) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Applicant Profile</div>
        <table class="data-table">
            <tr>
                <th>Full Name</th>
                <td colspan="3">
                    {{ $capitalizeData($applicantName) }}
                </td>
            </tr>
            <tr>
                <th>Employee ID</th>
                <td>{{ $application->employee_id }}</td>
                <th>NIC Number</th>
                <td>{{ $application->employee->nic }}</td>
            </tr>
            <tr>
                <th>Current School</th>
                <td colspan="3">
                    {{ $capitalizeData($application->currentWorkplace?->office_name) }}
                </td>
            </tr>
            <tr>
                <th>Service</th>
                <td>{{ $capitalizeData($serviceName) }}</td>
                <th>Appointment ID</th>
                <td>{{ $appointment?->appointment_id ?? ($application->appointment_id ?? 'N/A') }}</td>
            </tr>
            <tr>
                <th>Join Date (Current)</th>
                <td>
                    {{ $application->current_workplace_join_date ? $application->current_workplace_join_date->format('M d, Y') : 'N/A' }}
                    <div style="font-size: 8px; color: #6366f1; margin-top: 2px;">
                        {{ $application->current_workplace_service_years }}</div>
                </td>
                <th>First Appointment</th>
                <td>
                    {{ $application->first_appointment_date ? $application->first_appointment_date->format('M d, Y') : 'N/A' }}
                    <div style="font-size: 8px; color: #6366f1; margin-top: 2px;">
                        {{ $application->total_service_years }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Contact & Address Details</div>
        <table class="data-table">
            <tr>
                <th>Mobile Phone</th>
                <td>{{ $application->employee->phone ?? 'N/A' }}</td>
                <th>Email</th>
                <td>{{ $application->employee->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Permanent Address</th>
                <td colspan="3">
                    {{ $capitalizeData($permanentAddress) }}
                </td>
            </tr>
            <tr>
                <th>Temporary Address</th>
                <td colspan="3">
                    {{ $capitalizeData($temporaryAddress) }}
                </td>
            </tr>
        </table>
    </div>

    @if ($isSlts)
        <div class="section">
            <div class="section-title">SLTS Teaching Details</div>
            <table class="data-table">
                <tr>
                    <th>Teacher Category</th>
                    <td>{{ $capitalizeData($teacher?->teacherCategory?->name) }}</td>

                </tr>
                <tr>
                    <th>Medium</th>
                    <td>{{ $capitalizeData($teacher?->medium?->name) }}</td>
                </tr>
                <tr>
                    <th>Appointment Subject</th>
                    <td>{{ $capitalizeData($teacher?->appointmentSubject?->name_en) }}</td>

                </tr>

                <tr>
                    <th>Current Teaching Subject</th>
                    <td>{{ $capitalizeData($teacher?->currentTeachingSubject?->name_en ?? $teacher?->mainSubject?->name_en) }}
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div class="section">
        <div class="section-title">Working Place History</div>
        <table class="compact-table">
            <thead>
                <tr>
                    <th style="width: 32%;">Workplace</th>
                    <th style="width: 18%;">Office Level</th>
                    <th style="width: 14%;">Start Date</th>
                    <th style="width: 14%;">End Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workplaceHistory as $history)
                    <tr>
                        <td>
                            <strong>{{ $capitalizeData($history->workplace?->office_name, 'Unknown Workplace') }}</strong>
                            @if ($history->is_active)
                                <div class="muted">Current workplace</div>
                            @endif
                        </td>
                        <td>{{ $capitalizeData($history->officeLevel?->office_level_name) }}</td>
                        <td>{{ $history->start_date ? $history->start_date->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $history->end_date ? $history->end_date->format('Y-m-d') : 'Present' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b;">No workplace history records
                            found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Transfer Request Details</div>
        <table class="data-table">
            <tr>
                <th>Target Province</th>
                <td>{{ $capitalizeData($application->targetProvince?->name) }}</td>
            </tr>
            <tr>
                <th>Reason Category</th>
                <td>{{ $capitalizeData($application->reason?->title) }}</td>
            </tr>
            @if ($application->transfer_reason)
                <tr>
                    <th>Detailed Reason</th>
                    <td colspan="3" style="font-style: italic; color: #475569;">
                        "{{ $capitalizeData($application->transfer_reason) }}"
                    </td>
                </tr>
            @endif
        </table>
    </div>

    @if ($boardDecision)
        <div class="section">
            <div class="section-title">Transfer Decision Summary</div>
            <table class="data-table">
                <tr>
                    <th>Decision</th>
                    <td>{{ $capitalizeData($boardDecisionLabel) }}</td>
                    <th>Status</th>
                    <td>
                        <span class="status-badge status-{{ $boardDecision->recommendation_status }}">
                            {{ $capitalizeData(str_replace('_', ' ', $boardDecision->recommendation_status ?? 'pending')) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Approved Zone</th>
                    <td>{{ $approvedZoneName }}</td>
                    <th>Approved School</th>
                    <td>{{ $approvedSchoolName }}</td>
                </tr>
                <tr>
                    <th>Effective Date</th>
                    <td>{{ $boardDecision->transfer_effective_date?->format('F d, Y') ?? 'N/A' }}</td>
                    <th>Decision Officer</th>
                    <td>{{ $capitalizeData($boardDecision->creator?->name_with_initials) }}</td>
                </tr>
                @if (filled($boardDecision->recommendation_remarks))
                    <tr>
                        <th>Official Remarks</th>
                        <td colspan="3">{{ $capitalizeData($boardDecision->recommendation_remarks) }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    <div class="section">
        <div class="section-title">Station Preferences</div>
        <table class="preference-table">
            <thead>
                <tr>
                    <th style="width: 10%; text-align: center;">Order</th>
                    <th style="width: 30%;">Zone</th>
                    <th style="width: 60%;">Institution / School</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($application->preferences as $pref)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $pref->preference_order }}</td>
                        <td>{{ $capitalizeData($pref->zonalOffice?->office_name) }}</td>
                        <td>
                            <strong>{{ $capitalizeData($pref->institution?->office_name) }}</strong>
                            <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">{{ __('Census No') }}:
                                {{ str_pad($pref->institution->office()->census_no ?? '0', 5, '0', STR_PAD_LEFT) }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section" style="margin-top: 40px;">
        <div style="border: 1px solid #e2e8f0; padding: 15px; background-color: #f8fafc;">
            <div style="font-weight: bold; margin-bottom: 10px; font-size: 11px;">Declaration & Verification</div>
            <p style="font-size: 9px; color: #475569; margin-bottom: 20px;">
                I hereby declare that the information provided in this application is true and accurate to the best of
                my knowledge. I understand that any false information may lead to the rejection of my application or
                disciplinary action.
            </p>
            <table width="100%" style="margin-top: 30px;">
                <tr>
                    <td width="50%" style="border: none;">
                        <div style="border-top: 1px solid #333; width: 200px; margin-bottom: 5px;"></div>
                        <div style="font-size: 8px; font-weight: bold;">Signature of the Applicant</div>
                        <div style="font-size: 8px; color: #64748b;">Date: ____________________</div>
                    </td>
                    <td width="50%" style="border: none; text-align: right;">
                        <div style="border-top: 1px solid #333; width: 200px; margin-left: auto; margin-bottom: 5px;">
                        </div>
                        <div style="font-size: 8px; font-weight: bold;">Signature of Head of Institution</div>
                        <div style="font-size: 8px; color: #64748b;">(Official Seal)</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <htmlpagefooter name="page-footer">
        <div class="footer-content">
            Generated on {{ now()->format('Y-m-d H:i') }} | Page {PAGENO} of {nbpg} | System generated document
        </div>
    </htmlpagefooter>
</body>

</html>
