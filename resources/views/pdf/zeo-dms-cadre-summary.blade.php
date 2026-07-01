<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ZEO Cadre Summary</title>
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
            letter-spacing: 0.05em;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .summary-value {
            color: #0f172a;
            font-size: 19px;
            font-weight: 900;
        }

        .filters {
            margin-bottom: 16px;
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            color: #6b7280;
            font-weight: 800;
            letter-spacing: 0.04em;
            padding: 9px 8px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #f3f4f6;
            padding: 8px;
            vertical-align: middle;
        }

        .type-row td {
            background-color: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
            color: #475569;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.1em;
            padding: 6px 8px;
            text-transform: uppercase;
        }

        .subject-name {
            color: #111827;
            font-size: 11px;
            font-weight: 700;
        }

        .medium-name {
            color: #94a3b8;
            font-size: 8px;
            font-weight: 700;
            margin-top: 2px;
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

        .status-balanced { color: #059669; }
        .status-excess { color: #2563eb; }
        .status-deficit { color: #dc2626; }

        .empty-row td {
            color: #6b7280;
            font-style: italic;
            padding: 24px;
            text-align: center;
        }

        tfoot td {
            background-color: #111827;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            padding: 11px 8px;
        }

        tfoot .footer-label {
            color: #d1d5db;
            font-size: 9px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        #page-header {
            border-bottom: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 9px;
            padding-bottom: 5px;
            text-align: center;
        }

        #page-footer table {
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 9px;
            padding-top: 6px;
            width: 100%;
        }
    </style>
</head>
<body>
    <htmlpageheader name="page-header">
        <div id="page-header">
            ZEO CADRE SUMMARY - {{ $office->name ?? $office->short_name ?? 'ZEO' }}
        </div>
    </htmlpageheader>

    <div class="header-info">
        <h2>{{ __('Institution Cadre Summary for ZEO') }}</h2>
        <p>
            <strong>ZEO:</strong> {{ $office->name ?? $office->short_name ?? 'N/A' }} |
            <strong>Workplace ID:</strong> {{ $office->workplace_id ?? 'N/A' }}
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
                <div class="summary-label">Gap</div>
                <div class="summary-value {{ $grandDiff < 0 ? 'status-deficit' : ($grandDiff > 0 ? 'status-excess' : 'status-balanced') }}">
                    {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                </div>
            </td>
        </tr>
    </table>

    <div class="filters">
        @foreach ($filterSummary as $label => $value)
            <strong>{{ $label }}:</strong> {{ $value }}@if (! $loop->last) | @endif
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th width="42%">{{ __('Subject / Medium') }}</th>
                <th width="13%" class="text-right">{{ __('Approved') }}</th>
                <th width="13%" class="text-right">{{ __('Filled') }}</th>
                <th width="13%" class="text-right">{{ __('Gap') }}</th>
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
                    <td class="footer-label">{{ __('Grand Summary') }}</td>
                    <td class="text-right">{{ number_format($grandApproved) }}</td>
                    <td class="text-right">{{ number_format($grandFilled) }}</td>
                    <td class="text-right">{{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}</td>
                    <td class="text-center">{{ __('Finalized') }}</td>
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
