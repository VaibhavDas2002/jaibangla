
<style>
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
        text-transform: uppercase;

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

    .tableclass {
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        /* height:50px; */

        border-collapse: separate;
        border: solid #ccc 1px;
        /* border-radius: 8px; */
        overflow: hidden;
    }

    /* .tableclass tr:last-child td {
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    } */

    /* .borderClass, th, td {
  border: 1px solid black;
} */

</style>

<body>

    @foreach ($data as $row) 





        <table class="tableclass" cellspacing="1" cellpadding="1">
           
               

           
            <tbody>
              <tr>

                    <td> <img style="position: relative;top: 0px;"  src="{{ asset('images/biswo.png') }}"></td>
                    <td class="headtag" >Government of West Bengal</td>
                    <td></td>


                </tr>

                <tr><td colspan="4" style="position: relative;top: 0px;" class="smallheadtag">LOKKHI BHANDAR SCHEME</td></tr>
                <tr><td colspan="4" style="position: relative;top: 0px;" class="smallheadtag">APPLICATION FORM</td></tr>
                {{-- <tr><td class="" colspan="4">&nbsp; </td></tr> --}}
                <tr><td colspan="4" style="font-weight: bold; " class="borderClass">APPLICATION FORM for LOKKHI BHANDAR SCHEME </td></tr>
                {{-- <tr><td class="" colspan="4">&nbsp; </td></tr> --}}
                <tr><td colspan="4" style="font-weight: bold;" class="borderClass">PERSONAL DETAILS</td></tr>
                {{-- <tr><td class="" colspan="4">&nbsp; </td></tr> --}}
            </tbody>
        </table>
      
       
            <table class="tableclass" cellspacing="1" cellpadding="1">
                <tr>
                    <td class="borderClass1"> </td>
                    <td class="borderClass">First Name</td>
                    <td class="borderClass">Middle Name</td>
                    <td class="borderClass">Last Name</td>
                </tr>
                <tr>
                    <td class="borderClass1">Beneficiary Name:</td>
                    <td class="borderClass">{{$row->ben_fname}}</td>
                    <td class="borderClass">{{$row->ben_mname}}</td>
                    <td class="borderClass">{{$row->ben_lname}}</td>
                </tr>
                <tr>
                    <td colspan="1" class="borderClass1">Gender: </td>
                    <td colspan="3" class="borderClass1">{{$row->gender}} </td>
                </tr>
                <tr>
                    <td colspan="1" class="borderClass1">Date Of Birth: </td>
                    <td colspan="3" class="borderClass1">{{$row->dob}} </td>
                </tr>

                <tr>
                    <td colspan="1" class="borderClass1">Caste: </td>
                    <td colspan="1" class="borderClass1">{{$row->caste_category}} </td>
                    <td colspan="1" class="borderClass1">Caste Certificate No: </td>
                    <td colspan="1" class="borderClass1">{{$row->caste_certificate_no}} </td>
                </tr>
                {{-- <tr>
                    <td class="" colspan="4">&nbsp; </td>
                </tr> --}}
                <tr>
                    <td colspan="4" style="font-weight: bold;" class="borderClass">CONTACT DETAILS </td>
                </tr>
                {{-- <tr>
                    <td class="" colspan="4">&nbsp; </td>
                </tr> --}}
                {{-- <tr>
                    <td colspan="1" class="borderClass1">State* </td>
                    <td colspan="3" class="borderClass1">State* </td>
                </tr>

                <tr>
                    <td colspan="1" class="borderClass1">Assembly Constituency* </td>
                    <td colspan="3" class="borderClass1">Assembly Constituency* </td>
                </tr> --}}

                <tr>
                    <td colspan="1" class="borderClass1">District* </td>
                    <td colspan="3" class="borderClass1">{{$row->dist_name}} </td>
                </tr>

               
                <tr>
                    <td colspan="1" class="borderClass1">Block/Municipality/Corp.* </td>
                    <td colspan="3" class="borderClass1">{{$row->block_ulb_name}} </td>
                </tr>
                {{-- <tr>
                    <td colspan="1" class="borderClass1">GP/Ward No.* </td>
                    <td colspan="3" class="borderClass1">GP/Ward No.* </td>
                </tr> --}}
                <tr>
                    <td colspan="1" class="borderClass1">Village/Town/City* </td>
                    <td colspan="3" class="borderClass1">{{$row->village_town_city}}</td>
                </tr>
                <tr>
                    <td colspan="1" class="borderClass1">House / Premise No. </td>
                    <td colspan="3" class="borderClass1">{{$row->house_premise_no}} </td>
                </tr>
                <tr>
                    <td colspan="1" class="borderClass1">Post Office* </td>
                    <td colspan="3" class="borderClass1">{{$row->post_office}}</td>
                </tr>
                <tr>
                    <td colspan="1" class="borderClass1">Pin Code* </td>
                    <td colspan="3" class="borderClass1">{{$row->pincode}}</td>
                </tr>
                <tr>
                    <td colspan="1" class="borderClass1">Mobile Number* </td>
                    <td colspan="3" class="borderClass1">{{$row->mobile_no}}</td>
                </tr>
                {{-- <tr>
                    <td colspan="1" class="borderClass1">Email Id, if available </td>
                    <td colspan="3" class="borderClass1">Email Id, if available </td>
                </tr> --}}
                {{-- <tr>
                    <td class="" colspan="4">&nbsp; </td>
                </tr> --}}
                <tr>
                    <td colspan="4" style="font-weight: bold;" class="borderClass">BANK ACCOUNT DETAILS </td>
                </tr>
              
                <tr>
                    <td colspan="1" class="borderClass1">Bank Name* </td>
                    <td colspan="3" class="borderClass1">{{$row->bank_name}} </td>
                </tr>
                <tr>
                    <td colspan="1" class="borderClass1">Bank Branch Name* </td>
                    <td colspan="3" class="borderClass1">{{$row->branch_name}} </td>
                </tr>
                <tr>
                    <td colspan="1" class="borderClass1">Bank Account No.* </td>
                    <td colspan="3" class="borderClass1" style="font-weight: bold;">{{$row->bank_code}} </td>
                </tr>


            </table>
            <h6 style="font-weight: bold;">SELF DECLARATION</h6>
            <ul>
                <li>I give / do not give consent to the use of the Aadhaar No. for authenticating my identity for social
                    security pension (in case Aadhaar No. is provided by the Applicant).</li>
                <li>Presently, I am receiving following grant(s) from Central Govt. / State Govt. / Local Administration
                    / Govt. Aided Organization (in case the Applicant is receiving pension from any other source):-
                </li>

            </ul>
       
        @endforeach
 