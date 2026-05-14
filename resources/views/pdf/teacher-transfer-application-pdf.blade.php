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
            font-family: dejavusans, sans-serif;
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
                <td colspan="3">{{ $application->policy->title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Submission Date</th>
                <td>{{ $application->created_at->format('F d, Y') }}</td>
                <th>Status</th>
                <td>
                    <span class="status-badge status-{{ $application->status }}">
                        {{ ucfirst($application->status) }}
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
                <td colspan="3">{{ ($application->employee->title->title_name ?? '') . ' ' . $application->employee->full_name }}</td>
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
                    {{ $application->currentWorkplace->office_name ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <th>Join Date (Current)</th>
                <td>
                    {{ $application->current_workplace_join_date ? $application->current_workplace_join_date->format('M d, Y') : 'N/A' }}
                    <div style="font-size: 8px; color: #6366f1; margin-top: 2px;">{{ $application->current_workplace_service_years }}</div>
                </td>
                <th>First Appointment</th>
                <td>
                    {{ $application->first_appointment_date ? $application->first_appointment_date->format('M d, Y') : 'N/A' }}
                    <div style="font-size: 8px; color: #6366f1; margin-top: 2px;">{{ $application->total_service_years }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Transfer Request Details</div>
        <table class="data-table">
            <tr>
                <th>Target Province</th>
                <td>{{ $application->targetProvince->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Reason Category</th>
                <td>{{ $application->reason->title ?? 'N/A' }}</td>
            </tr>
            @if($application->transfer_reason)
            <tr>
                <th>Detailed Reason</th>
                <td colspan="3" style="font-style: italic; color: #475569;">
                    "{{ $application->transfer_reason }}"
                </td>
            </tr>
            @endif
        </table>
    </div>

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
                @foreach($application->preferences as $pref)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $pref->preference_order }}</td>
                    <td>{{ $pref->zonalOffice->office_name ?? 'N/A' }}</td>
                    <td>
                        <strong>{{ $pref->institution->office_name ?? 'N/A' }}</strong>
                        <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">{{ __('Census No') }}: {{ str_pad($pref->institution->office()->census_no ?? '0', 5, '0', STR_PAD_LEFT) }}</div>
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
                I hereby declare that the information provided in this application is true and accurate to the best of my knowledge. I understand that any false information may lead to the rejection of my application or disciplinary action.
            </p>
            <table width="100%" style="margin-top: 30px;">
                <tr>
                    <td width="50%" style="border: none;">
                        <div style="border-top: 1px solid #333; width: 200px; margin-bottom: 5px;"></div>
                        <div style="font-size: 8px; font-weight: bold;">Signature of the Applicant</div>
                        <div style="font-size: 8px; color: #64748b;">Date: ____________________</div>
                    </td>
                    <td width="50%" style="border: none; text-align: right;">
                        <div style="border-top: 1px solid #333; width: 200px; margin-left: auto; margin-bottom: 5px;"></div>
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