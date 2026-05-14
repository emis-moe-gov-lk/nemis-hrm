<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transfer Board Decision Report - {{ $board->board_id }}</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
            margin: 28mm 12mm 18mm 12mm;
        }

        body {
            font-family: dejavusans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.45;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }

        .header-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .section {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #f1f5f9;
            border-left: 4px solid #2563eb;
            color: #0f172a;
            font-size: 11px;
            font-weight: bold;
            padding: 6px 8px;
            text-transform: uppercase;
        }

        .data-table,
        .list-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            width: 24%;
            text-align: left;
            padding: 5px 7px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 5px 7px;
            border: 1px solid #e2e8f0;
            color: #111827;
        }

        .list-table th {
            background-color: #0f172a;
            color: #ffffff;
            padding: 6px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
        }

        .list-table td {
            border: 1px solid #e2e8f0;
            padding: 6px;
            vertical-align: top;
        }

        .summary-table td {
            border: 1px solid #e2e8f0;
            padding: 9px;
            text-align: center;
            background-color: #f8fafc;
        }

        .summary-number {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-label {
            display: block;
            margin-top: 3px;
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-blue {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .badge-green {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-red {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .badge-gray {
            background-color: #f1f5f9;
            color: #475569;
        }

        .application-card {
            page-break-before: always;
        }

        .application-card.first {
            page-break-before: auto;
        }

        .application-heading {
            background-color: #eef2ff;
            border: 1px solid #c7d2fe;
            padding: 9px 10px;
            margin-bottom: 10px;
        }

        .application-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e1b4b;
        }

        .application-detail-block {
            border-left: 2px solid #c7d2fe;
            margin-left: 10mm;
            padding-left: 4mm;
        }

        .muted {
            color: #64748b;
        }

        .footer-content {
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 9px;
            padding-top: 4px;
            text-align: center;
        }
    </style>
</head>

<body>
    <htmlpageheader name="page-header">
        <table width="100%" style="border-bottom: 2px solid #0f172a; padding-bottom: 8px;">
            <tr>
                <td width="72%" style="border: none;">
                    <div class="header-title">Transfer Board Decision Report</div>
                    <div class="header-subtitle">{{ $board->board_name }} | {{ $board->board_id }}</div>
                </td>
                <td width="28%" align="right" style="border: none; color: #64748b; font-size: 9px;">
                    Generated: {{ $generatedAt->format('Y-m-d H:i') }}<br>
                    Status: {{ strtoupper(str_replace('_', ' ', $board->board_status)) }}
                </td>
            </tr>
        </table>
    </htmlpageheader>

    <htmlpagefooter name="page-footer">
        <div class="footer-content">
            {{ $board->board_id }} - Transfer Board Decision Report - Page {PAGENO} of {nbpg}
        </div>
    </htmlpagefooter>

    <div class="section">
        <div class="section-title">Board Overview</div>
        <table class="data-table">
            <tr>
                <th>Board Name</th>
                <td colspan="3">{{ $board->board_name }}</td>
            </tr>
            <tr>
                <th>Board ID</th>
                <td>{{ $board->board_id }}</td>
                <th>Board Date</th>
                <td>{{ $board->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Office</th>
                <td>{{ $board->workplace?->office_name ?? 'N/A' }}</td>
                <th>Office Level</th>
                <td>{{ $board->officeLevel?->office_level_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Transfer Policy</th>
                <td>{{ $board->policy?->title ?? 'N/A' }}</td>
                <th>Transfer Category</th>
                <td>{{ $board->category?->transfer_category_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Subjects</th>
                <td colspan="3">
                    @forelse($board->subjects as $subject)
                        {{ $subject->name_en }}@if(!$loop->last), @endif
                    @empty
                        N/A
                    @endforelse
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Decision Summary</div>
        <table class="summary-table">
            <tr>
                <td>
                    <span class="summary-number">{{ $summary['total'] }}</span>
                    <span class="summary-label">Matched Applications</span>
                </td>
                <td>
                    <span class="summary-number">{{ $summary['approved'] }}</span>
                    <span class="summary-label">Approved Decisions</span>
                </td>
                <td>
                    <span class="summary-number">{{ $summary['rejected'] }}</span>
                    <span class="summary-label">Rejected Decisions</span>
                </td>
                <td>
                    <span class="summary-number">{{ $summary['pending'] }}</span>
                    <span class="summary-label">Pending Decisions</span>
                </td>
            </tr>
        </table>
    </div>

    @forelse($applications as $application)
        @php
            $decision = $decisions->get($application->transfer_application_id);
            $teacher = $application->teacher;
            $statusClass = match ($decision?->recommendation_status) {
                'approved' => 'badge-green',
                'rejected' => 'badge-red',
                default => 'badge-gray',
            };
            $decisionLabel = $decision?->recommendationList?->decision
                ?? ($decision?->recommendation_status ? ucfirst($decision->recommendation_status) : 'Decision Pending');
        @endphp

        <div class="application-card {{ $loop->first ? 'first' : '' }}">
            <div class="application-heading">
                <table width="100%">
                    <tr>
                        <td width="70%" style="border: none;">
                            <div class="application-title">{{ $loop->iteration }}. {{ $application->transfer_application_id }} - {{ $application->employee?->full_name ?? 'Teacher' }}</div>
                            <div class="muted">Submitted: {{ $application->created_at?->format('Y-m-d') ?? 'N/A' }} | Status: {{ ucfirst($application->status) }}</div>
                        </td>
                        <td width="30%" align="right" style="border: none;">
                            <span class="badge {{ $statusClass }}">{{ $decisionLabel }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="application-detail-block">
                <div class="section">
                    <div class="section-title">Applicant and Teaching Details</div>
                    <table class="data-table">
                        <tr>
                            <th>Full Name</th>
                            <td>{{ $application->employee?->full_name ?? 'N/A' }}</td>
                            <th>NIC / Employee ID</th>
                            <td>{{ $application->employee?->nic ?? 'N/A' }} / {{ $application->employee_id }}</td>
                        </tr>
                        <tr>
                            <th>Current Station</th>
                            <td>{{ $application->currentWorkplace?->office_name ?? 'N/A' }}</td>
                            <th>Target Province</th>
                            <td>{{ $application->targetProvince?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>First Appointment</th>
                            <td>{{ $application->first_appointment_date?->format('Y-m-d') ?? 'N/A' }}<br><span class="muted">{{ $application->total_service_years }}</span></td>
                            <th>Current Station Entry</th>
                            <td>{{ $application->current_workplace_join_date?->format('Y-m-d') ?? 'N/A' }}<br><span class="muted">{{ $application->current_workplace_service_years }}</span></td>
                        </tr>
                        <tr>
                            <th>Main Subject</th>
                            <td>{{ $teacher?->mainSubject?->name_en ?? 'N/A' }}</td>
                            <th>Other Subjects</th>
                            <td>
                                Secondary: {{ $teacher?->secondarySubject?->name_en ?? 'N/A' }}<br>
                                Current Teaching: {{ $teacher?->currentTeachingSubject?->name_en ?? 'N/A' }}<br>
                                Appointment: {{ $teacher?->appointmentSubject?->name_en ?? 'N/A' }}
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">Transfer Board Decision</div>
                    <table class="data-table">
                        <tr>
                            <th>Decision</th>
                            <td><span class="badge {{ $statusClass }}">{{ $decisionLabel }}</span></td>
                            <th>Decision Status</th>
                            <td>{{ $decision?->recommendation_status ? ucfirst($decision->recommendation_status) : 'Pending' }}</td>
                        </tr>
                        <tr>
                            <th>School Selection</th>
                            <td>{{ $decision?->school_selection_type ? ucfirst($decision->school_selection_type) : 'N/A' }}</td>
                            <th>Effective Date</th>
                            <td>{{ $decision?->transfer_effective_date?->format('Y-m-d') ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Selected Zone</th>
                            <td>{{ $decision?->selectedZone?->name ?? 'N/A' }}</td>
                            <th>Selected School</th>
                            <td>{{ $decision?->selectedSchool?->name ?? 'N/A' }}</td>
                        </tr>
                        @if($decision?->rejection_reason)
                            <tr>
                                <th>Rejection Reason</th>
                                <td colspan="3">{{ ucfirst($decision->rejection_reason) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Board Remarks</th>
                            <td colspan="3">{{ $decision?->recommendation_remarks ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="section">
            <div class="section-title">Applications</div>
            <table class="data-table">
                <tr>
                    <td>No transfer applications matched this closed board configuration.</td>
                </tr>
            </table>
        </div>
    @endforelse

    <div class="section">
        <div class="section-title">Board Officers</div>
        <table class="list-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Role</th>
                    <th style="width: 32%;">Name</th>
                    <th style="width: 20%;">Workplace / Union</th>
                    <th style="width: 30%;">Signature</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Chairman</td>
                    <td>{{ $board->chairman?->full_name ?? 'N/A' }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Secretary</td>
                    <td>{{ $board->secretary?->full_name ?? 'N/A' }}</td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach($additionalMembers as $member)
                    <tr>
                        <td>Member</td>
                        <td>{{ $member->person?->full_name ?? 'N/A' }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>

</html>
