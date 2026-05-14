<!DOCTYPE html>
<html lang="si">

<head>
    <meta charset="UTF-8">
    <title>Institution Profile - {{ $institution->name }}</title>

    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 13px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #075f75;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #075f75;
        }

        .header p {
            margin: 4px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f3f4f6;
            color: #374151;
            padding: 8px 12px;
            font-size: 12px;
            border: 1px solid #e5e7eb;
            text-align: left;
            width: 35%;
            font-weight: bold;
        }

        td {
            padding: 8px 12px;
            font-size: 12px;
            border: 1px solid #e5e7eb;
            width: 65%;
        }

        .section-title {
            background-color: #075f75;
            color: #ffffff;
            font-size: 14px;
            padding: 6px 12px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            border-radius: 4px;
        }

        @page {
            header: page-header;
            footer: page-footer;
        }
    </style>
</head>

<body>

    <htmlpageheader name="page-header">
        <div style="text-align:right; font-size:10px; color: #777;">
            Institution Basic Profile
        </div>
    </htmlpageheader>

    <div class="header">
        <h1>{{ $institution->name }}</h1>
        <p><strong>Census No:</strong> {{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }} | <strong>Status:</strong> {{ $institution->active_status ? 'Active' : 'Inactive' }}</p>
    </div>

    <!-- Basic Information -->
    <div class="section-title">General Information</div>
    <table>
        <tbody>
            <tr>
                <th>Registration Type</th>
                <td>{{ $institution->institutionType->institution_types_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Grade Span</th>
                <td>{{ $institution->gradeSpan->grade_span_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Gender Type</th>
                <td>{{ $institution->typeByGender->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Established Year</th>
                <td>{{ $institution->established_year ?? 'Not Recorded' }}</td>
            </tr>
            <tr>
                <th>Language Medium</th>
                <td>{{ $institution->institutionLanguages->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Mission</th>
                <td>{{ $institution->mission ?? 'Not Available' }}</td>
            </tr>
            <tr>
                <th>Vision</th>
                <td>{{ $institution->vision ?? 'Not Available' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Contact & Address -->
    <div class="section-title">Contact & Location Details</div>
    <table>
        <tbody>
            <tr>
                <th>Address</th>
                <td>{{ $institution->address ?? 'Not Recorded' }}</td>
            </tr>
            <tr>
                <th>Province</th>
                <td>{{ $institution->district?->province?->province_name ?? 'Not Recorded' }}</td>
            </tr>
            <tr>
                <th>District</th>
                <td>{{ $institution->district?->district_name ?? 'Not Recorded' }}</td>
            </tr>
            <tr>
                <th>Telephone</th>
                <td>{{ $institution->phone ?? 'Not Available' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $institution->email ?? 'Not Available' }}</td>
            </tr>
            <tr>
                <th>Postal Code</th>
                <td>{{ $institution->postal_code ?? 'Not Available' }}</td>
            </tr>
            <tr>
                <th>Coordinates (Lat / Lng)</th>
                <td>{{ $institution->latitude ?? 'N/A' }} / {{ $institution->longitude ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Administrative Hierarchy -->
    <div class="section-title">Administrative Structure</div>
    <table>
        <tbody>
            <tr>
                <th>Provincial Education Office (PEO)</th>
                <td>{{ $institution->zonalEducationOffice?->provincialEducationOffice?->name ?? 'Not Assigned' }}</td>
            </tr>
            <tr>
                <th>Zonal Education Office (ZEO)</th>
                <td>{{ $institution->zonalEducationOffice?->name ?? 'Not Assigned' }}</td>
            </tr>
            <tr>
                <th>Divisional Education Office (DEO)</th>
                <td>{{ $institution->divisionalEducationOffice?->name ?? 'Not Assigned' }}</td>
            </tr>
            <tr>
                <th>Divisional Secretariat</th>
                <td>{{ $institution->divisionalSecretariatOffice?->name ?? 'Not Recorded' }}</td>
            </tr>
            <tr>
                <th>Grama Niladhari Division</th>
                <td>{{ $institution->gnDivision?->name ?? 'Not Recorded' }}</td>
            </tr>
            <tr>
                <th>Police Station</th>
                <td>{{ $institution->policeStation?->name ?? 'Not Recorded' }}</td>
            </tr>
            <tr>
                <th>MOH Area</th>
                <td>{{ $institution->mohArea?->name ?? 'Not Recorded' }}</td>
            </tr>
        </tbody>
    </table>

    <htmlpagefooter name="page-footer">
        <div style="width:100%; border-top:1px solid #ccc; padding-top:6px; font-size:10px; color:#555; display:table;">
            <div style="display:table-cell;text-align:left;">
                Generated by: {{ $userNic }}
            </div>
            <div style="display:table-cell;text-align:center;">
                Page {PAGENO} of {nbpg}
            </div>
            <div style="display:table-cell;text-align:right;">
                Date: {{ now()->format('Y-m-d H:i') }}
            </div>
        </div>
    </htmlpagefooter>

</body>
</html>
