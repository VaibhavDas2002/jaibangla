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
            background-color: #bbbbbb; /* Gray background */
            text-align: center; /* Centered text */
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
            <td><b>Beneficiary ID</b></td>
            <td>{{ $beneficiary_details['ben_id'] }}</td>
        </tr>
        <tr>
            <td><b>Full Name</b></td>
            <td>{{ $beneficiary_details['ben_name'] }}</td>
        </tr>
        <tr>
            <td><b>Beneficiary Block/Sub Division</b></td>
            <td>{{ $beneficiary_details['ben_block'] }}</td>
        </tr>
        <tr>
            <td><b>Beneficiary District</b></td>
            <td>{{ $beneficiary_details['ben_district'] }}</td>
        </tr>
    </table>
    
   
     @if ($ip_found==0)
     <p>In Jai Bangla Portal no IP found.</p>
     @elseif($ip_found==1 )
    <p>Jai Bangla Portal was able to capture firewall's private IP/IPs {{($distinct_ip)}}   of the West Bengal State Data Centre.</p>
    @endif

    <p>All operations in the Jai Bangla Portal with respect to the applicant are listed below:</p>
    
   
    <table id="Activity">
        <thead>
            <tr>
                <th>Activity</th>
                <th>Date & Time (YYYY-MM-DD H:M:S)</th>
                <th>Action By</th>
                <th>Bank Information Change Details</th>
               
            </tr>
        </thead>
        <tbody>
           
            @foreach($applicant_activity as $act)
                <tr>
                    <td>{{$act['activity_name']}}</td>  
                    <td>{{$act['activity_time']}}</td> 
                    <td>{{$act['user_details']}}</td>
                    <td>{{$act['banking_details']}}</td>  
                   
                </tr>
            @endforeach
           
        </tbody>
    </table>
   
</body>

</html>
