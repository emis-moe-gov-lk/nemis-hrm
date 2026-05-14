<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ \Illuminate\Support\Str::headline($type) }} School List - {{ $board->board_id }}</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
            margin: 22mm 9mm 14mm 9mm;
        }

        body {
            font-family: dejavusans, sans-serif;
            font-size: 9.2px;
            color: #111827;
            line-height: 1.35;
        }

        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }

        .header-subtitle {
            margin-top: 2px;
            font-size: 9px;
            color: #64748b;
        }

        .section {
            margin-bottom: 12px;
        }

        .section-title {
            background-color: #f1f5f9;
            border-left: 4px solid #2563eb;
            color: #0f172a;
            font-size: 10px;
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
            width: 20%;
            text-align: left;
            padding: 5px 6px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 8px;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
        }

        .summary-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: center;
            background-color: #f8fafc;
        }

        .summary-number {
            display: block;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-label {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 8px;
            text-transform: uppercase;
        }

        .list-table th {
            background-color: #0f172a;
            color: #ffffff;
            padding: 6px 5px;
            font-size: 8.2px;
            text-align: left;
            text-transform: uppercase;
        }

        .list-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 5px;
            vertical-align: top;
        }

        .list-table tbody tr {
            page-break-inside: avoid;
        }

        .text-right {
            text-align: right;
        }

        .muted {
            color: #64748b;
        }

        .school-name {
            display: block;
            color: #0f172a;
            font-size: 8.9px;
            text-transform: uppercase;
        }

        .school-meta {
            margin-top: 3px;
            color: #64748b;
            font-size: 7.6px;
            line-height: 1.35;
        }

        .school-summary {
            margin-top: 5px;
            padding: 4px 5px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            color: #334155;
            font-size: 7.8px;
            line-height: 1.4;
        }

        .medium-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .medium-table th {
            padding: 4px 3px;
            border: 1px solid #cbd5e1;
            background-color: #e2e8f0;
            color: #334155;
            font-size: 7.5px;
            text-align: left;
            text-transform: uppercase;
        }

        .medium-table td {
            padding: 4px 3px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            vertical-align: middle;
        }

        .medium-name {
            font-weight: bold;
            color: #0f172a;
        }

        .number-cell {
            text-align: right;
            white-space: nowrap;
        }

        .balance-total {
            font-size: 15px;
            font-weight: bold;
            line-height: 1;
        }

        .balance-label {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 7px;
            text-transform: uppercase;
        }

        .balance-needed {
            color: #be123c;
        }

        .balance-excess {
            color: #a16207;
        }

        .applicant-note {
            margin-top: 5px;
            padding: 3px 5px;
            border: 1px solid #dbeafe;
            background-color: #eff6ff;
            color: #1d4ed8;
            font-size: 7.3px;
        }
    </style>
</head>

<body>
    <htmlpageheader name="page-header">
        <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 6px;">
            <div class="header-title">{{ \Illuminate\Support\Str::headline($type) }} School List</div>
            <div class="header-subtitle">
                {{ $board->board_name }} | {{ $board->board_id }} | Generated {{ $generatedAt->format('Y-m-d H:i') }}
            </div>
        </div>
    </htmlpageheader>

    <htmlpagefooter name="page-footer">
        <div style="border-top: 1px solid #cbd5e1; padding-top: 5px; font-size: 8px; color: #64748b; text-align: right;">
            Page {PAGENO} of {nbpg}
        </div>
    </htmlpagefooter>

    <div class="section">
        <div class="section-title">Board Information</div>
        <table class="data-table">
            <tr>
                <th>Board</th>
                <td>{{ $board->board_name }}</td>
                <th>Board ID</th>
                <td>{{ $board->board_id }}</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>{{ $board->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <th>Scope</th>
                <td>{{ $board->workplace?->office_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Policy</th>
                <td>{{ $board->policy?->title ?? 'N/A' }}</td>
                <th>Category</th>
                <td>{{ $board->category?->transfer_category_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Subjects</th>
                <td colspan="3">{{ $board->subjects->pluck('name_en')->filter()->join(', ') ?: 'N/A' }}</td>
            </tr>
            <tr>
                <th>DMS Circular</th>
                <td colspan="3">{{ $summary['activeCircular']?->title ?? $summary['activeCircular']?->circular_no ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="summary-table">
            <tr>
                <td>
                    <span class="summary-number">{{ $summary['needed']->count() }}</span>
                    <span class="summary-label">Needed Schools</span>
                </td>
                <td>
                    <span class="summary-number">{{ $summary['needed']->sum('need_count') }}</span>
                    <span class="summary-label">Total Needed Posts</span>
                </td>
                <td>
                    <span class="summary-number">{{ $summary['excess']->count() }}</span>
                    <span class="summary-label">Excess Schools</span>
                </td>
                <td>
                    <span class="summary-number">{{ $summary['excess']->sum('excess_count') }}</span>
                    <span class="summary-label">Total Excess Posts</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ \Illuminate\Support\Str::headline($type) }} Schools</div>

        @if($summary['note'])
            <p class="muted">{{ $summary['note'] }}</p>
        @endif

        @php
            $balanceLabel = $type === 'needed' ? 'Need' : 'Excess';
            $balanceKey = $type === 'needed' ? 'need_count' : 'excess_count';
            $balanceClass = $type === 'needed' ? 'balance-needed' : 'balance-excess';
        @endphp

        <table class="list-table">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 28%;">School</th>
                    <th style="width: 16%;">Zone</th>
                    <th style="width: 42%;">Subject and Medium Breakdown</th>
                    <th class="text-right" style="width: 10%;">Total {{ $balanceLabel }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="school-name">{{ $row['school_name'] }}</span>
                            <div class="school-meta">
                                Census: {{ $row['census_no'] }}<br>
                                Authority: {{ $row['authority'] }}
                            </div>
                            <div class="school-summary">
                                Approved {{ $row['approved_posts'] }} |
                                Filled {{ $row['filled_posts'] }} |
                                In {{ $row['incoming_transfers'] }} |
                                Out {{ $row['outgoing_transfers'] }} |
                                Adjusted {{ $row['adjusted_filled_posts'] }}
                            </div>
                        </td>
                        <td>{{ $row['zone_name'] }}</td>
                        <td>
                            <table class="medium-table">
                                <thead>
                                    <tr>
                                        <th style="width: 22%;">Subject</th>
                                        <th style="width: 18%;">Medium</th>
                                        <th class="number-cell" style="width: 10%;">Approved</th>
                                        <th class="number-cell" style="width: 10%;">Filled</th>
                                        <th class="number-cell" style="width: 8%;">In</th>
                                        <th class="number-cell" style="width: 8%;">Out</th>
                                        <th class="number-cell" style="width: 12%;">Adjusted</th>
                                        <th class="number-cell" style="width: 12%;">{{ $balanceLabel }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($row['medium_rows'] ?? [$row]) as $mediumRow)
                                        <tr>
                                            <td>{{ $mediumRow['subject_name'] }}</td>
                                            <td class="medium-name">{{ $mediumRow['medium_name'] }}</td>
                                            <td class="number-cell">{{ $mediumRow['approved_posts'] }}</td>
                                            <td class="number-cell">{{ $mediumRow['filled_posts'] }}</td>
                                            <td class="number-cell">{{ $mediumRow['incoming_transfers'] }}</td>
                                            <td class="number-cell">{{ $mediumRow['outgoing_transfers'] }}</td>
                                            <td class="number-cell">{{ $mediumRow['adjusted_filled_posts'] }}</td>
                                            <td class="number-cell"><strong class="{{ $balanceClass }}">{{ $mediumRow[$balanceKey] }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="applicant-note">
                                Applications selecting this school: {{ $row['applicant_count'] ?? 0 }}
                            </div>
                        </td>
                        <td class="text-right">
                            <span class="balance-total {{ $balanceClass }}">{{ $row[$balanceKey] }}</span>
                            <span class="balance-label">{{ $balanceLabel }} Posts</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 18px;" class="muted">
                            No {{ $type }} schools found for the selected board subjects and scope.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
