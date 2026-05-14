<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>National School Cadre Summary</title>
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
            line-height: 1.5;
        }
        .header-info {
            margin-bottom: 20px;
        }
        .header-info h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: -0.025em;
        }
        .header-info p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th {
            background-color: #f9fafb;
            color: #6b7280;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 8px;
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
            font-weight: 800;
            font-size: 9px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 6px 8px;
        }
        .subject-name {
            font-weight: 700;
            font-size: 12px;
            color: #111827;
        }
        .medium-name {
            font-size: 9px;
            color: #9ca3af;
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
        .status-balanced { color: #059669; }
        .status-excess { color: #2563eb; }
        .status-deficit { color: #dc2626; }
        .status-normal { color: #0f172a; }
        
        tfoot td {
            background-color: #111827;
            color: #ffffff;
            font-weight: 900;
            padding: 12px 8px;
            font-size: 14px;
        }
        tfoot .summary-label {
            font-size: 10px;
            letter-spacing: 0.2em;
            color: #9ca3af;
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
            NATIONAL SCHOOL CADRE SUMMARY – {{ $officeName }}
        </div>
    </htmlpageheader>

    <div class="header-info">
        <h2>{{ __('Institution Cadre Summary') }}</h2>
        <p>
            <strong>Circular:</strong> {{ $circular->circular_no }} ({{ $circular->issued_date }}) | 
            <strong>Authority:</strong> National School
        </p>
    </div>

    {{-- Grand Summary Section at Top --}}
    <table style="margin-bottom: 25px; border: none;">
        <tr>
            <td style="width: 32%; padding: 0; border: none;">
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center;">
                    <div style="font-size: 8px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.05em;">Total Approved</div>
                    <div style="font-size: 18px; font-weight: 900; color: #0f172a;">{{ $grandApproved }}</div>
                </div>
            </td>
            <td style="width: 2%; border: none;"></td>
            <td style="width: 32%; padding: 0; border: none;">
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center;">
                    <div style="font-size: 8px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.05em;">Total Filled</div>
                    <div style="font-size: 18px; font-weight: 900; color: #0f172a;">{{ $grandFilled }}</div>
                </div>
            </td>
            <td style="width: 2%; border: none;"></td>
            <td style="width: 32%; padding: 0; border: none;">
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center;">
                    <div style="font-size: 8px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.05em;">Overall Gap</div>
                    <div style="font-size: 18px; font-weight: 900;" class="{{ $grandDiff < 0 ? 'status-deficit' : ($grandDiff > 0 ? 'status-excess' : 'status-normal') }}">
                        {{ $grandDiff > 0 ? '+' . $grandDiff : $grandDiff }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="45%">Subject / Medium</th>
                <th width="12%" class="text-right">Approved</th>
                <th width="12%" class="text-right">Filled</th>
                <th width="12%" class="text-right">Gap</th>
                <th width="19%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedRows as $typeId => $items)
                @php $currentType = $typeLabels[$typeId] ?? ['label' => 'Other']; @endphp
                <tr class="type-row">
                    <td colspan="5">{{ $currentType['label'] }}</td>
                </tr>
                @foreach($items as $row)
                    <tr>
                        <td>
                            <div class="subject-name">{{ $row['subject_name'] }}</div>
                            <div class="medium-name">{{ $row['medium_name'] }}</div>
                        </td>
                        <td class="text-right font-bold">{{ $row['approved_posts'] }}</td>
                        <td class="text-right font-bold" style="color: #4b5563;">{{ $row['filled_posts'] }}</td>
                        <td class="text-right font-bold {{ $row['diff'] < 0 ? 'status-deficit' : ($row['diff'] > 0 ? 'status-excess' : '') }}">
                            {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                        </td>
                        <td class="text-center">
                            <span class="font-bold {{ $row['status'] == 'Balanced' ? 'status-balanced' : ($row['status'] == 'Excess' ? 'status-excess' : 'status-deficit') }}">
                                {{ strtoupper($row['status']) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
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
