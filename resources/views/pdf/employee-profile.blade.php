<!DOCTYPE html>
<html lang="si">

<head>
    <meta charset="UTF-8">
    <title>Employee Profile</title>

    <style>
        @page {
            header: page-header;
            footer: page-footer;
            margin: 20mm 15mm 15mm 15mm;
        }

        @page :first {
            header: page-header;
            footer: page-footer;
        }

        body {
            font-family: dejavusans, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* HEADER */
        .header-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #075f75;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 15px;
        }

        /* PROFILE SECTION */
        .profile-container {
            display: block;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #075f75;
        }

        .employee-name {
            font-size: 18px;
            color: #075f75;
            font-weight: bold;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .employee-position {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .profile-table {
            width: 100%;
            margin: 10px 0;
        }

        .profile-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .profile-table strong {
            color: #075f75;
        }

        /* QR CODE */
        .qr-container {
            text-align: center;
            padding: 10px;
            border-radius: 4px;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .qr-id {
            font-size: 9px;
            color: #666;
            margin-top: 5px;
            font-family: monospace;
        }

        /* SECTION STYLES */
        .section {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            background: linear-gradient(to right, #075f75, #0c8da5);
            color: white;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* TABLE STYLES */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .data-table th {
            background: #f0f8fa;
            color: #075f75;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            border: 1px solid #ddd;
            width: 25%;
        }

        .data-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .data-table tr:hover {
            background-color: #f5f5f5;
        }

        /* TWO COLUMN LAYOUT */
        .two-column {
            display: block;
        }

        .two-column table {
            width: 100%;
        }

        .two-column th {
            width: 25%;
        }

        /* FOOTER STYLES */
        .footer-content {
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }

        .footer-table {
            width: 100%;
        }

        .footer-table td {
            padding: 2px 0;
        }

        /* UTILITY CLASSES */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .field-label {
            color: #075f75;
            font-weight: 600;
            display: inline-block;
            min-width: 120px;
        }

        /* RESPONSIVE FOR PDF */
        @media print {
            .page-break {
                page-break-before: always;
            }
            
            .keep-together {
                page-break-inside: avoid;
            }
            
            .no-break {
                page-break-inside: avoid;
            }
        }

        /* INFO BOX */
        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #075f75;
            padding: 10px 12px;
            margin: 10px 0;
            border-radius: 0 4px 4px 0;
            font-size: 11px;
        }

        .info-box p {
            margin: 5px 0;
        }

        /* EMPLOYEE ID STYLE */
        .employee-id {
            font-family: monospace;
            font-size: 12px;
            color: #075f75;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 3px 8px;
            border-radius: 3px;
            display: inline-block;
        }

        /* DATE STYLES */
        .date-value {
            font-family: monospace;
            color: #d35400;
        }
    </style>
</head>

<body>

    <!-- ================= HEADER ================= -->
    <htmlpageheader name="page-header">
        <div class="header-title">
            OFFICIAL EMPLOYEE PROFILE - HUMAN RESOURCE MANAGEMENT SYSTEM
        </div>
    </htmlpageheader>

    <!-- ================= PROFILE SECTION ================= -->
    <div class="profile-container">
        <table width="100%">
            <tr>
                <!-- EMPLOYEE DETAILS -->
                <td width="80%" valign="top">
                    <div class="employee-name">
                        {{ $people->title->title_name }} {{ $people->name_with_initials }}
                    </div>
                    
                    <div class="employee-position">
                        {{ $people->currentAppointment->position->position_name ?? 'Position not assigned' }}
                    </div>

                    <table class="profile-table">
                        <tr>
                            <td width="35%"><strong>Employee ID</strong></td>
                            <td width="65%">
                                <span class="employee-id">{{ $people->people_id ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Working Place</strong></td>
                            <td>{{ $people->currentAppointment->workplace->office_name ?? 'Not assigned' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Designation</strong></td>
                            <td>{{ $people->currentAppointment->position->position_name ?? 'Not assigned' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Status</strong></td>
                            <td>
                                <span class="badge" style="background: #28a745; color: white; padding: 2px 8px; border-radius: 3px; font-size: 10px;">
                                    Active
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- QR CODE -->
                <td width="20%" valign="top" align="center">
                    <div class="qr-container" >
                        <img src="{{ $qrCode }}" class="qr-code" />
                        <div style="font-size: 8px; color: #888; margin-top: 3px;">
                            Scan to verify
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div></div>

    <!-- ================= PERSONAL INFORMATION ================= -->
    <div class="section keep-together">
        <div class="section-title">Personal Information</div>
        
        <table class="data-table">
            <tr>
                <th>Full Name</th>
                <td colspan="3"><strong>{{ $people->full_name }}</strong></td>
            </tr>
            <tr>
                <th>NIC Number</th>
                <td>{{ $people->nic ?? 'N/A' }}</td>
                <th>Date of Birth</th>
                <td class="date-value">{{ $people->date_of_birth ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ $people->gender->gender_name ?? 'N/A' }}</td>
                <th>Religion</th>
                <td>{{ $people->religion->religion_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Ethnicity</th>
                <td>{{ $people->ethnicity->ethnicity_name ?? 'N/A' }}</td>
                <th>Marital Status</th>
                <td>{{ $people->civilStatus->civil_status_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Blood Group</th>
                <td>{{ $people->bloodGroup->blood_group ?? 'N/A' }}</td>
                <th>Age</th>
                <td><strong>{{ \Carbon\Carbon::parse($people->date_of_birth)->age ?? 'N/A' }} years</strong></td>
            </tr>
        </table>
    </div>

    <!-- ================= CONTACT INFORMATION ================= -->
    <div class="section keep-together">
        <div class="section-title">Contact Information</div>
        
        <table class="data-table">
            <tr>
                <th>Personal Email</th>
                <td colspan="3">
                    {{ $people->email ?? 'Not provided' }}
                    @if($people->email)
                        <span style="color: #888; font-size: 9px; margin-left: 10px;">
                            (Personal)
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Personal Phone</th>
                <td>{{ $people->phone ?? 'Not provided' }}</td>
                <th>Work Phone</th>
                <td>{{ $people->work_phone ?? 'Not provided' }}</td>
            </tr>
            <tr>
                <th>Permanent Address</th>
                <td colspan="3">
                    {{ $people->address_line1 ?? '' }}
                    {{ $people->address_line2 ? ', ' . $people->address_line2 : '' }}
                    {{ $people->address_line3 ? ', ' . $people->address_line3 : '' }}
                    {{ $people->postal_code ? ' ' . $people->postal_code : '' }}
                    {{ !$people->address_line1 && !$people->address_line2 && !$people->address_line3 ? 'Not provided' : '' }}
                </td>
            </tr>
            <tr>
                <th>Temporary Address</th>
                <td colspan="3">
                    @if($people->t_address_line1 || $people->t_address_line2 || $people->t_address_line3)
                        {{ $people->t_address_line1 ?? '' }}
                        {{ $people->t_address_line2 ? ', ' . $people->t_address_line2 : '' }}
                        {{ $people->t_address_line3 ? ', ' . $people->t_address_line3 : '' }}
                        {{ $people->t_postal_code ? ' ' . $people->t_postal_code : '' }}
                    @else
                        <span style="color: #888;">Same as permanent address</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= EMPLOYMENT INFORMATION ================= -->
    <div class="section keep-together">
        <div class="section-title">Employment Information</div>

        <!-- Initial Appointment -->
        <div class="info-box mb-10">
            <strong>Initial Appointment Details:</strong>
            <table width="100%" style="margin-top: 5px;">
                <tr>
                    <td width="25%" style="border: none; padding: 2px 0;"><strong>Service:</strong></td>
                    <td style="border: none; padding: 2px 0;">{{ $people->appointment->service->service_name ?? 'N/A' }}</td>
                    <td width="25%" style="border: none; padding: 2px 0;"><strong>Rank:</strong></td>
                    <td style="border: none; padding: 2px 0;">{{ $people->appointment->rank->rank_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 2px 0;"><strong>Appointment Date:</strong></td>
                    <td style="border: none; padding: 2px 0;" class="date-value">
                        {{ $people->appointment->first_appointment_date->format('Y-m-d') ?? 'N/A' }}
                    </td>
                    <td style="border: none; padding: 2px 0;"><strong>Retirement Date:</strong></td>
                    <td style="border: none; padding: 2px 0;" class="date-value">
                        {{ $people->appointment->retirement_date->format('Y-m-d') ?? 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Current Appointment -->
        <table class="data-table">
            <tr>
                <th>Current Service</th>
                <td>{{ $people->currentAppointment->service->service_name ?? 'N/A' }}</td>
                <th>Current Rank</th>
                <td>{{ $people->currentAppointment->rank->rank_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Current Appointment Date</th>
                <td class="date-value">{{ $people->currentAppointment->appoint_date->format('Y-m-d') ?? 'N/A' }}</td>
                <th>Current Position</th>
                <td><strong>{{ $people->currentAppointment->position->position_name ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <th>Working Place</th>
                <td colspan="3">
                    <strong>{{ $people->currentAppointment->workplace->office_name ?? 'N/A' }}</strong>
                    @if($people->currentAppointment->workplace->address)
                        <div style="color: #666; font-size: 10px; margin-top: 3px;">
                            {{ $people->currentAppointment->workplace->address }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= FOOTER ================= -->
    <htmlpagefooter name="page-footer">
        <div class="footer-content">
            <table class="footer-table">
                <tr>
                    <td align="left" width="33%">
                        <strong>HRMS Document</strong> | Employee Profile
                    </td>
                    <td align="center" width="34%">
                        Page <strong>{PAGENO}</strong> of <strong>{nbpg}</strong>
                    </td>
                    <td align="right" width="33%">
                        Generated: {{ now()->format('Y-m-d H:i') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center" style="font-size: 8px; color: #999; padding-top: 3px;">
                        CONFIDENTIAL - For Official Use Only
                    </td>
                </tr>
            </table>
        </div>
    </htmlpagefooter>

</body>

</html>