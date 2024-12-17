<!DOCTYPE html>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"/>


<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <style>
    body {
    background: rgb(204,204,204); 
    }
    .headtag {
    font-size: 25px;
    text-align: center;
    font-weight: bold;
    }

    .smallheadtag {
    font-size: 20px;
    text-align: center;
    font-weight: bold;
    }

    .hr {
    border-top: 1px solid #000;
    margin-top: 1px;
    }


    .borderClass {
    border: 1px solid black;
    text-align: center;
    }

    .borderClass1 {
    border: 1px solid black;
    text-align: left;
    padding-right: 20px;
    }

    .bold_class {
    border: 1px solid black;
    font-weight: bold;


    }

    .tablebottom {
    position: absolute;
    bottom: 10px;
    width: 50%;
    border: 3px solid #8AC007;
    }
    .tableclass {
    margin-left: auto;
    margin-right: auto;
    width: 100%;
    /* height:50px; */

    border-collapse: separate;
    /* border: solid #ccc 1px; */
    /* border-radius: 8px; */
    overflow: hidden;
    }
    page {
    background: white;
    display: block;
    margin: 0 auto;
    margin-bottom: 0.5cm;
    /* box-shadow: 0 0 0.5cm rgba(0,0,0,0.5); */
    }
    page[size="A4"] {  
    width: 21cm;
    }
    page[size="A4"][layout="landscape"] {
    width: 29.7cm;
    height: 21cm;  
    }
    @media print {
    body, page {
        margin: 0;
        box-shadow: 0;
    }
    }

    </style>
</head>
<body>
    <page size="A4">
     @foreach ($data as $row) 
         <table style="width: 100%;">
            <tr>
                <td style="width: 20%;">
                     <img  src="{{ storage_path('app/public/images/biswo.png') }}">     
                </td>
                <td style="width: 60%; text-align: center;">
                    <h1>Government of West Bengal</h1>
                    <h2>LOKKHI BHANDAR SCHEME</h2>
                    <h3>APPLICATION FORM</h3>
                    <i>(To be filled in English Block Capital Letters Only)</i><br/>
                    <i>(Please Check Appropriate Boxes, wherever applicable)</i><br/>
                    <i>(* Marked fields are mandatory)</i><br/>
                </td>
                <td style="width: 20%; border: 1px solid black; text-align: center;">
                    <i>Affix Self—Attested Passport Size Photograph</i>
                </td>
            </tr>
            <tr><td style="padding-bottom: 10px;">&nbsp;</td></tr>
            <tr><td colspan="4" style="font-weight: bold; " class="borderClass">APPLICATION FORM for LOKKHI BHANDAR SCHEME </td></tr>
            <tr><td style="padding-bottom: 15px;">&nbsp;</td></tr>
            <tr><td colspan="4" style="font-weight: bold;" class="borderClass">PERSONAL DETAILS</td></tr>
        </table>


        <table class="tableclass" cellspacing="1" cellpadding="1">
            <tr>
                <td class=""> </td>
                <td class="borderClass">First Name</td>
                <td class="borderClass">Middle Name</td>
                <td class="borderClass">Last Name</td>
            </tr>
            <tr>
                <td class="borderClass1">Beneficiary Name:</td>
                <td class="borderClass">{{trim($row->ben_fname)}}</td>
                <td class="borderClass">{{trim($row->ben_mname)}}</td>
                <td class="borderClass">{{trim($row->ben_lname)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Gender: </td>
                <td colspan="1" class="borderClass1">{{trim($row->gender)}} </td>
                <td colspan="2" class=""> </td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Date Of Birth (dd/mm/yyyy):</td>
                <td colspan="1" class="borderClass1">@if($row->dob!=''){{\Carbon\Carbon::createFromFormat('Y-m-d', $row->dob)->format('d/m/Y')}}@endif</td>
                <td colspan="2" class=""> </td>
            </tr>
            <tr>
                <td class=""> </td>
                <td class="borderClass">First Name</td>
                <td class="borderClass">Middle Name</td>
                <td class="borderClass">Last Name</td>
            </tr>
            <tr>
                <td class="borderClass1">Father's Name:</td>
                <td class="borderClass">{{trim($row->father_fname)}}</td>
                <td class="borderClass">{{trim($row->father_mname)}}</td>
                <td class="borderClass">{{trim($row->father_lname)}}</td>
            </tr>
            <tr>
                <td class="borderClass1">Mother's Name:</td>
                <td class="borderClass">{{trim($row->mother_fname)}}</td>
                <td class="borderClass">{{trim($row->mother_mname)}}</td>
                <td class="borderClass">{{trim($row->mother_lname)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Religion*: </td>
                <td colspan="1" class="borderClass1">{{trim($row->religion)}}</td>
                <td colspan="2" class=""></td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Caste: </td>
                <td colspan="1" class="borderClass1">{{trim($row->caste)}}</td>
                <td colspan="1" class="borderClass1">Caste Certificate No: </td>
                <td colspan="1" class="borderClass1">{{trim($row->caste_certificate_no)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Marital Status: </td>
                <td colspan="1" class="borderClass1">{{trim($row->marital_status)}}</td>
                <td colspan="2" class=""></td>
            </tr>
            <tr>
                <td class=""> </td>
                <td class="borderClass">First Name</td>
                <td class="borderClass">Middle Name</td>
                <td class="borderClass">Last Name</td>
            </tr>
            <tr>
                <td class="borderClass1">Spouse Name, if Available:</td>
                <td class="borderClass">{{trim($row->spouse_fname)}}</td>
                <td class="borderClass">{{trim($row->spouse_mname)}}</td>
                <td class="borderClass">{{trim($row->spouse_lname)}}</td>
            </tr>
        
            <tr>
                <td colspan="4" style="font-weight: bold; padding-top: 20px;" class="borderClass">Monthly Income </td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Monthly Family Income (Rs.)* : </td>
                <td colspan="1" class="borderClass1">{{trim($row->mothly_income)}}</td>
                <td colspan="2" class=""></td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold; padding-top: 20px;" class="borderClass">Personal Identification Number(S) </td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Digital Ration Card No* : </td>
                <td colspan="3" class="borderClass1">{{trim($row->ration_card_cat)}}{{trim($row->ration_card_no)}}</td>
            </tr>    
            <tr>
                <td colspan="1" class="borderClass1">Aadhar No* : </td>
                <td colspan="3" class="borderClass1">{{trim($row->aadhar_no)}}</td>
            </tr>    
            <tr>
                <td colspan="1" class="borderClass1">EPIC/VoterId No* : </td>
                <td colspan="1" class="borderClass1">{{trim($row->epic_voter_id)}}</td>
                <td colspan="2" class=""></td>
            </tr>    
            <tr>
                <td colspan="1" class="borderClass1">PAN, if Available : </td>
                <td colspan="1" class="borderClass1">{{trim($row->pan_no)}}</td>
                <td colspan="2" class=""></td>
            </tr>    
            <tr>
                <td colspan="4" style="font-weight: bold; padding-top: 20px;" class="borderClass">CONTACT DETAILS </td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">State* </td>
                <td colspan="3" class="borderClass1">West Bengal</td>
            </tr>
             <tr>
                <td colspan="1" class="borderClass1">Assembly Constituency* </td>
                <td colspan="3" class="borderClass1">{{trim($row->assembly_name)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">District* </td>
                <td colspan="3" class="borderClass1">{{trim($row->dist_name)}}</td>
            </tr>
            
            <tr>
                <td colspan="1" class="borderClass1">Police Station* </td>
                <td colspan="3" class="borderClass1">{{trim($row->police_station)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Block/Municipality/Corp.* </td>
                <td colspan="3" class="borderClass1">{{trim($row->block_ulb_name)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">GP/Ward No.* </td>
                <td colspan="3" class="borderClass1">{{trim($row->gp_ward_name)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Village/Town/City* </td>
                <td colspan="3" class="borderClass1">{{trim($row->village_town_city)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">House / Premise No. </td>
                <td colspan="3" class="borderClass1">{{trim($row->house_premise_no)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Post Office* </td>
                <td colspan="3" class="borderClass1">{{trim($row->post_office)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Pin Code* </td>
                <td colspan="3" class="borderClass1">{{trim($row->pincode)}}</td>
            </tr>
           
            <tr>
                <td colspan="1" class="borderClass1">Number of Years Dwelling in West Bengal* </td>
                <td colspan="3" class="borderClass1">{{trim($row->residency_period)}}</td>
            </tr>
             <tr >
                <td colspan="1" class="borderClass1">Mobile Number* </td>
                <td colspan="3" class="borderClass1">{{trim($row->mobile_no)}}</td>
            </tr>
            <tr style="page-break-after: always;">
                <td colspan="1" class="borderClass1">Email Id,if available </td>
                <td colspan="3" class="borderClass1">{{trim($row->email)}}</td>
            </tr>
            
            <tr>
                <td colspan="4" style="font-weight: bold;" class="borderClass">BANK ACCOUNT DETAILS </td>
            </tr>
          
            <tr>
                <td colspan="1" class="borderClass1">Bank Name* </td>
                <td colspan="3" class="borderClass1">{{trim($row->bank_name)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Bank Branch Name* </td>
                <td colspan="3" class="borderClass1">{{trim($row->branch_name)}}</td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">Bank Account No.* </td>
                <td colspan="1" class="borderClass1" style="font-weight: bold;">{{trim($row->bank_code)}}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="1" class="borderClass1">IFS Code.* </td>
                <td colspan="3" class="borderClass1">{{trim($row->bank_ifsc)}}</td>
            </tr>
        </table>
        <table  class="tableclass" style="padding-top: 20px;">
            <tr>
                <td colspan="7" style="font-weight: bold;" class="borderClass">FAMILY MEMBERS </td>   
            </tr>
            <tr>
                <td class="borderClass">Sl No</td>
                <td class="borderClass">Member Name</td>
                <td class="borderClass">Sex</td>
                <td class="borderClass">Age</td>
                <td class="borderClass">Relationship</td>
                <td class="borderClass">Mobile No</td>
                <td class="borderClass">Aadhar No (if any)</td>
            </tr>
            <tr>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
                <td class="borderClass">&nbsp;</td>
            </tr>
            
        </table>
        <table style="width: 100%;padding-top: 20px;">
            <tr>
                <td colspan="5" style="font-weight: bold;" class="borderClass">ENCLOSURE LIST (SELF ATTESTED COPIES) (Please check Appropriate Boxes) </td>   
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>1</td>
                <td>Passport Photograph</td>
                <td><input type="checkbox"/></td>
                <td style="width: 10%;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>2</td>
                <td>Copy of Caste Certificate</td>
                <td><input type="checkbox"/></td>
                <td style="width: 10%;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>3</td>
                <td>Copy of Digital Ration Card</td>
                <td><input type="checkbox"/></td>
                <td style="width: 10%;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>4</td>
                <td>Copy of Aadhaar Card</td>
                <td><input type="checkbox"/></td>
                <td style="width: 10%;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>5</td>
                <td>Copy of Voter Id</td>
                <td><input type="checkbox"/></td>
                <td style="width: 10%;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>6</td>
                <td>Copy of Residential Certificate (Self Declaration)</td>
                <td><input type="checkbox"/></td>
                <td style="width: 10%;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>7</td>
                <td>Copy of Income Certificate (Self Declaration)</td>
                <td><input type="checkbox" checked/></td>
                <td style="width: 10%;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>8</td>
                <td>Copy of Bank Pass Book</td>
                <td><input type="checkbox"/></td>
                <td style="width: 10%;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td>9</td>
                <td>Others, please specify</td>
                <td><input type="checkbox"/></td>
                <td style="width: 10%;"></td>
            </tr>
        </table>
        <table style="width: 100%; page-break-after: always;">
            <tr>
                <td colspan="5">
                    <h3 style="font-weight: bold;">SELF DECLARATION</h3>
                    <ul>
                        <li>I give / do not give consent to the use of the Aadhaar No. for authenticating my identity for social
                            security pension (in case Aadhaar No. is provided by the Applicant).</li>
                        <li>Presently, I am receiving following grant(s) from Central Govt. / State Govt. / Local Administration
                            / Govt. Aided Organization (in case the Applicant is receiving pension from any other source):-
                        </li>
                    </ul>
                    <ol style="padding-top: 20px;">
                        <li>.........................................................................................................................................................................</li>
                        <li>.........................................................................................................................................................................</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr>
                <td>
                    Date:
                </td>
                <td colspan="2" style="padding: 40px;">
                    &nbsp;
                </td>
                <td colspan="2" style="text-align: right;">
                    (Signature of Applicant)
                </td>
            </tr>
        </table>        

        <table style="width: 100%;page-break-after: always;">
            <tr>
                <td colspan="3" style="font-weight: bold;" class="borderClass">FOR OFFICE USE ONLY</td>   
            </tr>
            <tr>
                <td class="borderClass1">Acknowledgement No.</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass1">Acknowledgement Date</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass1">Application Id.</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass1">Enquiry Officer’s Name</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass1">Enquiry Officer’s Designation</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass1">Enquiry Officer’s Mobile No.</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 10px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 50px; padding-top: 20px;">Comments of Enquiry Officer regarding acceptance/ rejection of the application): APPROVED / REJECTED
                    (Please mention reasons, if rejected)
                   </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 30px;">&nbsp;</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>&nbsp;</td>
                <td style="text-align: right;">(Signature with Stamp of Enquiry Officer)</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 10px;">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass1">Recommending Authority’s Name</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass1">Recommending Authority’s Designation</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="borderClass1">Recommending Authority’s Mobile No.</td>
                <td class="borderClass1" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 10px;">&nbsp;</td>
            </tr>
            <tr>
                <td style="padding-bottom: 40px;"><u><h6>COMMENTS</h6></u></td>
                <td colspan="2" style="padding-bottom: 10px;">&nbsp;</td>
            </tr>
            <tr>
                <td style="padding: 40px;">&nbsp;</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>&nbsp;</td>
                <td style="text-align: right;">(Signature with Stamp of Recommending Authority)</td>
            </tr>
        </table>
         @endforeach
    </page>

</body>
</html>
<!-- <page size="A4" layout="landscape">A4 landscape</page> -->
