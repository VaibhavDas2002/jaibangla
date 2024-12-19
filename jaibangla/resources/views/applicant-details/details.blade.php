<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Details</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Page Setup */
        body {
            margin: 1.5cm;
            font-size: 10px;
            color: #000;
        }

        h1,
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table td,
        table th {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        /* Activity Table Header Styling */
        #activity thead {
            background-color: #bbbbbb;
            /* Gray background */
            text-align: center;
            /* Centered text */
            /* padding: 10px; Add padding for better spacing */
            font-weight: bold;
            color: black;
        }

        /* Notice Styling */
        #notice {
            color: red;
            font-weight: bold;
            margin: 20px 0;
        }

        #notice b {
            color: black;
        }

        .bold-black {
            font-weight: bold;
            color: black;
        }

        .red {
            color: red;
        }

        /* Table Header */
        #activity {
            background-color: #bbbbbb;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>

<body>
    <h1><u>Applicant Details</u></h1>

    <table>
        <tr>
            <td><b>Application ID</b></td>
            <td>{{ $applicant_details['app_id'] }}</td>
        </tr>
        <tr>
            <td><b>Applicant Name</b></td>
            <td>{{ $applicant_details['app_name'] }}</td>
        </tr>
        <tr>
            <td><b>Applicant Block</b></td>
            <td>{{ $applicant_details['app_block'] }}</td>
        </tr>
        <tr>
            <td><b>Applicant District</b></td>
            <td>{{ $applicant_details['app_district'] }}</td>
        </tr>
    </table>

    <p>Jai Bangla Portal was able to capture firewall's private IP (172.25.159.242) of the West Bengal State Data
        Centre.</p>
    <p>All operations in the Jai Bangla Portal with respect to the applicant are listed below:</p>

    <p id="notice"><b>N.B.:</b> <span class="red">This beneficiary was imported to Jai Bangla portal through Excel as
            approved legacy data provided by the concerned department.</span></p>

    <table id="Activity">
        <thead>
            <tr>
                <th>Activity</th>
                <th>DateTime (YYYY-MM-DD H:M:S)</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applicant_activity as $act)
                <tr>
                    <td>{{ $act['activity'] }}</td>
                    <td>{{ $act['datetime'] }}</td>
                    <td>{!! $act['remarks'] !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>