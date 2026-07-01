<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DMS Approved Cadre</title>
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
            margin-bottom: 20px;
            border: none;
        }

        .summary-table td {
            border: none;
            padding: 0;
        }

        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 11px;
            text-align: center;
        }

        .summary-label {
            font-size: 8px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 0.05em;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .total-summary-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .total-summary-box .summary-label {
            color: #1d4ed8;
        }

        .total-summary-box .summary-value {
            color: #1e3a8a;
        }

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
            padding: 9px 7px;
            border-bottom: 2px solid #e5e7eb;
            text-align: center;
        }

        th.subject-column {
            text-align: left;
        }

        td {
            padding: 7px;
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
            padding: 6px 7px;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .subject-name {
            font-weight: 700;
            font-size: 11px;
            color: #111827;
        }

        .subject-id {
            margin-top: 2px;
            font-size: 8px;
            color: #9ca3af;
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
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

        tfoot td {
            background-color: #111827;
            color: #ffffff;
            font-weight: 900;
            padding: 10px 7px;
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
            DMS APPROVED CADRE - {{ $workplace->name ?? 'Institution' }}
        </div>
    </htmlpageheader>

    <div class="header-info">
        <h2>{{ __('DMS Approved Cadre') }}</h2>
        <p>
            <strong>Institution:</strong> {{ $workplace->name ?? 'N/A' }} |
            <strong>Census No:</strong> {{ $workplace->census_no ?? 'N/A' }} |
            <strong>Workplace ID:</strong> {{ $workplace->workplace_id ?? 'N/A' }}
        </p>
        <p>
            <strong>Circular:</strong> {{ $circular->circular_no ?? 'N/A' }} |
            <strong>Issued Date:</strong> {{ $circular->issued_date ?? 'N/A' }}
        </p>
    </div>

    <table class="summary-table">
        <tr>
            @foreach ($mediums as $medium)
                <td style="width: {{ 80 / max($mediums->count(), 1) }}%;">
                    <div class="summary-box">
                        <div class="summary-label">{{ $medium->name }}</div>
                        <div class="summary-value">{{ number_format($mediumSums[$medium->medium_id] ?? 0) }}</div>
                    </div>
                </td>
                <td style="width: 1%;"></td>
            @endforeach
            <td style="width: 20%;">
                <div class="summary-box total-summary-box">
                    <div class="summary-label">Total Cadre</div>
                    <div class="summary-value">{{ number_format($grandTotal) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="subject-column" width="34%">{{ __('Subject Details') }}</th>
                @foreach ($mediums as $medium)
                    <th>{{ $medium->name }}</th>
                @endforeach
                <th width="12%">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groupedRows as $typeId => $rows)
                @php $currentType = $typeLabels[$typeId] ?? ['label' => 'General']; @endphp
                <tr class="type-row">
                    <td colspan="{{ $mediums->count() + 2 }}">{{ $currentType['label'] }}</td>
                </tr>

                @foreach ($rows as $row)
                    <tr>
                        <td>
                            <div class="subject-name">{{ $row['subject']->name_en ?? $row['subject']->subject_name ?? 'N/A' }}</div>
                            <div class="subject-id">ID: {{ $row['subject']->subject_id ?? 'N/A' }}</div>
                        </td>
                        @foreach ($mediums as $medium)
                            <td class="text-center font-bold">
                                {{ number_format($row['medium_totals'][$medium->medium_id] ?? 0) }}
                            </td>
                        @endforeach
                        <td class="text-center font-bold">{{ number_format($row['total']) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr class="empty-row">
                    <td colspan="{{ $mediums->count() + 2 }}">{{ __('No data available') }}</td>
                </tr>
            @endforelse
        </tbody>
        @if ($grandTotal > 0)
            <tfoot>
                <tr>
                    <td class="footer-label">{{ __('Grand Total') }}</td>
                    @foreach ($mediums as $medium)
                        <td class="text-center">{{ number_format($mediumSums[$medium->medium_id] ?? 0) }}</td>
                    @endforeach
                    <td class="text-center">{{ number_format($grandTotal) }}</td>
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
