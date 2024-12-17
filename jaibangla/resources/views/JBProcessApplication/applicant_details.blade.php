<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiary Details</title>
    <style>
        /* Reset styles */
        body, h2, h3, h4 {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 10px;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #000;
        }

        .logo {
            display: inline-block;
            vertical-align: middle;
        }

        .header-text {
            display: inline-block;
            vertical-align: middle;
            padding-left: 10px;
        }

        .header-text h2 {
            font-size: 12px;
            font-weight: bold;
        }

        .header-text h3, .header-text h4 {
            font-size: 9px;
        }

        .section {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        td {
            padding: 1px;
            /* font-size: 5px; */
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <img src="images/biswo.png" alt="Logo 1" class="logo" height="70" width="50">
            <div class="header-text">
                <h2>Jai Bangla</h2>
                <h3>Beneficiary ID: {{ $data->id }}</h3>
                <h4>Scheme Name: {{$scheme_name}}</h4>
            </div>
        </div>

        <!-- Personal Details Section -->
        <div class="section">
            <div class="section-title">Personal Details</div>
            <table>
                <tr>
                    <td><strong>Name:</strong></td>
                    <td>{{ $data->ben_fname }} {{ $data->ben_mname }} {{ $data->ben_lname }}</td>
                </tr>
                <tr>
                    <td><strong>Gender:</strong></td>
                    <td>{{ $data->gender }}</td>
                </tr>
                <tr>
                    <td><strong>Date of Birth:</strong></td>
                    <td>{{ date('d/m/Y', strtotime($data->dob)) }}</td>
                </tr>
                <tr>
                    <td><strong>Father's Name:</strong></td>
                    <td>{{ $data->father_fname }} {{ $data->father_mname }} {{ $data->father_lname }}</td>
                </tr>
                <tr>
                    <td><strong>Mother's Name:</strong></td>
                    <td>{{ $data->mother_fname }} {{ $data->mother_mname }} {{ $data->mother_lname }}</td>
                </tr>
                <tr>
                    <td><strong>Caste:</strong></td>
                    <td>{{ $data->caste }}</td>
                </tr>
                <tr>
                    <td><strong>Marital Status:</strong></td>
                    <td>{{ $data->marital_status }}</td>
                </tr>
                @if ($scheme_id == 11)
                    <tr>
                        <td><strong>Husband's Name:</strong></td>
                        <td>{{ $row->husband_fname }} {{ $row->husband_mname }} {{ $row->husband_lname }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Spouse Name:</strong></td>
                    <td>{{ $data->spouse_fname }} {{ $data->spouse_mname }} {{ $data->spouse_lname }}</td>
                </tr>
                <tr>
                    <td><strong>Monthly Family Income (Rs.):</strong></td>
                    <td>{{ $data->mothly_income }}</td>
                </tr>
            </table>
        </div>

        <!-- Personal Identification Numbers Section -->
        <div class="section">
            <div class="section-title">Personal Identification Numbers</div>
            <table>
                <tr>
                    <td><strong>Ration Card No.:</strong></td>
                    <td>{{ $data->ration_card_no }}</td>
                </tr>
                @if ($data->aadhar_no != '')
                    <tr>
                        <td><strong>Aadhaar No., if available:</strong></td>
                        <td>{{ $data->aadhar_no }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>EPIC/Voter ID No.:</strong></td>
                    <td>{{ $data->epic_voter_id }}</td>
                </tr>
                <tr>
                    <td><strong>PAN, if available:</strong></td>
                    <td>{{ $data->pan_no }}</td>
                </tr>
            </table>
        </div>

        <!-- Contact Details Section -->
        <div class="section">
            <div class="section-title">Contact Details</div>
            <table>
                <tr>
                    <td><strong>State:</strong></td>
                    <td>West Bengal</td>
                </tr>
                <tr>
                    <td><strong>Assembly Constitution:</strong></td>
                    <td>{{ $data->assembly_name }}</td>
                </tr>
                <tr>
                    <td><strong>District:</strong></td>
                    <td>{{ $district_name }}</td>
                </tr>
                <tr>
                    <td><strong>Block/Municipality/Corp:</strong></td>
                    <td>{{ $block_name }}</td>
                </tr>
                <tr>
                    <td><strong>GP/Ward No.:</strong></td>
                    <td>{{ $gp_name }}</td>
                </tr>
                <tr>
                    <td><strong>Pin Code:</strong></td>
                    <td>{{ $data->pincode }}</td>
                </tr>
                <tr>
                    <td><strong>Mobile Number:</strong></td>
                    <td>{{ $data->mobile_no }}</td>
                </tr>
            </table>
        </div>

        <!-- Bank Details Section -->
        <div class="section">
            <div class="section-title">Bank Details</div>
            <table>
                <tr>
                    <td><strong>Bank Name:</strong></td>
                    <td>{{ $data->bank_name }}</td>
                </tr>
                <tr>
                    <td><strong>Bank Branch Name:</strong></td>
                    <td>{{ $data->branch_name }}</td>
                </tr>
                <tr>
                    <td><strong>Bank Account No.:</strong></td>
                    <td>{{ $data->bank_code }}</td>
                </tr>
                <tr>
                    <td><strong>IFSC Code:</strong></td>
                    <td>{{ $data->bank_ifsc }}</td>
                </tr>
            </table>
        </div>

        <!-- Additional Details Section -->
        @if($data->scheme_id == 17 || $data->scheme_id == 2 || $data->scheme_id == 10)
            <div class="section">
                <div class="section-title">Additional Details</div>
                <table>
                    @if($data->scheme_id == 2)
                        <tr>
                            <td><strong>Type of Disability:</strong></td>
                            <td>{{ $data->type_disability }}</td>
                        </tr>
                        <tr>
                            <td><strong>Percentage of Disability:</strong></td>
                            <td>{{ $data->percentage_disability }}</td>
                        </tr>
                        <tr>
                            <td><strong>Certifying Authority:</strong></td>
                            <td>{{ $data->certifying_auth }}</td>
                        </tr>
                    @endif
                    @if($scheme_id == 17)
                        <tr>
                            <td><strong>Phase:</strong></td>
                            <td>{{ $data->app_phase }}</td>
                        </tr>
                        <tr>
                            <td><strong>Temple Type:</strong></td>
                            <td>{{ $data->temple_type }}</td>
                        </tr>
                    @endif
                    @if($scheme_id == 10)
                        @if ($data->sm_flag == 1)
                            <tr>
                                <td><strong>Mobile Number:</strong></td>
                                <td>{{ $data->sm_mobile_no }}</td>
                            </tr>
                        @endif
                    @endif
                </table>
            </div>
        @endif
    </div>

</body>
</html>
