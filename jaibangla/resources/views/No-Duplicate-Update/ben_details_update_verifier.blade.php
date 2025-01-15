<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>JB | Jai Bangla</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link href="{{ asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link href="{{ asset('/bower_components/AdminLTE/dist/css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet"
        type="text/css" />

    <style>
        * {
            font-size: 15px;
        }
        .field-name {
            float: left;
            font-weight: 600;
            font-size: 17px;
            margin-right: 3%;
            padding-top: 1%;
        }
        .field-value {


            font-size: 17px;
            padding-top: 1%;

        }
        .required-field::after {
            content: "*";
            color: red;
        }
        .row {
            margin-right: 0px !important;
            margin-left: 0px !important;
        }
        .section1 {
            border: 1.5px solid #9187878c;
            overflow: hidden;
            padding-bottom: 10px;


        }
        .color1 {

            background-color: #dcdfdf;
        }
        .color1 h3 {
            margin: 10px 0px 10px 0px !important;
        }
        .setPos {
            padding: 0px 0px 10px 0px;
            margin: 10px 0px 10px 0px;
            border: 1px solid #dcdfdf;
            overflow: hidden;
        }

        .modal_field_name {
            float: left;
            font-weight: 700;
            margin-right: 1%;
            padding-top: 1%;
            margin-top: 1%;
        }

        .modal_field_value {
            margin-right: 1%;
            padding-top: 1%;
            margin-top: 1%;
        }

        .modal-header {
            background-color: #7fffd4;
        }

        @media print {
            .example-screen {
                display: none;
            }

            * {
                font-size: 15px;
            }

            .field-name {
                float: left;
                font-weight: 600;
                font-size: 17px;
                margin-right: 3%;
                padding-top: 1%;
            }

            .field-value {


                font-size: 17px;
                padding-top: 1%;

            }

            .row {
                margin-right: 0px !important;
                margin-left: 0px !important;
            }

            .section1 {
                border: 1.5px solid #9187878c;
                overflow: hidden;
                padding-bottom: 10px;


            }

            .color1 {

                background-color: #dcdfdf;

            }

            .color1 h3 {
                margin: 10px 0px 10px 0px !important;
            }

            .setPos {
                padding: 0px 0px 10px 0px;
                margin: 10px 0px 10px 0px;
                border: 1px solid #dcdfdf;
                overflow: hidden;
            }

            .modal_field_name {
                float: left;
                font-weight: 700;
                margin-right: 1%;
                padding-top: 1%;
                margin-top: 1%;
            }

            .modal_field_value {
                margin-right: 1%;
                padding-top: 1%;
                margin-top: 1%;
            }

            .modal-header {
                background-color: #7fffd4;
            }
        }

        .btnJb {
            margin: 20px;
        }
    </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- Main Header -->
        @include('layouts.header')
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content">
                <div class="row">
                    @if (count($field_arrays)> 0)
                    <div class="alert alert-danger alert-block">
                        <strong>Below are the incomplete details</strong>
                        <ul>
                            @foreach ($field_arrays as $field_array)
                            <li><strong> {{ $field_array }}</strong></li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                
                <div class="row">

                    <!-- left column -->
                    <div class="col-md-12">

                        @if (($message = Session::get('success')) && ($id = Session::get('lb_id')))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }} with LB Application ID: {{ $id }}</strong>


                        </div>
                        @endif
                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>


                        </div>
                        @endif
                        @if (count($errors) > 0)
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li><strong> {{ $error }}</strong></li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                    </div>

                    <div class="tab-content" style="margin-top:16px;">
                        <div class="tab-pane active" id="personal_details">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4><b>Beneficiary Details </b></h4>
                                </div>
                                <div class="panel-body">


                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="text-align: center; color:rgb(18, 219, 62);">Application ID:{{ $row->id }}
                                                <a href="{{ route('noDupBeneficiariesList') }}"><img width="50px;" style="pull-right ;"
                                                        src="{{ asset('images/back.png') }}" alt="Back" /></a>
                                            </h3>
                                        </div>
                                    </div>
                                
                                   


                                    @if($row->is_bank_failed == 1)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="text-align: center; color:rgb(238, 9, 9);">
                                                @if($PaymentErrorType->pay_validated == 3)
                                                Payment Transaction Failed from SBI.
                                                @endif
                                                @if($PaymentErrorType->pay_validated == 4)
                                                Payment Transaction Failed from RBI.
                                                @endif
                                                @if($PaymentErrorType->pay_validated == 5)
                                                Payment Transaction Failed from IFMS.
                                                @endif
                                            </h3>
                                        </div>
                                    </div>
                                    @endif


                                    @if($invalid_status)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="text-align: center; color:rgb(238, 9, 9);">Failed Reason :  {{$invalid_status}}</h3>
                                        </div>
                                    </div>
                                @endif



                                    @include('pension-details-view.personal_details')
                                    @include('pension-details-view.personal_identification')
                                    @include('pension-details-view.bank_details')
                                    @include('pension-details-view.contact_details')
                                    @if ($is_verifier)
                                    <form method="post" name="formSubmit" id="formSubmit"
                                        action="{{ route('updateApplicantDetails') }}" class="submit-once"
                                        enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="is_verifier" id="is_verifier" value="{{$is_verifier}}" />
                                        <input type="hidden" name="id" id="id" value="{{ $row->id }}" />
                                        <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $row->scheme_id }}" />
                                        <input type="hidden" name="old_aadhar" id="old_aadhar" value="{{ $row->aadhar_no }}" />
                                        <input type="hidden" name="old_bank_code" id="old_bank_code" value="{{ trim($row->bank_code) }}" />
                                        <input type="hidden" name="old_bank_ifsc" id="old_bank_ifsc" value="{{ trim($row->bank_ifsc) }}" />
                                        <input type="hidden" name="old_bank_name" id="old_bank_name" value="{{ $row->bank_name }}" />
                                        <input type="hidden" name="old_bank_branch" id="old_bank_branch" value="{{ $row->branch_name }}" />
                                        <input type="hidden" name="old_mobile_no" id="old_mobile_no" value="{{ $row->mobile_no }}" />
                                        <input type="hidden" name="dup_bank" id="dup_bank" value="{{ $row->dup_bank }}">
                                        <input type="hidden" name="dup_aadhar" id="dup_aadhar" value="{{ $row->dup_aadhar }}">
                                        <input type="hidden" name="dup_mobile" id="dup_mobile" value="{{ $row->dup_mobile }}">
                                        <input type="hidden" name="no_mobile" id="no_mobile" value="{{ $row->no_mobile }}">
                                        <input type="hidden" name="no_aadhar" id="no_aadhar" value="{{ $row->no_aadhar }}">
                                        <input type="hidden" name="is_incomplete" id="is_incomplete" value="{{ $row->is_incomplete }}">
                                        <input type="hidden" name="is_bank_failed" id="is_bank_failed" value="{{ $row->is_bank_failed }}">
                                        <input type="hidden" name="bank_checked" id="bank_checked" value="">
                                        <input type="hidden" name="mobile_checked" id="mobile_checked" value="">
                                        <input type="hidden" name="aadhar_checked" id="aadhar_checked" value="">
                                        <input type="hidden" name="aadhar_doc" id="aadhar_doc" value="{{$getAadharDoc}}">





                                        @if (intval($row->dup_aadhar) == 1 || intval($row->no_aadhar) == 1)
                                        <div class="panel panel-default">
                                            <div class="panel-heading" id="panel_head"
                                                style="font-size: 14px; font-weight: bold; font-style: italic;">New Aadhar Details</div>
                                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                                <div class="row">

                                                    <div class="form-group col-md-4">
                                                        <label class="required-field">Aadhaar
                                                            Number</label>
                                                        <input type="text" name="new_aadhar_no" id="new_aadhar_no"
                                                            class="form-control NumOnly" placeholder="Aadhar No."
                                                            maxlength="12" value="{{ trim($row->aadhar_no) }}" />
                                                        <span id="error_new_aadhar_no" class="text-danger"></span>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label
                                                            class="@if (intval($row->no_aadhar) == 1) required-field @endif">Aadhaar
                                                            Document</label>
                                                        <input type="file" name="new_aadhar_doc"
                                                            id="new_aadhar_doc" class="form-control" />
                                                        <span id="error_new_aadhar_doc" class="text-danger"></span>
                                                    </div>

                                                    @if ($getAadharDoc > 0)
                                                    <div class="form-group col-md-2" style="margin-top: 30px;">
                                                        <button class="btn btn-warning btn-sm aadhar_doc_button"
                                                            id="bankDoc_{{ $row->id }}" value="{{ $row->id }}">View Existing Aadhaar Copy</button>
                                                    </div>
                                                    @endif
                                                    @if(intval($row->dup_aadhar == 1))
                                                    <div class="form-group col-md-2" style="margin-top: 30px;" id="aadharCheckDiv">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" id="aadharCheck">
                                                            <label class="form-check-label" for="aadharCheck">
                                                                Keep Same
                                                            </label>
                                                        </div>
                                                        <span id="error_new_aadhar_no" class="text-danger"></span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if (intval($row->dup_bank) == 1 || intval($row->is_bank_failed == 1) || intval($row->is_bank_failed == 2) || intval($row->is_bank_failed == 3))
                                        <div class="panel panel-default">
                                            <div class="panel-heading" id="panel_head"
                                                style="font-size: 14px; font-weight: bold; font-style: italic;">New Bank Details</div>
                                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label class="required-field">IFSC</label>
                                                        <input type="text" name="bank_ifsc_code"
                                                            id="bank_ifsc_code" class="form-control"
                                                            autocomplete="off" placeholder="IFSC Code"
                                                            onkeyup="this.value = this.value.toUpperCase();"
                                                            value="{{ trim($row->bank_ifsc) }}" />
                                                        <span id="error_bank_ifsc_code" class="text-danger"></span>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label class="required-field">Bank
                                                            Branch</label>
                                                        <input type="text" name="new_bank_branch"
                                                            id="new_bank_branch" class="form-control NumOnly"
                                                            placeholder="Bank Account"
                                                            value="{{ trim($row->branch_name) }}" readonly />
                                                        <span id="error_new_bank_branch" class="text-danger"></span>
                                                    </div>


                                                    <br />
                                                </div>
                                                <div class="row">

                                                    <div class="form-group col-md-4">
                                                        <label class="required-field">Bank
                                                            Name</label>
                                                        <input type="text" name="new_bank_name" id="new_bank_name"
                                                            class="form-control" placeholder="Bank Name"
                                                            value="{{ trim($row->bank_name) }}" readonly />
                                                        <span id="error_new_bank_name" class="text-danger"></span>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label class="required-field">Bank
                                                            Account</label>
                                                        <input type="text" name="new_bank_code" id="new_bank_code"
                                                            class="form-control NumOnly" placeholder="Bank Account"
                                                            value="{{ trim($row->bank_code) }}" />
                                                        <span id="error_new_bank_code" class="text-danger"></span>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label class="required-field">Confirm Bank
                                                            Account</label>
                                                        <input type="text" name="new_confirm_bank_code" id="new_confirm_bank_code"
                                                            class="form-control NumOnly" placeholder="Confrin Bank Account"
                                                            value="{{ trim($row->bank_code) }}" />
                                                        <span id="error_new_confirm_bank_code" class="text-danger"></span>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label class="required-field">Passbook
                                                            Document</label>
                                                        <input type="file" name="new_bank_doc" id="new_bank_doc"
                                                            class="form-control" />
                                                        <span id="error_new_bank_doc" class="text-danger"></span>
                                                    </div>
                                                    @if($getBankDoc > 0)
                                                    <div class="form-group col-md-2" style="margin-top: 30px;">
                                                        <button class="btn btn-warning btn-sm bank_doc_button"
                                                            id="bankDoc_{{ $row->id }}"
                                                            value="{{ $row->id }}">View Passbook</button>
                                                    </div>
                                                    @endif
                                                    @if ($row->dup_bank == 1)
                                                    <div class="form-group col-md-2" style="margin-top: 30px;" id="bankCheckDiv">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="2" id="bankCheck">
                                                            <label class="form-check-label" for="bankCheck">
                                                                Keep Same
                                                            </label>
                                                        </div>
                                                        <span id="error_new_bank_code" class="text-danger"></span>
                                                    </div>
                                                    @endif
                                               
                                                </div>


                                                @if ($row->is_bank_failed == 2)
                                              
                                                
                                                @if (!empty($av_name_response))
                                                    <div class="row">
                                                        <div style="font-size: 16px; text-align: center; ">
                                                            Name Response from Bank :- 
                                                            <span id="name_response" class="text-success"><b>{{ $av_name_response }}</b></span>
                                                        </div>
                                                    </div>
                                                @endif


                                                <div class="row">
                                                    <div id="radio_div">
                                                        <div style="font-size:15px; font-weight: bold; font-style: italic;" class="text-warning" align="center">
                                                            Please select which one do you want to process?
                                                        </div>
                                                        <div style="padding: 5px 5px 5px 50px; border: 1px solid whitesmoke; border-radius: 5px; margin: 5px 0px; background-color: whitesmoke;" class="row">
                                                            @foreach ($name_options as $options )
                                                            <label style="cursor: pointer; margin-bottom: 5px;">
                                                                <input type="radio" id="process_type" name="process_type" class="process_type_radio" value="{{$options->id}}">
                                                                {{ $options->description }}
                                                            </label><br>
                                                            @endforeach                                                            
                                                        </div>
                                                        <span id="error_process_type" class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        @endif


                                        @if (intval($row->dup_mobile) == 1 || intval($row->no_mobile) == 1 || intval($row->is_bank_failed == 1))

                                        <div class="panel panel-default">
                                            <div class="panel-heading" id="panel_head"
                                                style="font-size: 14px; font-weight: bold; font-style: italic;">New Mobile Details</div>
                                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label
                                                            class="@if (intval($row->no_mobile) == 1) required-field @endif">Mobile
                                                            Number</label>
                                                        <input type="text" id="new_mobile_no"
                                                            name="new_mobile_no" class="form-control NumOnly"
                                                            placeholder="Mobile No" maxlength="10"
                                                            value="{{ trim($row->mobile_no) }}">
                                                        <span id="error_new_mobile_no" class="text-danger"></span>
                                                    </div>
                                                    @if (intval($row->dup_mobile == 1))
                                                    <div class="form-group col-md-4" style="margin-top: 30px;" id="mobileCheckDiv">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="3" id="mobileCheck">
                                                            <label class="form-check-label" for="mobileCheck">
                                                                Keep Same
                                                            </label>
                                                        </div>
                                                        <span id="error_new_aadhar_no"
                                                            class="text-danger"></span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif


                                        <!-- Manabik Edit -->
                                        @if ($row->scheme_id == 2 && $row->is_incomplete == 1 && !empty(array_intersect([10,11,12,13,14], $required_fields)))

                                        <div class="panel panel-default">
                                            <div class="panel-heading" id="panel_head"
                                                style="font-size: 14px; font-weight: bold; font-style: italic;">New Disability Details</div>
                                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Type of Disability</label>
                                                    <select class="form-control" name="new_disablity_type" id="disablity_type">
                                                        @if ($type == $op_type)
                                                        @foreach(Config::get('constants.disablity_type') as $key => $val)
                                                        <option value="{{ $key }}" @if($row->type_disability == $key) selected @endif>
                                                            {{ $val }}
                                                        </option>
                                                        @endforeach
                                                        @else
                                                        <option value="">--Select--</option>
                                                        @foreach(Config::get('constants.disablity_type') as $key => $val)
                                                        <option value="{{$key}}" @if(old('disablity_type')==$key) selected @endif>{{$val}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    <span id="error_disablity_type" class="text-danger"></span>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Percentage of Disablity</label>
                                                    <input type="text" name="new_disablity_type_percentage" id="disablity_type_percentage" class="form-control "
                                                        placeholder="Percentage" maxlength="5"
                                                        value="{{ $type == $op_type ? $row->percentage_disability : old('disablity_type_percentage') }}" />
                                                    <span id="error_disablity_type_percentage" class="text-danger"></span>

                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Authority Name</label>
                                                    <input type="text" name="new_disablity_type_authority" id="disablity_type_authority" class="form-control txtOnly"
                                                        placeholder="Certifying Authority" maxlength="200"
                                                        value="{{$type == $op_type ? $row->certifying_auth : old('disablity_type_authority') }}" />
                                                    <span id="error_disablity_type_authority" class="text-danger"></span>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Authority Designation</label>
                                                    <input type="text" name="new_disability_designation" id="disability_designation" class="form-control txtOnly"
                                                        placeholder="Designation Name" maxlength="200"
                                                        value="{{$type == $op_type ? $row->disability_designation : old('disability_designation') }}" />
                                                    <span id="error_disability_designation" class="text-danger"></span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label
                                                        class="required-field">Disability Certificate from Appropriate Authority</label>
                                                    <input type="file" name="new_disability_doc"
                                                        id="new_disability_doc" class="form-control" />
                                                    <span id="error_new_disability_doc" class="text-danger"></span>
                                                </div>
                                                @if ($getDisabilityDoc > 0)
                                                <div class="form-group col-md-2" style="margin-top: 30px;">
                                                    <button class="btn btn-warning btn-sm aadhar_doc_button"
                                                        id="bankDoc_{{ $row->id }}"
                                                        value="{{ $row->id }}">View Existing Disability Certificate Copy</button>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                </div>




                                @endif

                                @if ($row->scheme_id == 11 && $row->is_incomplete == 1 && !empty(array_intersect([26,9], $required_fields)))
                                <div class="panel panel-default">
                                    <div class="panel-heading" id="panel_head"
                                        style="font-size: 14px; font-weight: bold; font-style: italic;">New Husband Death Details</div>
                                    <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                        <div class="form-group col-md-12">
                                            <label class="">Husband's Name</label>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="required-field">First Name</label>
                                            <input type="text" name="new_husband_first_name" id="husband_first_name" class="form-control txtOnly"
                                                placeholder="First Name" maxlength="200" value="{{$type == $op_type ? $row->husband_fname : old('husband_first_name') }}" tabindex="4" />
                                            <span id="error_husband_first_name" class="text-danger"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Middle Name</label>
                                            <input type="text" name="new_husband_middle_name" id="husband_middle_name" class="form-control txtOnly"
                                                placeholder="Middle Name" maxlength="100" value="{{$type == $op_type ? $row->husband_mname : old('husband_middle_name') }}" tabindex="5" />
                                            <span id="error_husband_middle_name" class="text-danger"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="required-field">Last Name</label>
                                            <input type="text" name="new_husband_last_name" id="husband_last_name" class="form-control txtOnly"
                                                placeholder="Last Name" maxlength="200" value="{{$type == $op_type ? $row->husband_lname : old('husband_last_name') }}" tabindex="6" />
                                            <span id="error_husband_last_name" class="text-danger"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="required-field">Husband's Death Certificate</label>
                                            <input type="file" name="new_husband_death_doc"
                                                id="new_husband_death_doc" class="form-control" />
                                            <span id="error_new_husband_death_doc" class="text-danger"></span>
                                        </div>
                                        @if ($getHusbandDoc > 0)
                                        <div class="form-group col-md-2" style="margin-top: 30px;">
                                            <button class="btn btn-warning btn-sm husband_doc_button"
                                                id="bankDoc_{{ $row->id }}"
                                                value="{{ $row->id }}">View Existing Husband Death Certificate </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            @endif
                            @if ($row->is_incomplete == 1 && ($row->scheme_id == 1 || $row->scheme_id == 3) && !empty(array_intersect([3, 4, 18], $required_fields)))
                            <div class="panel panel-default">
                                <div class="panel-heading" id="panel_head"
                                    style="font-size: 14px; font-weight: bold; font-style: italic;">New Caste Certificate Details</div>
                                <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                    <div class="row">

                                        <div class="form-group col-md-4">
                                            <label class="required-field">Caste</label>
                                            <select class="form-control" name="new_caste_category" id="caste_category">
                                                @if ($type == $op_type)
                                                @if($scheme_id == 3)
                                                <option value="SC">SC</option>
                                                @elseif ($scheme_id == 1)
                                                <option value="ST">ST</option>
                                                @else
                                                @foreach(Config::get('constants.caste') as $key => $val)
                                                <option value="{{$key}}" @if($row->gender == $key) selected @endif>{{$val}}
                                                </option>
                                                @endforeach
                                                @endif
                                                @else
                                                @if($scheme_id == 3)
                                                <option value="SC">SC</option>
                                                @elseif ($scheme_id == 1)
                                                <option value="ST">ST</option>
                                                @else
                                                @foreach(Config::get('constants.caste') as $key => $val)
                                                <option value="{{$key}}" @if(old('caste_category')==$key) selected @endif>{{$val}}
                                                </option>
                                                @endforeach
                                                @endif
                                                @endif
                                            </select>
                                            <span id="error_caste_category" class="text-danger"></span>
                                        </div>

                                        <div class="form-group col-md-4" id="caste_certificate_no_section">
                                            <label class="required-field">Caste Certificate No.</label>
                                            <input type="text" name="new_caste_certificate_no" id="caste_certificate_no" class="form-control"
                                                placeholder="Caste Certificate No." maxlength="200"
                                                value="{{$type == $op_type ? $row->caste_certificate_no : old('caste_certificate_no')}}" />
                                            <span id="error_caste_certificate_no" class="text-danger"></span>
                                        </div>


                                        <div class="form-group col-md-4">
                                            <label class="required-field">Caste Certificate</label>
                                            <input type="file" name="new_caste_certificate_doc"
                                                id="new_caste_certificate_doc" class="form-control" />
                                            <span id="new_caste_certificate_doc" class="text-danger"></span>
                                        </div>
                                    </div>
                                    @if ($getCasteDoc > 0)
                                    <div class="form-group col-md-2" style="margin-top: 30px;">
                                        <button class="btn btn-warning btn-sm caste_doc_button"
                                            id="casteDoc_{{ $row->id }}"
                                            value="{{ $row->id }}">View Existing Caste Certificate </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                       

                        @endif

                        @if ($row->no_ration_card == 1 && !empty(array_intersect([19,20,21], $required_fields)))
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head"
                                style="font-size: 14px; font-weight: bold; font-style: italic;">New Ration Card Details</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="required-field">Digital Ration Card Number</label>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <select class="form-control" name="new_ration_card_cat" id="ration_card_cat" style="margin-left:-15px;">
                                                    @if ($type == $op_type)
                                                    @foreach(Config::get('constants.ration_cat') as $key => $val)
                                                    <option value="{{$key}}" @if($row->ration_card_cat == $key) selected @endif>{{$val}}</option>
                                                    @endforeach
                                                    @else
                                                    <option value="">Category</option>
                                                    @foreach(Config::get('constants.ration_cat') as $key => $val)
                                                    <option value="{{ $key }}" @if(old('ration_card_cat')==$key) selected @endif>{{ $val }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-7">
                                                <input type="text" name="new_ration_card_no" id="ration_card_no" class="form-control NumOnly"
                                                    placeholder="Card Number" maxlength="10"
                                                    value="{{$type == $op_type ? $row->ration_card_no : old('ration_card_no') }}"
                                                    style="margin-left:-15px; margin-right:-15px;" />
                                            </div>
                                            <span id="error_ration_card_cat" class="text-danger"></span><br />
                                            <span id="error_ration_card_no" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="required-field">Copy of Digital Ration Card</label>
                                        <input type="file" name="new_ration_doc" id="new_ration_doc" class="form-control" />
                                        <span id="new_ration_doc" class="text-danger"></span>
                                    </div>
                                    @if ($getRationDoc > 0)
                                    <div class="form-group col-md-2" style="margin-top: 30px;">
                                        <button class="btn btn-warning btn-sm aadhar_doc_button" id="bankDoc_{{ $row->id }}" value="{{ $row->id }}">
                                            View Existing Digital Ration Card
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        @if ($row->no_epic_voter == 1 &&  !empty(array_intersect([22,23], $required_fields)))
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head"
                                style="font-size: 14px; font-weight: bold; font-style: italic;">New EPIC / Voter Card Id Details </div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="required-field">EPIC/Voter Id number</label>
                                        <input type="text" name="new_epic_voter_id" id="epic_voter_id" class="form-control"
                                            placeholder="EPIC/Voter Id.No." maxlength="20"
                                            value="{{$type == $op_type ? $row->epic_voter_id : old('epic_voter_id') }}" />
                                        <span id="error_epic_voter_id" class="text-danger"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="required-field">Copy of EPIC/ Voter Id</label>
                                        <input type="file" name="new_epic_doc" id="new_epic_doc" class="form-control" />
                                        <span id="new_epic_doc" class="text-danger"></span>
                                    </div>
                                    @if ($getEpicDoc > 0)
                                    <div class="form-group col-md-2" style="margin-top: 30px;">
                                        <button class="btn btn-warning btn-sm aadhar_doc_button" id="bankDoc_{{ $row->id }}" value="{{ $row->id }}">
                                            View EPIC/Voter ID Card
                                        </button>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    </div>

                    </form>
                    @endif
                    <br /> <br /> <br /> <br />
                </div>

        </div>

        <div class="col-md-12" align="center">
            <div class="btn-group">
                <button type="button" class="btnJb btn btn-info confirmBtn" id="fetch_document"
                    value="1" op_text="Are You Want to Update">Update</button>
            </div>
        </div>

        <br />
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <!-- /.box -->
    </div>
    <!--/.col (left) -->

    </div>

    <!-- /.row -->
    <div id="modalReject" class="modal fade">

        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header flex-column">

                    <h4 class="modal-title w-100">Confirm Message</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 style="font-size:30px;color: #fc3903;"><span id="op_text"
                            style="font-size:30px;color: #fc3903;"></span> this ID:{{ $row->id }}?</h4>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info modal-submitapprove">OK</button>
                    <button type="button" id="submittingapprove" value="Submit"
                        class="btn btn-success success btn-lg" disabled>Submitting please wait</button>
                </div>
            </div>
        </div>

    </div>
    <div class="modal" id="encolser_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="encolser_name">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="encolser_content"> </div>


            </div>
        </div>
    </div>
    </section>

    <!-- Main content -->
    <!--  <section class="content">

      Your Page Content Here



    </section> -->
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer -->
    @include('layouts.footer')

    <!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- jQuery 2.1.3 -->
    <script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js') }}"
        type="text/javascript"></script>

    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset('/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset('/bower_components/AdminLTE/dist/js/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('js/validateAdhar.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#submittingapprove").hide();
            $(".NumOnly").keyup(function(event) {
                $(this).val($(this).val().replace(/[^\d].+/, ""));
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

            $('.process_type_radio').change(function() {
                if ($(this).val() == 1 || $(this).val() == 3) {
                    $("#new_bank_code").prop("readonly", true);
                    $("#bank_ifsc_code").prop("readonly", true);
                    $("#new_confirm_bank_code").prop("readonly", true);
                } else {
                $("#new_bank_code").prop("readonly", false);
                $("#bank_ifsc_code").prop("readonly", false);
                $("#new_confirm_bank_code").prop("readonly", false);
                }
            });

            function validateBankCodes() {
                var bankCode = $('#new_bank_code').val();
                var confirmBankCode = $('#new_confirm_bank_code').val();

                if (bankCode !== confirmBankCode) {
                    error_new_confirm_bank_code = 'Bank codes do not match.'; 
                    $('#error_new_confirm_bank_code').text(error_new_confirm_bank_code);
                } else {
                    error_new_confirm_bank_code = '';
                    $('#error_new_confirm_bank_code').text(error_new_confirm_bank_code); // Clear the error
                }
            }

            // Event listeners for keyup on both fields
            $('#new_bank_code, #new_confirm_bank_code').on('keyup', function() {
                validateBankCodes();
            });

            $('.confirmBtn').click(function() {
                var designation_id = $("#designation_id").val();
                var new_aadhar = $('#new_aadhar_no').val();
                var old_aadhar = $('#old_aadhar').val();
                var new_bank = $('#new_bank_code').val();
                var old_bank = $('#old_bank_code').val();
                var new_ifsc = $('#bank_ifsc_code').val();
                var old_ifsc = $('#old_bank_ifsc').val();
                var new_bank_branch = $('#new_bank_branch').val();
                var new_bank_name = $('#new_bank_name').val();
                var new_mobile = $('#new_mobile_no').val();
                var old_mobile = $('#old_mobile').val();
                var bank_check = $('#bank_checked').val();
                var aadhar_check = $('#aadhar_checked').val();
                var mobile_check = $('#mobile_checked').val();
                var is_verifier = $('#is_verifier').val();
              
                // alert("Aadhar Doc: " + aadhar_doc + "\nDuplicate Aadhar: " + dup_aadhar);

                
                
                // $('#bank_code').val(new_bank);
                // $('#bank_ifsc').val(new_ifsc);
                // $('#new_aadhar').val(new_aadhar);
                // $('#new_mobile').val(new_mobile);
                // $('#new_bank_name').val(new_bank_name);
                // $('#new_bank_branch').val(new_bank_branch);
                // $('#bank_name').val($('#old_bank_name').val());
                // $('#bank_branch').val($('#old_bank_branch').val());

                $("#action_type").val('');
                $('.verify_reject').text('');
                var op_text = $(this).attr("op_text");
                $('#op_text').text(op_text);
                $('#action_msg').val(op_text);
                // alert(op_text);
                if (is_verifier) {
                    var error_new_mobile_no = '';
                    var error_new_aadhar_no = '';
                     var error_new_confirm_bank_code = ''
                    if ($.trim($('#new_mobile_no').val()) != "") {
                        if ($.trim($('#new_mobile_no').val()).length != 10) {
                            error_new_mobile_no = 'Mobile Number must be 10 digit';
                            $('#error_new_mobile_no').text(error_new_mobile_no);
                            $('#new_mobile_no').addClass('has-error');
                        } else {
                            error_new_mobile_no = '';
                            $('#error_mobile_no').text(error_new_mobile_no);
                            $('#new_mobile_no').removeClass('has-error');

                        }
                    }
                    if ($.trim($('#new_aadhar_no').val()) != "") {
                        if ($.trim($('#new_aadhar_no').val()).length != 12) {

                            error_new_aadhar_no = 'Aadhar No should be 12 digit ';
                            $('#error_new_aadhar_no').text(error_new_aadhar_no);
                            $('#new_aadhar_no').addClass('has-error');
                        } else {
                            var new_aadhar_no = $('#new_aadhar_no').val();
                            if (new_aadhar_no != '') {
                                var aadhar_valid = validate_adhar(new_aadhar_no);
                                // aadhar_valid=1;
                                if (aadhar_valid) {
                                    error_new_aadhar_no = '';
                                    $('#error_new_aadhar_no').text(error_new_aadhar_no);
                                    $('#new_aadhar_no').removeClass('has-error');
                                } else {
                                    error_new_aadhar_no = 'Invalid Aadhar No.';
                                    $('#error_new_aadhar_no').text(error_new_aadhar_no);
                                    $('#new_aadhar_no').addClass('has-error');
                                }
                            } else {
                                error_new_aadhar_no = '';
                                $('#error_new_aadhar_no').text(error_new_aadhar_no);
                                $('#new_aadhar_no').removeClass('has-error');
                            }
                        }
                    }

                    
                    if (error_new_mobile_no == '' && error_new_aadhar_no == '' && error_new_confirm_bank_code  == '') {
                        $('#modalReject').modal();
                    }
                }
                $('#modalReject').modal();
            });
            // *******Aadhar document view******* //
            $(document).on('click', '.aadhar_doc_button', function(e) {
                e.preventDefault();
                // $('.aadhar_doc_button').attr('disabled', true);
                var benid = $(this).val();
                View_encolser_modal('Copy of Aadhar Document', 6, benid);
            });
            // *******Aadhar document view******* //
            $(document).on('click', '.bank_doc_button', function(e) {
                e.preventDefault();
                // $('.bank_doc_button').attr('disabled', true);
                var benid = $(this).val();
                View_encolser_modal('Copy of Bank Passbook', 10, benid);
            });

            $(document).on('click', '.caste_doc_button', function(e) {
                e.preventDefault();
                // $('.caste_doc_button').attr('disabled', true);
                var benid = $(this).val();
                View_encolser_modal('Copy of Caste Certificate', 3, benid);
            });

            $(document).on('click', '.husband_doc_button', function(e) {
                e.preventDefault();
                // $('.caste_doc_button').attr('disabled', true);
                var benid = $(this).val();
                View_encolser_modal('Copy of Husband Death Certificate', 105, benid);
            });
            // *******keep same******* //
            $('#aadharCheck').click(function() {
                var aadhar_no = $("#new_aadhar_no").val();
                var aadharSameVal = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('aadharDupCheck') }}",
                    data: {
                        aadhar_no: aadhar_no,
                        aadharSameVal: aadharSameVal,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            $('#aadharCheck').prop('checked', false);
                            alert(data.msg);
                        } else {
                            var isChecked = $('#aadharCheck').is(":checked");
                            if (isChecked == true) {
                                $("#new_aadhar_no").prop("readonly", true);
                            } else {
                                $("#new_aadhar_no").prop("readonly", false);
                            }
                        }
                    },
                    error: function(ex) {
                        alert(sessiontimeoutmessage);
                        window.location.href = base_url;
                    }
                });
            });



            $('#bankCheck').click(function() {
                var bank_code = $("#new_bank_code").val();
                var bank_ifsc = $("#bank_ifsc_code").val();
                var bankSameVal = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('bankDupCheck') }}",
                    data: {
                        bank_code: bank_code,
                        bank_ifsc: bank_ifsc,
                        bankSameVal: bankSameVal,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            $('#bankCheck').prop('checked', false);
                            alert(data.msg);
                        } else {
                            var isChecked = $('#bankCheck').is(":checked");
                            if (isChecked == true) {
                                $("#new_bank_code").prop("readonly", true);
                                $("#bank_ifsc_code").prop("readonly", true);
                            } else {
                                $("#new_bank_code").prop("readonly", false);
                                $("#bank_ifsc_code").prop("readonly", false);
                            }
                        }
                    },
                    error: function(ex) {
                        alert(sessiontimeoutmessage);
                        window.location.href = base_url;
                    }
                });
            });



            $('#mobileCheck').click(function() {
                var mobile_no = $("#new_mobile_no").val();
                var mobileSameVal = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('mobileDupCheck') }}",
                    data: {
                        mobile_no: mobile_no,
                        mobileSameVal: mobileSameVal,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            $('#mobileCheck').prop('checked', false);
                            alert(data.msg);
                        } else {
                            var isChecked = $('#mobileCheck').is(":checked");
                            if (isChecked == true) {
                                $("#new_mobile_no").prop("readonly", true);
                            } else {
                                $("#new_mobile_no").prop("readonly", false);
                            }
                        }
                    },
                    error: function(ex) {
                        alert(sessiontimeoutmessage);
                        window.location.href = base_url;
                    }
                });
            });



            $('#mobileCheck').click(function() {
                var isChecked = $(this).is(":checked");
                if (isChecked == true) {
                    $("#new_mobile_no").prop("readonly", true);
                } else {
                    $("#new_mobile_no").prop("readonly", false);
                }
                var val = $(this).val();
                $('#mobile_checked').val(val);
            });


            $('#aadharCheck').click(function() {
                var isChecked = $(this).is(":checked");
                if (isChecked == true) {
                    $("#new_aadhar_no").prop("readonly", true);
                } else {
                    $("#new_aadhar_no").prop("readonly", false);
                }
                var val = $(this).val();
                $('#aadhar_checked').val(val);
            });

           

            $('#bankCheck').click(function() {

                var isChecked = $(this).is(":checked");
                if (isChecked == true) {
                    $("#bank_ifsc_code").prop("readonly", true);
                    // $("#new_bank_branch").prop("readonly", true);
                    $("#new_bank_code").prop("readonly", true);
                    // $("#new_bank_name").prop("readonly", true);
                } else {
                    $("#bank_ifsc_code").prop("readonly", false);
                    // $("#new_bank_branch").prop("readonly", false);
                    $("#new_bank_code").prop("readonly", false);
                    // $("#new_bank_name").prop("readonly", false);
                }
                var val = $(this).val();
                $('#bank_checked').val(val);
            });
            // *******End Keep Same******* //

            // *******Hide Keep Same******* //
            $('#new_aadhar_no').on('keyup', function() {
                // alert($(this).val());
                if ($('#old_aadhar').val() != $(this).val()) {
                    $('#aadharCheckDiv').hide();
                } else {
                    $('#aadharCheckDiv').show();
                }
            });
            $('#bank_ifsc_code').on('keyup', function() {
                if ($('#old_bank_ifsc').val() != $(this).val()) {
                    $('#bankCheckDiv').hide();
                } else {
                    $('#bankCheckDiv').show();
                }
            });
            $('#new_bank_code').on('keyup', function() {
                if ($('#old_bank_code').val() != $(this).val()) {
                    $('#bankCheckDiv').hide();
                } else {
                    $('#bankCheckDiv').show();
                }
            }); //new_mobile_no
            $('#new_mobile_no').on('keyup', function() {
                if ($('#old_mobile').val() != $(this).val()) {
                    $('#mobileCheckDiv').hide();
                } else {
                    $('#mobileCheckDiv').show();
                }
            });
            // *******End Hide Keep Same******* //

            // *******Fetch Bank Details******* //
            // $('#bank_ifsc_code').blur(function() {
            //     $ifsc_data = $.trim($('#bank_ifsc_code').val());
            //     $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
            //     if ($ifscRGEX.test($ifsc_data)) {
            //         $('#bank_ifsc_code').removeClass('has-error');
            //         $('#error_bank_ifsc_code').text('');
            //         $('#error_new_bank_name').html('<img  src="{{ asset('images / ZKZg.gif ') }}" width="50px" height="50px"/>');
            //         $('#error_new_bank_branch').html('<img  src="{{ asset('images / ZKZg.gif ') }}" width="50px" height="50px"/>');
            //         $.ajax({
            //             type: 'POST',
            //             url: '{{ url('legacy / getBankDetails ') }}',
            //             data: {
            //                 ifsc: $ifsc_data,
            //                 _token: '{{ csrf_token() }}',
            //             },
            //             success: function(data) {
            //                 if (!data || data.length === 0) {
            //                     $('#error_bank_ifsc_code').text('No data found with the IFSC');
            //                     $('#bank_ifsc_code').addClass('has-error');
            //                     return;
            //                 }
            //                 data = JSON.parse(data);
            //                 // console.log(data);
            //                 $('#new_bank_name').val(data.bank);
            //                 $('#new_bank_branch').val(data.branch);
            //                 $('#error_new_bank_name').html('');
            //                 $('#error_new_bank_branch').html('');
            //                 $('#faulty_form_same #old_bank_ifsc').val($ifsc_data);
            //             },
            //             error: function(ex) {
            //                 alert(sessiontimeoutmessage);
            //                 window.location.href = base_url;
            //             }
            //         });

            //     } else {
            //         $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
            //         $('#bank_ifsc_code').addClass('has-error');
            //     }
            // });



            $('#bank_ifsc_code').blur(function () {
            var $ifsc_data = $.trim($('#bank_ifsc_code').val());
            var $ifscRGEX = /^[a-zA-Z]{4}0[a-zA-Z0-9]{6}$/;
            if ($ifscRGEX.test($ifsc_data)) {
                $('#bank_ifsc_code').removeClass('has-error');
                $('#error_bank_ifsc_code').text('');
                $('#error_name_of_bank').html('<img src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');
                $('#error_bank_branch').html('<img src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');

                $.ajax({
                    type: 'POST',
                    url: '{{ url('legacy/getBankDetails') }}',
                    data: {
                        ifsc: $ifsc_data,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        if (!data || data.length === 0) {
                                $('#error_bank_ifsc_code').text('No data found with the IFSC');
                                $('#bank_ifsc_code').addClass('has-error');
                                return;
                            }
                            data = JSON.parse(data);
                            // console.log(data);
                            $('#new_bank_name').val(data.bank);
                            $('#new_bank_branch').val(data.branch);
                            $('#error_new_bank_name').html('');
                            $('#error_new_bank_branch').html('');
                            $('#faulty_form_same #old_bank_ifsc').val($ifsc_data);
                    },
                    error: function () {
                        alert(sessiontimeoutmessage);
                        window.location.href = base_url;
                        $('#error_bank_ifsc_code').text('Data fetch error');
                        $('#bank_ifsc_code').addClass('has-error');
                        $('#error_name_of_bank').html('');
                        $('#error_bank_branch').html('');
                    }
                });
            } else {
                $('#error_bank_ifsc_code').text('IFSC format invalid, please check the code');
                $('#bank_ifsc_code').addClass('has-error');
            }
        });



            // *******End Fetch Bank Details******* //
            $('.modal-submitapprove').on('click', function() {
                // alert('Hlw');
                $(".modal-submitapprove").hide();
                $("#submittingapprove").show();

                var error_new_aadhar_no = '';
                var error_new_aadhar_doc = '';
                var error_bank_ifsc_code = '';
                var error_new_bank_code = '';
                var error_new_bank_doc = '';
                var error_new_mobile_no = '';
                var dup_bank = $('#dup_bank').val();
                var dup_mobile = $('#dup_mobile').val();
                var dup_aadhar = $('#dup_aadhar').val();
                var no_aadhar = $('#no_aadhar').val();
                var no_mobile = $('#no_mobile').val();
                var aadhar_doc = $('#aadhar_doc').val();
                var dup_aadhar = $('#dup_aadhar').val();

                if (dup_bank == 1) {
                    if ($.trim($('#bank_ifsc_code').val()).length == 0) {
                        error_bank_ifsc_code = 'IFSC is required';
                        $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
                        $('#bank_ifsc_code').addClass('has-error');
                    } else {
                        error_bank_ifsc_code = '';
                        $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
                        $('#bank_ifsc_code').removeClass('has-error');
                    }

                    if ($.trim($('#new_bank_code').val()).length == 0) {
                        error_new_bank_code = 'Bank Code is required';
                        $('#error_new_bank_code').text(error_new_bank_code);
                        $('#new_bank_code').addClass('has-error');
                    } else {
                        error_new_bank_code = '';
                        $('#error_new_bank_code').text(error_new_bank_code);
                        $('#new_bank_code').removeClass('has-error');
                    }

                    if ($.trim($('#new_bank_doc').val()).length == 0) {
                        error_new_bank_doc = 'Bank Docuemnt is required';
                        $('#error_new_bank_doc').text(error_new_bank_doc);
                        $('#new_bank_doc').addClass('has-error');
                    } else {
                        error_new_bank_doc = '';
                        $('#error_new_bank_doc').text(error_new_bank_doc);
                        $('#new_bank_doc').removeClass('has-error');
                    }
                }
                if (dup_mobile == 1 || no_mobile == 1) {
                    if ($.trim($('#new_mobile_no').val()).length == 0) {
                        error_new_mobile_no = 'Mobile is required';
                        $('#error_new_mobile_no').text(error_new_mobile_no);
                        $('#new_mobile_no').addClass('has-error');
                    } else {
                        error_new_mobile_no = '';
                        $('#error_new_mobile_no').text(error_new_mobile_no);
                        $('#new_mobile_no').removeClass('has-error');
                    }
                }
                if (dup_aadhar == 1 || no_aadhar == 1) {
                    if ($.trim($('#new_aadhar_no').val()).length == 0) {
                        error_new_aadhar_no = 'Aadhar is required';
                        $('#error_new_aadhar_no').text(error_new_aadhar_no);
                        $('#new_aadhar_no').addClass('has-error');
                    } else {
                        error_new_aadhar_no = '';
                        $('#error_new_aadhar_no').text(error_new_aadhar_no);
                        $('#new_aadhar_no').removeClass('has-error');
                    }

                    if ($.trim($('#new_aadhar_doc').val()).length == 0) {
                        error_new_aadhar_doc = 'Aadhar Document is required';
                        $('#error_new_aadhar_doc').text(error_new_aadhar_doc);
                        $('#new_aadhar_doc').addClass('has-error');
                    } else {
                        error_new_aadhar_doc = '';
                        $('#error_new_aadhar_doc').text(error_new_aadhar_doc);
                        $('#new_aadhar_doc').removeClass('has-error');
                    }
                }

                if(dup_aadhar == 1 && aadhar_doc == 0)
                    {
                        if ($.trim($('#new_aadhar_doc').val()).length == 0) {
                        error_new_aadhar_doc = 'Aadhar Document is required';
                        $('#error_new_aadhar_doc').text(error_new_aadhar_doc);
                        $('#new_aadhar_doc').addClass('has-error');
                    } else {
                        error_new_aadhar_doc = '';
                        $('#error_new_aadhar_doc').text(error_new_aadhar_doc);
                        $('#new_aadhar_doc').removeClass('has-error');
                    }
                    }

                if (error_new_aadhar_no != '' || error_new_aadhar_doc != '' || error_bank_ifsc_code != '' ||
                    error_new_bank_code != '' ||
                    error_new_bank_doc != '' || error_new_mobile_no != '') {

                    $('#modalReject').modal('hide');
                    $('#submittingapprove').hide();
                    $('.modal-submitapprove').show();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                    return false;
                } else {
                    // alert('Correct');
                    var new_aadhar = $('#new_aadhar_no').val();
                    var old_aadhar = $('#old_aadhar').val();
                    var new_bank = $('#new_bank_code').val();
                    var old_bank = $('#old_bank_code').val();
                    var new_ifsc = $('#bank_ifsc_code').val();
                    var old_ifsc = $('#old_bank_ifsc').val();
                    var new_bank_branch = $('#new_bank_branch').val();
                    var new_bank_name = $('#new_bank_name').val();
                    var new_mobile = $('#new_mobile_no').val();
                    var old_mobile = $('#old_mobile').val();
                    var isMobileChecked = $('#mobileCheck').is(":checked");
                    var isAadharChecked = $('#aadharCheck').is(":checked");
                    var isBankChecked = $('#bankCheck').is(":checked");
                    // alert($('#aadharCheck').val());
                    // alert(old_aadhar);
                    // alert('Old Bank:'+ old_bank);
                    // $('#new_bank_code').val(new_bank);
                    // $('#new_bank_ifsc').val(new_ifsc);
                    // $('#new_aadhar').val(new_aadhar);
                    // $('#new_mobile').val(new_mobile);
                    // $('#new_bank_name').val(new_bank_name);
                    // $('#new_bank_branch').val(new_bank_branch);

                    if (dup_aadhar == 1 && (new_aadhar == old_aadhar) && isAadharChecked == false) {
                        alert(
                            'Aadhaar details remains same as previous. If you want to same as previous, please click on the Keep Same.');
                        $('#modalReject').modal('hide');
                        $("#submittingapprove").hide();
                        $(".modal-submitapprove").show();
                        return false;
                    } else if (dup_bank == 1 && (new_bank == old_bank) && isBankChecked == false) {
                        alert(
                            'Bank details remains same as previous. If you want to same as previous, please click on the Keep Same.');
                        $('#modalReject').modal('hide');
                        $("#submittingapprove").hide();
                        $(".modal-submitapprove").show();
                        return false;
                    } else if (dup_mobile == 1 && (new_mobile == old_mobile) && isMobileChecked == false) {
                        alert(
                            'Mobile number remains same as previous. If you want to same as previous, please click on the Keep Same.');
                        $('#modalReject').modal('hide');
                        $("#submittingapprove").hide();
                        $(".modal-submitapprove").show();
                        return false;
                    } else {
                        $("#formSubmit").submit();
                    }
                }
            });

            $("#uploadForm").on('submit', function(e) {
                $('#submitButton').hide();
                $('#btn_encolser_loader').show();
                e.preventDefault();
                var form = $('#uploadForm')[0];
                var formData = new FormData(form);
                var ben_id = $("#commonfield #id").val();
                var scheme_id = $("#commonfield #scheme_id").val();
                formData.append('ben_id', ben_id);
                formData.append('scheme_id', scheme_id);
                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = ((evt.loaded / evt.total) * 100);
                                var percentComplete = Math.ceil(percentComplete);
                                $(".progress-bar").width(percentComplete + '%');
                                $(".progress-bar").html(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    dataType: 'json',
                    url: '{{ url('jb_ajax_encloser_entry ') }}',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $(".progress-bar").width('0%');
                        //$('#uploadStatus').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');
                    },
                    error: function(ex) {
                        //console.log(ex);
                        $('#uploadStatus').html(
                            '<p style="color:#EA4335;">File upload failed, please try again.</p>'
                        );
                        $('#btn_encolser_loader').hide();
                        $('#submitButton').show();


                    },
                    success: function(resp) {
                        //console.log(resp);
                        if (resp.return_status == 1) {
                            var id = $("#uploadForm #document_type").val();
                            $('#uploadForm')[0].reset();
                            $('#download_' + id).show();
                            $('#uploadStatus').html(
                                '<p style="color:#28A74B;">File has uploaded successfully!</p>'
                            );
                            //$(".progress-bar").width('0%');

                        } else if (resp.return_status == 0) {
                            $('#uploadStatus').html('<p style="color:#EA4335;">' + resp
                                .return_msg + '</p>');
                        }
                        $('#btn_encolser_loader').hide();
                        $('#submitButton').show();


                    }
                });


            });


            $('#encolser_modal').on('hidden.bs.modal', function(e) {
                $("#uploadForm #document_type").val('');
                $(".progress-bar").html('');

            });
            $('.confirmBtnCaste').click(function() {
                var clickval = $(this).val();
                var application_id = $("#commonfield #id").val();
                var scheme_id = $("#commonfield #scheme_id").val();
                window.location = "changeCastelb?scheme_id=" + scheme_id + "&id=" + application_id +
                    "&type=" + clickval;
            });
        });

        function View_encolser_modal(doc_name, doc_type, application_id) {
            // alert(doc_name);
            $('#encolser_name').html('');
            $('#encolser_content').html('');
            $('#encolser_name').html(doc_name + '(' + application_id + ')');
            $('#encolser_content').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');

            var url = '{{ url('ajaxGetEncloser ') }}';

            //alert(url);
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    doc_type: doc_type,
                    scheme_id: {{ $scheme_id  }},
                    application_id: application_id,
                    _token: '{{ csrf_token() }}',
                },
            }).done(function(data, textStatus, jqXHR) {
                if (data.return_status) {
                    $('#encolser_content').html('');
                    $('#encolser_content').html(data.htmlText);
                    $('.ben_doc_button').attr('disabled', false);
                    // $('.ben_reject_button').attr('disabled',false); 
                    $('.ben_reject_button').attr('disabled', false);
                    $("#encolser_modal").modal();
                } else {
                    alert(data.return_msg);
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                $('.ben_doc_button').attr('disabled', false);
                //$('.ben_reject_button').attr('disabled',false); 
                $('.ben_reject_button').attr('disabled', false);
                $('#encolser_content').html('');
                alert(sessiontimeoutmessage);
                window.location.href = base_url;
            });
        }
    </script>
</body>

</html>