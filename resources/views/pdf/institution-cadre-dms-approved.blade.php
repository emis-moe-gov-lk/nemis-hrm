<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Institution Cadre Summary</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
            margin-top: 25mm;
            margin-bottom: 20mm;
        }

        body {
            font-family: 'dejavusans', sans-serif;
            color: #111827;
            line-height: 1.45;
        }

        .header-info {
            margin-bottom: 18px;
        }

        .header-info h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header-info p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #4b5563;
        }

        .summary-table {
            margin-bottom: 22px;
            border: none;
        }

        .summary-table td {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: center;
        }

        .summary-table .summary-gap {
            background-color: #ffffff;
            border: none;
            padding: 0;
        }

        .summary-label {
            font-size: 8px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 19px;
            font-weight: 900;
            color: #0f172a;
        }

        .status-balanced { color: #059669; }
        .status-excess { color: #2563eb; }
        .status-deficit { color: #dc2626; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th {
            background-color: #f9fafb;
            color: #6b7280;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 9px 8px;
            border-bottom: 2px solid #e5e7eb;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .type-row td {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 800;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 6px 8px;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .subject-name {
            font-size: 11px;
            font-weight: 700;
            color: #111827;
        }

        .medium-name {
            margin-top: 2px;
            font-size: 8px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: 700;
        }

        .empty-row td {
            padding: 24px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
        }

        .teacher-list-heading td {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: 0.08em;
            padding: 7px 8px 4px;
            text-transform: uppercase;
        }

        .teacher-list-header td {
            background-color: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 7px;
            font-weight: 800;
            padding: 5px 8px;
            text-transform: uppercase;
        }

        .teacher-row td {
            background-color: #ffffff;
            color: #334155;
            font-size: 8px;
            padding: 5px 8px;
        }

        .teacher-number {
            color: #64748b;
            font-weight: 700;
        }

        .teacher-name {
            color: #0f172a;
            font-weight: 700;
        }

        .teacher-meta {
            color: #0f172a;
            font-weight: 700;
        }

        .teacher-excess td {
            background-color: #fff1f2;
            color: #991b1b;
        }

        .excess-label {
            color: #dc2626;
            font-size: 7px;
            font-weight: 900;
            text-transform: uppercase;
        }

        tfoot td {
            background-color: #111827;
            color: #ffffff;
            font-weight: 900;
            padding: 11px 8px;
            font-size: 12px;
        }

        tfoot .footer-label {
            color: #d1d5db;
            font-size: 9px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        #page-header {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }

        #page-footer table {
            width: 100%;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
            font-size: 9px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <htmlpageheader name="page-header">
        <div id="page-header">
            INSTITUTION CADRE SUMMARY - {{ $institution->name ?? 'Institution' }}
        </div>
    </htmlpageheader>

    <div class="header-info">
        <h2>{{ __('Institution Cadre Summary') }}</h2>
        <p>
            <strong>Institution:</strong> {{ $institution->name ?? 'N/A' }} |
            <strong>Census No:</strong> {{ $institution->census_no ?? 'N/A' }} |
            <strong>Workplace ID:</strong> {{ $institution->workplace_id ?? 'N/A' }}
        </p>
        <p>
            <strong>Circular:</strong> {{ $circular->circular_no ?? 'N/A' }} |
            <strong>Issued Date:</strong> {{ $circular->issued_date ?? 'N/A' }}
        </p>
    </div>

    <table class="summary-table">
        <tr>
            <td style="width: 32%;">
                <div class="summary-label">Approved Cadre</div>
                <div class="summary-value">{{ number_format($grandApproved) }}</div>
            </td>
            <td class="summary-gap" style="width: 2%;"></td>
            <td style="width: 32%;">
                <div class="summary-label">Filled Staff</div>
                <div class="summary-value">{{ number_format($grandFilled) }}</div>
            </td>
            <td class="summary-gap" style="width: 2%;"></td>
            <td style="width: 32%;">
                <div class="summary-label">Difference</div>
                <div class="summary-value {{ $grandDiff < 0 ? 'status-deficit' : ($grandDiff > 0 ? 'status-excess' : 'status-balanced') }}">
                    {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="42%">{{ __('Subject / Medium') }}</th>
                <th width="13%" class="text-right">{{ __('Approved') }}</th>
                <th width="13%" class="text-right">{{ __('Filled') }}</th>
                <th width="13%" class="text-right">{{ __('Difference') }}</th>
                <th width="19%" class="text-center">{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groupedRows as $typeId => $items)
                @php $currentType = $typeLabels[$typeId] ?? ['label' => 'Other']; @endphp
                <tr class="type-row">
                    <td colspan="5">{{ $currentType['label'] }}</td>
                </tr>

                @foreach ($items as $row)
                    @php
                        $teachers = $row['teachers'] ?? collect();
                        $excessCount = $row['status'] === 'Excess' ? abs($row['diff']) : 0;
                    @endphp
                    <tr>
                        <td>
                            <div class="subject-name">{{ $row['subject_name'] }}</div>
                            <div class="medium-name">{{ $row['medium_name'] }}</div>
                        </td>
                        <td class="text-right font-bold">{{ number_format($row['approved_posts']) }}</td>
                        <td class="text-right font-bold">{{ number_format($row['filled_posts']) }}</td>
                        <td class="text-right font-bold {{ $row['diff'] < 0 ? 'status-deficit' : ($row['diff'] > 0 ? 'status-excess' : '') }}">
                            {{ $row['diff'] > 0 ? '+' . number_format($row['diff']) : number_format($row['diff']) }}
                        </td>
                        <td class="text-center">
                            <span class="font-bold {{ $row['status'] === 'Balanced' ? 'status-balanced' : ($row['status'] === 'Excess' ? 'status-excess' : 'status-deficit') }}">
                                {{ strtoupper($row['status']) }}
                            </span>
                        </td>
                    </tr>

                    @if ($teachers->isNotEmpty())
                        <tr class="teacher-list-heading">
                            <td colspan="5">{{ __('Teacher List') }} - {{ $row['subject_name'] }} / {{ $row['medium_name'] }}</td>
                        </tr>
                        <tr class="teacher-list-header">
                            <td>{{ __('# / Name') }}</td>
                            <td>{{ __('NIC') }}</td>
                            <td class="text-right">{{ __('Appointment Date') }}</td>
                            <td class="text-right">{{ __('Service Years') }}</td>
                            <td class="text-center">{{ __('Remark') }}</td>
                        </tr>

                        @foreach ($teachers as $teacherIndex => $teacher)
                            @php
                                $isExcess = $row['status'] === 'Excess' && $teacherIndex < $excessCount;
                                $appointment = $teacher->currentAppointment;
                                $appointDate = $appointment?->appoint_date;
                            @endphp
                            <tr class="teacher-row {{ $isExcess ? 'teacher-excess' : '' }}">
                                <td>
                                    <span class="teacher-number">{{ $teacherIndex + 1 }}.</span>
                                    <span class="teacher-name">{{ $teacher->employee?->name_with_initials ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $teacher->employee?->nic ?? 'N/A' }}</td>
                                <td class="text-right teacher-meta">
                                    {{ $appointDate ? $appointDate->format('Y-m-d') : 'N/A' }}
                                </td>
                                <td class="text-right teacher-meta">{{ $appointment?->service_years ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if ($isExcess)
                                        <span class="excess-label">{{ __('Excess') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            @empty
                <tr class="empty-row">
                    <td colspan="5">{{ __('No cadre data available.') }}</td>
                </tr>
            @endforelse
        </tbody>
        @if ($grandApproved || $grandFilled || $grandDiff)
            <tfoot>
                <tr>
                    <td class="footer-label">{{ __('Grand Total Summary') }}</td>
                    <td class="text-right">{{ number_format($grandApproved) }}</td>
                    <td class="text-right">{{ number_format($grandFilled) }}</td>
                    <td class="text-right">{{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}</td>
                    <td class="text-center">{{ __('All Services') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <htmlpagefooter name="page-footer">
        <div id="page-footer">
            <table>
                <tr>
                    <td style="text-align: left; border: none;">Generated by: {{ $userNic }}</td>
                    <td style="text-align: center; border: none;">Page {PAGENO} of {nbpg}</td>
                    <td style="text-align: right; border: none;">{{ now()->format('Y-m-d H:i') }}</td>
                </tr>
            </table>
        </div>
    </htmlpagefooter>
</body>
</html>
