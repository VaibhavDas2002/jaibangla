<style>
    /* Zoom effect on modal hover */
.enclosure-modal-dialog {
    transition: transform 0.3s ease-in-out;
}

.enclosure-modal-dialog:hover {
    transform: scale(1.05); /* Slight zoom effect */
}

/* Optional: Smooth hover effect for images inside modal */
.enclosure-modal-content img {
    transition: transform 0.3s ease-in-out;
}

.enclosure-modal-content img:hover {
    transform: scale(1.1); /* Zoom in the image slightly */
}

/* General button styling */
.update-btn, .reject-btn {
    min-width: 140px;  /* Consistent button width */
    font-size: 18px;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
    margin: 5px 10px; /* Added spacing between buttons */
}

/* Space between buttons */
.btn-spacing {
    display: inline-block;
    width: 20px; /* Adjust spacing as needed */
}

/* Update Button */
.update-btn {
    background-color: #28a745; /* Green */
    border: none;
    color: white;
}

.update-btn:hover {
    background-color: #218838; /* Darker Green */
    transform: scale(1.05);
}

/* Reject Button */
.reject-btn {
    background-color: #dc3545; /* Red */
    border: none;
    color: white;
}

.reject-btn:hover {
    background-color: #c82333; /* Darker Red */
    transform: scale(1.05);
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

</style>
@extends('layouts.app-template-datatable_new')
@section('content')
    <div class="content-wrapper">
        <section class="content">

        <div class="col-md-12">
            @if ($message = Session::get('error'))
                <div class="callout callout-danger">
                    <h4><i class="fa fa-times-circle"></i> Error!</h4>
                    <p>{{ $message }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="callout callout-danger">
                    <h4><i class="fa fa-exclamation-triangle"></i> Validation Errors!</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li><strong>{{ $error }}</strong></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>


        <div class="row">
            @if (!empty($field_arrays) && count($field_arrays) > 0)
                <div class="col-md-12">
                    <div class="callout callout-danger">
                        <h4><i class="fa fa-exclamation-triangle"></i> Incomplete Details</h4>
                        <p>Please complete the following missing details:</p>
                        <ul class="list-unstyled">
                            @foreach ($field_arrays as $field_array)
                                <li><i class="fa fa-times-circle"></i> <strong>{{ $field_array }}</strong></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        @if($row->is_bank_failed == 1)
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-danger text-center">
                        <h4 class="mb-3">
                            <i class="fa fa-exclamation-circle"></i> Payment Transaction Failed!
                        </h4>

                        @php
                            $paymentSources = [
                                3 => 'SBI',
                                4 => 'RBI',
                                5 => 'IFMS'
                            ];
                        @endphp

                        @if(!empty($payemntModel) && array_key_exists($payemntModel->pay_validated, $paymentSources))
                            <p class="lead">
                                Transaction failed from <strong>{{ $paymentSources[$payemntModel->pay_validated] }}</strong>.
                            </p>
                        @endif

                        @if($invalid_status)
                            <hr>
                            <h4 class="mt-3">
                                <i class="fa fa-times-circle"></i> Failed Reason:
                            </h4>
                            <p ><strong>{{ $invalid_status }}</strong></p>
                        @endif
                    </div>
                </div>
            </div>
        @endif






        @if($canBankupdate != 1)
            <div class="callout callout-danger">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="fa fa-ban"></i> Alert!</h4>
                <p><strong>Bank details cannot be updated/modified.</strong></p>
            </div>
        @endif


            <div class="tab-content" style="margin-top:16px;">
                <div class="tab-pane active" id="beneficiary_details">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4><b>Beneficiary Details </b></h4>
                        </div>
                        <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="text-align: center; color:rgb(18, 219, 62);">Beneficiary ID:{{ $row->id }}
                                                <a href="{{ route('incomplete-details-verifier-view') }}"><img width="50px;" style="pull-right ;"
                                                        src="{{ asset('images/back.png') }}" alt="Back" /></a>
                                            </h3>
                                        </div>
                                    </div>

                        <div class="panel-body">
                            @include('pension-details-view.personal_details')
                            @include('pension-details-view.personal_identification')
                            @include('pension-details-view.bank_details')
                            @include('pension-details-view.contact_details')
                            @include('pension-details-view.enclosure_list')
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4><b>Update Beneficiary Incomplete Details </b></h4>
                        </div>
                        <div class="panel-body">
                            <form method="post" name="formSubmit" id="formSubmit"
                                action="{{ route('updateBeneficiaryDetails') }}" class="submit-once"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" id="id" value="{{ $row->id }}" />
                                <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $row->scheme_id }}" />
                                <input type="hidden" name="old_aadhar" id="old_aadhar" value="{{ $row->aadhar_no }}" />
                                <input type="hidden" name="old_bank_code" id="old_bank_code"
                                    value="{{ trim($row->bank_code) }}" />
                                <input type="hidden" name="old_bank_ifsc" id="old_bank_ifsc"
                                    value="{{ trim($row->bank_ifsc) }}" />
                                <input type="hidden" name="old_bank_name" id="old_bank_name"
                                    value="{{ $row->bank_name }}" />
                                <input type="hidden" name="old_bank_branch" id="old_bank_branch"
                                    value="{{ $row->branch_name }}" />
                                <input type="hidden" name="old_mobile_no" id="old_mobile_no"
                                    value="{{ $row->mobile_no }}" />
                                <input type="hidden" name="dup_bank" id="dup_bank" value="{{ $row->dup_bank }}">
                                <input type="hidden" name="dup_aadhar" id="dup_aadhar" value="{{ $row->dup_aadhar }}">
                                <input type="hidden" name="dup_mobile" id="dup_mobile" value="{{ $row->dup_mobile }}">
                                <input type="hidden" name="no_mobile" id="no_mobile" value="{{ $row->no_mobile }}">
                                <input type="hidden" name="no_aadhar" id="no_aadhar" value="{{ $row->no_aadhar }}">
                                <input type="hidden" name="is_incomplete" id="is_incomplete"
                                    value="{{ $row->is_incomplete }}">
                                <input type="hidden" name="is_bank_failed" id="is_bank_failed"
                                    value="{{ $row->is_bank_failed }}">


                                <!-- No Aadhar and Dup Aadhar  -->

                                @if (intval($row->dup_aadhar ?? 0) == 1 || intval($row->no_aadhar ?? 0) == 1)
                                    <div class="panel panel-default">
                                        <div class="panel-heading" id="panel_head"
                                            style="font-size: 14px; font-weight: bold; font-style: italic;">New Aadhar Details
                                        </div>
                                        <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                            <div class="row">

                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Aadhaar Number</label>
                                                    <input type="text" name="new_aadhar_no" id="new_aadhar_no"
                                                        class="form-control NumOnly" placeholder="Aadhar No." maxlength="12"
                                                        value="{{ trim($row->aadhar_no ?? '') }}" />
                                                    <span id="error_new_aadhar_no" class="text-danger"></span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label
                                                        class="@if (intval($row->no_aadhar ?? 0) == 1) required-field @endif">Aadhaar
                                                        Document</label>
                                                    <input type="file" name="new_aadhar_doc" id="new_aadhar_doc"
                                                        class="form-control" />
                                                    <span id="error_new_aadhar_doc" class="text-danger"></span>
                                                </div>

                                                @if (!empty($getAadharDoc) && $getAadharDoc > 0)
                                                    <div class="form-group col-md-2" style="margin-top: 30px;">
                                                        <button class="btn btn-warning btn-sm aadhar_doc_button"
                                                            id="bankDoc_{{ $row->id ?? '' }}" value="{{ $row->id ?? '' }}">View
                                                            Existing Aadhaar Copy</button>
                                                    </div>
                                                @endif

                                                @if (intval($row->dup_aadhar ?? 0) == 1)
                                                    <div class="form-group col-md-2" style="margin-top: 30px;" id="aadharCheckDiv">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                id="aadharCheck">
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

                                <!-- No Mobile and Duplicate Mobile -->
                                @if (intval($row->dup_mobile) == 1 || intval($row->no_mobile) == 1 || intval($row->is_bank_failed == 1))
                                    <div class="panel panel-default">
                                        <div class="panel-heading" id="panel_head"
                                            style="font-size: 14px; font-weight: bold; font-style: italic;">New Mobile Details
                                        </div>
                                        <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label
                                                        class="@if (intval($row->no_mobile) == 1) required-field @endif">Mobile
                                                        Number</label>
                                                    <input type="text" id="new_mobile_no" name="new_mobile_no"
                                                        class="form-control NumOnly" placeholder="Mobile No" maxlength="10"
                                                        value="{{ trim($row->mobile_no) }}">
                                                    <span id="error_new_mobile_no" class="text-danger"></span>
                                                </div>
                                                @if (intval($row->dup_mobile == 1))
                                                    <div class="form-group col-md-4" style="margin-top: 30px;" id="mobileCheckDiv">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="3"
                                                                id="mobileCheck">
                                                            <label class="form-check-label" for="mobileCheck">
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


                                @if (intval($row->dup_bank) == 1 || in_array(intval($row->is_bank_failed), [1, 2, 3]))
                                    <div class="panel panel-default">
                                        <div class="panel-heading" id="panel_head"
                                            style="font-size: 14px; font-weight: bold; font-style: italic;">
                                            New Bank Details
                                        </div>
                                        <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="required-field">IFSC</label>
                                                    <input type="text" name="new_bank_ifsc" id="new_bank_ifsc"
                                                        class="form-control" autocomplete="off" placeholder="IFSC Code"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        value="{{ trim($row->bank_ifsc ?? '') }}" @if(isset($canBankupdate) && $canBankupdate != 1) readonly @endif />
                                                    <span id="error_new_bank_ifsc" class="text-danger"></span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Bank Branch</label>
                                                    <input type="text" name="new_bank_branch" id="new_bank_branch"
                                                        class="form-control NumOnly" placeholder="Bank Branch"
                                                        value="{{ trim($row->branch_name ?? '') }}" readonly />
                                                    <span id="error_new_bank_branch" class="text-danger"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Bank Name</label>
                                                    <input type="text" name="new_bank_name" id="new_bank_name"
                                                        class="form-control" placeholder="Bank Name"
                                                        value="{{ trim($row->bank_name ?? '') }}" readonly />
                                                    <span id="error_new_bank_name" class="text-danger"></span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Bank Account</label>
                                                    <input type="text" name="new_bank_code" id="new_bank_code"
                                                        class="form-control NumOnly" placeholder="Bank Account"
                                                        value="{{ trim($row->bank_code ?? '') }}" @if(isset($canBankupdate) && $canBankupdate != 1) readonly @endif />
                                                    <span id="error_new_bank_code" class="text-danger"></span>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Confirm Bank Account</label>
                                                    <input type="text" name="new_confirm_bank_code" id="new_confirm_bank_code"
                                                        class="form-control NumOnly" placeholder="Confirm Bank Account"
                                                        value="{{ trim($row->bank_code ?? '') }}" @if(isset($canBankupdate) && $canBankupdate != 1) readonly @endif />
                                                    <span id="error_new_confirm_bank_code" class="text-danger"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="required-field">Passbook Document</label>
                                                    <input type="file" name="new_bank_doc" id="new_bank_doc"
                                                        class="form-control" />
                                                    <span id="error_new_bank_doc" class="text-danger"></span>
                                                </div>
                                                @if(!empty($getBankDoc) && $getBankDoc > 0)
                                                    <div class="form-group col-md-2" style="margin-top: 30px;">
                                                        <button class="btn btn-warning btn-sm bank_doc_button"
                                                            id="bankDoc_{{ $row->id }}" value="{{ $row->id }}">View
                                                            Passbook</button>
                                                    </div>
                                                @endif
                                                @if (!empty($row->dup_bank) && intval($row->dup_bank) == 1)
                                                    <div class="form-group col-md-2" style="margin-top: 30px;" id="bankCheckDiv">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="2"
                                                                id="bankCheck">
                                                            <label class="form-check-label" for="bankCheck">
                                                                Keep Same
                                                            </label>
                                                        </div>
                                                        <span id="error_new_bank_code" class="text-danger"></span>
                                                    </div>
                                                @endif
                                            </div>

                                            @if (!empty($row->is_bank_failed) && intval($row->is_bank_failed) == 2)
                                                <div class="row">
                                                    <div style="font-size: 16px; text-align: center;">
                                                        Name Response from Bank:
                                                        <span id="name_response"
                                                            class="text-success"><b>{{ $av_name_response ?? '' }}</b></span>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div id="radio_div">
                                                        <div style="font-size: 15px; font-weight: bold; font-style: italic;"
                                                            class="text-warning" align="center">
                                                            Please select which one you want to process?
                                                        </div>
                                                        <div style="padding: 5px 5px 5px 50px; border: 1px solid whitesmoke; border-radius: 5px; margin: 5px 0px; background-color: whitesmoke;"
                                                            class="row">
                                                            @if (!empty($name_options))
                                                                @foreach ($name_options as $options)
                                                                    <label style="cursor: pointer; margin-bottom: 5px;">
                                                                        <input type="radio" id="process_type" name="process_type"
                                                                            class="process_type_radio" value="{{ $options->id }}">
                                                                        {{ $options->description }}
                                                                    </label><br>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        <span id="error_process_type" class="text-danger"></span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Manabik Edit -->
                                @if (
                                    $row->scheme_id == 2 && isset($row->is_incomplete) && $row->is_incomplete == 1 &&
                                    !empty($required_fields) && is_array($required_fields) &&
                                    !empty(array_intersect([10, 11, 12, 13, 14], $required_fields))
                                )
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading" id="panel_head"
                                                                        style="font-size: 14px; font-weight: bold; font-style: italic;">
                                                                        New Disability Details
                                                                    </div>
                                                                    <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                                                        <div class="row">
                                                                            <div class="form-group col-md-4">
                                                                                <label class="required-field">Type of Disability</label>
                                                                                <select class="form-control" name="new_disability_type" id="new_disability_type">
                                                                                    <option value="">--Select--</option>
                                                                                    @if (!empty(Config::get('constants.disablity_type')))
                                                                                        @foreach(Config::get('constants.disablity_type') as $key => $val)
                                                                                            <option value="{{ $key }}" @if(old('disablity_type') == $key) selected
                                                                                            @endif>
                                                                                                {{ $val }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </select>
                                                                                <span id="error_disablity_type" class="text-danger"></span>
                                                                            </div>

                                                                            <div class="form-group col-md-4">
                                                                                <label class="required-field">Percentage of Disability</label>
                                                                                <input type="text" name="new_disability_type_percentage"
                                                                                    id="new_disability_type_percentage" class="form-control"
                                                                                    placeholder="Percentage" maxlength="5"
                                                                                    value="{{ old('disablity_type_percentage') }}" />
                                                                                <span id="error_disablity_type_percentage" class="text-danger"></span>
                                                                            </div>

                                                                            <div class="form-group col-md-4">
                                                                                <label class="required-field">Authority Name</label>
                                                                                <input type="text" name="new_disability_type_authority"
                                                                                    id="new_disability_type_authority" class="form-control txtOnly"
                                                                                    placeholder="Certifying Authority" maxlength="200"
                                                                                    value="{{ old('disablity_type_authority') }}" />
                                                                                <span id="error_disablity_type_authority" class="text-danger"></span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="form-group col-md-4">
                                                                                <label class="required-field">Authority Designation</label>
                                                                                <input type="text" name="new_disability_designation"
                                                                                    id="new_disability_designation" class="form-control txtOnly"
                                                                                    placeholder="Designation Name" maxlength="200"
                                                                                    value="{{ old('disability_designation') }}" />
                                                                                <span id="error_disability_designation" class="text-danger"></span>
                                                                            </div>

                                                                            <div class="form-group col-md-4">
                                                                                <label class="required-field">Disability Certificate from Appropriate
                                                                                    Authority</label>
                                                                                <input type="file" name="new_disability_doc" id="new_disability_doc"
                                                                                    class="form-control" />
                                                                                <span id="error_new_disability_doc" class="text-danger"></span>
                                                                            </div>

                                                                            @if (!empty($getDisabilityDoc) && $getDisabilityDoc > 0)
                                                                                <div class="form-group col-md-2" style="margin-top: 30px;">
                                                                                    <button class="btn btn-warning btn-sm aadhar_doc_button"
                                                                                        id="bankDoc_{{ $row->id }}" value="{{ $row->id }}">
                                                                                        View Existing Disability Certificate Copy
                                                                                    </button>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                @endif

                                <!-- Widow_edit -->
                                @if ( 
                                    $row->scheme_id == 11 &&  isset($row->is_incomplete) && $row->is_incomplete == 1 
                                && !empty($required_fields) && is_array($required_fields) &&  !empty(array_intersect([26, 9], $required_fields))
                                    )
                                <div class="panel panel-default">
                                    <div class="panel-heading" id="panel_head"
                                        style="font-size: 14px; font-weight: bold; font-style: italic;">
                                        New Husband Death Details
                                    </div>
                                    <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label>Husband's Name</label>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="required-field">First Name</label>
                                                <input type="text" name="new_husband_first_name" id="husband_first_name"
                                                    class="form-control txtOnly" placeholder="First Name" maxlength="200"
                                                    value="{{ old('new_husband_first_name', $row->husband_fname ?? '') }}" tabindex="4" />
                                                <span id="error_husband_first_name" class="text-danger"></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label>Middle Name</label>
                                                <input type="text" name="new_husband_middle_name" id="husband_middle_name"
                                                    class="form-control txtOnly" placeholder="Middle Name" maxlength="100"
                                                    value="{{ old('new_husband_middle_name', $row->husband_mname ?? '') }}" tabindex="5" />
                                                <span id="error_husband_middle_name" class="text-danger"></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="required-field">Last Name</label>
                                                <input type="text" name="new_husband_last_name" id="husband_last_name"
                                                    class="form-control txtOnly" placeholder="Last Name" maxlength="200"
                                                    value="{{ old('new_husband_last_name', $row->husband_lname ?? '') }}" tabindex="6" />
                                                <span id="error_husband_last_name" class="text-danger"></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="required-field">Husband's Death Certificate</label>
                                                <input type="file" name="new_husband_death_doc" id="new_husband_death_doc"
                                                    class="form-control" />
                                                <span id="error_new_husband_death_doc" class="text-danger"></span>
                                            </div>

                                            @if (isset($getHusbandDoc) && $getHusbandDoc > 0)
                                                <div class="form-group col-md-2" style="margin-top: 30px;">
                                                    <button class="btn btn-warning btn-sm husband_doc_button"
                                                        id="bankDoc_{{ $row->id }}" value="{{ $row->id }}">
                                                        View Existing Husband Death Certificate
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif


                        <!-- Johar and Bondhu Edit -->
                        
                        @if ($row->is_incomplete == 1 && in_array($scheme_id, [1,3]) && !empty(array_intersect([3, 4, 18], $required_fields)))
                            <div class="panel panel-default">
                                <div class="panel-heading" id="panel_head"
                                    style="font-size: 14px; font-weight: bold; font-style: italic;">New Caste Certificate Details</div>
                                <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                    <div class="row">

                                        <div class="form-group col-md-4">
                                            <label class="required-field">Caste</label>
                                            <select class="form-control" name="new_caste_category" id="new_caste_category" id="caste_category">
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
                                            </select>
                                            <span id="error_caste_category" class="text-danger"></span>
                                        </div>

                                        <div class="form-group col-md-4" id="caste_certificate_no_section">
                                            <label class="required-field">Caste Certificate No.</label>
                                            <input type="text" name="new_caste_certificate_no" id="new_caste_certificate_no" class="form-control"
                                                placeholder="Caste Certificate No." maxlength="200"
                                                value="{{ $row->caste_certificate_no ?? old('caste_certificate_no')}}" />
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

                        


                        
                        
                        
                        
                        
                    </form>
                </div>
                    </div>

                             <!-- Enclosure Modal -->
                   <div class="modal fade" id="enclosure_modal" tabindex="-1" role="dialog" aria-labelledby="enclosure_name" aria-hidden="true">
                       <div class="modal-dialog modal-lg enclosure-modal-dialog" role="document">
                           <div class="modal-content enclosure-modal-content">
                               <div class="modal-header bg-primary">
                                   <h4 class="modal-title" id="enclosure_name">Modal Title</h4>
                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                       <span aria-hidden="true">&times;</span>
                                   </button>
                               </div>
                               <div class="modal-body text-center">
                                   <div id="enclosure_content">
                                       <!-- <img src="{{ asset('images/loading.gif') }}" width="80px" height="80px" alt="Loading..."/> -->
                                   </div>
                               </div>
                               <div class="modal-footer">
                                   <button type="button" class="btn btn-danger" data-dismiss="modal">
                                       <i class="fa fa-times"></i> Close
                                   </button>
                               </div>
                           </div>
                       </div>
                   </div>


                   <!--Update Model-->
                <div id="modalUpate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel" aria-hidden="true">
                    <div class="modal-dialog modal-confirm modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title w-100 text-center">Confirm Action</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body text-center">
                                <p style="font-size: 18px; color: #fc3903; font-weight: bold;">
                                Are You Want to Update this ID: <strong>{{ $row->id }}</strong>?
                                </p>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer d-flex justify-content-center ">
                                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-info px-4 modal-submitapprove">OK</button>
                                <button type="button" id="submittingapprove" class="btn btn-success px-4" disabled>
                                    Submitting... Please wait
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                                <!--Reject Model-->

                                <div class="modal fade" id="modal-danger" tabindex="-1" role="dialog" aria-labelledby="modal-danger-label" aria-hidden="true">
                                    <form action="{{ route('rejectApplicantDetails') }}" method="post" id="rejectForm">
                                        {{ csrf_field() }}
                                        <input type="hidden" id="id" name="id" value="{{ $row->id }}">
                                        <input type="hidden" id="scheme_id" name="scheme_id" value="{{ $row->scheme_id }}">
                                        <input type="hidden" id="op_type" name="op_type" value="R">

                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                            <div class="modal-header bg-red">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h2 class="modal-title" id="modal-danger-label">Reject Confirmation</h2>
                                            </div>
                                                <div class="modal-body">
                                                <h3>Are you sure you want to reject this applicant with Beneficiary id  {{$row->id}}?</h3> 
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Ok</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>


         
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-success btn-lg update-btn">
                                <i class="fa fa-check-circle"></i> Update
                            </button>
                            
                            <span class="btn-spacing"></span>

                            <button type="button" class="btn btn-danger btn-lg reject-btn" data-toggle="modal" data-target="#modal-danger">
                                <i class="fa fa-times-circle"></i> Reject
                            </button>
                        </div>

                </div>
            </div>
        </section>
    </div>
@endsection


<!-- jQuery 2.1.3 -->
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>
<script>
    $(document).ready(function () {
        $("#submittingapprove").hide();
      
        $(".NumOnly").keyup(function(event) {
                $(this).val($(this).val().replace(/[^\d].+/, ""));
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
        });

            // *******Aadhar document view******* //
            $(document).on('click', '.aadhar_doc_button', function(e) {
                e.preventDefault();
                // $('.aadhar_doc_button').attr('disabled', true);
                var benid = $(this).val();
                View_enclosure_modal('Copy of Aadhar Document', 6, benid);
            });
            // *******Bank Passboook document view******* //
            $(document).on('click', '.bank_doc_button', function(e) {
                e.preventDefault();
                // $('.bank_doc_button').attr('disabled', true);
                var benid = $(this).val();
                View_enclosure_modal('Copy of Bank Passbook', 10, benid);
            });
             // *******Caste Certificate  document view******* //
            $(document).on('click', '.caste_doc_button', function(e) {
                e.preventDefault();
                // $('.caste_doc_button').attr('disabled', true);
                var benid = $(this).val();
                View_enclosure_modal('Copy of Caste Certificate', 3, benid);
            });
             // *******Husband Death document view******* //
            $(document).on('click', '.husband_doc_button', function(e) {
                e.preventDefault();
                // $('.caste_doc_button').attr('disabled', true);
                var benid = $(this).val();
                View_enclosure_modal('Copy of Husband Death Certificate', 105, benid);
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

            $('.process_type_radio').change(function() {
                if ($(this).val() == 1 || $(this).val() == 3) {
                    $("#new_bank_code").prop("readonly", true);
                    $("#new_bank_ifsc").prop("readonly", true);
                    $("#new_confirm_bank_code").prop("readonly", true);
                } else {
                $("#new_bank_code").prop("readonly", false);
                $("#new_bank_ifsc").prop("readonly", false);
                $("#new_confirm_bank_code").prop("readonly", false);
                }
            });


            // *******Aadhar Keep Same******* //
            $('#aadharCheck').click(function() {
                var aadhar_no = $("#new_aadhar_no").val().trim();
                var ben_id = $('#id').val();
                var isChecked = $(this).is(":checked");

                $.ajax({
                    type: 'POST',
                    url: "{{ route('BenaadharDupCheck') }}",
                    data: {
                        aadhar_no: aadhar_no,
                        aadharSameVal: isChecked ? 1 : 0,
                        ben_id: ben_id,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            $('#aadharCheck').prop('checked', false);
                            alert(response.msg);
                        } else {
                            $("#new_aadhar_no").prop("readonly", isChecked);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Session timeout. Please login again.");
                        window.location.href = base_url;
                    }
                });
            });


            $('#bankCheck').click(function() {
                var bank_code = $("#new_bank_code").val().trim();
                var bank_ifsc = $("#new_bank_ifsc").val().trim();
                var ben_id = $('#id').val();
                var isChecked = $(this).is(":checked");

                $.ajax({
                    type: 'POST',
                    url: "{{ route('BenbankDupCheck') }}",
                    data: {
                        bank_code: bank_code,
                        bank_ifsc: bank_ifsc,
                        bankSameVal: isChecked ? 1 : 0,
                        ben_id: ben_id,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            $('#bankCheck').prop('checked', false);
                            alert(response.msg);
                        } else {
                            $("#new_bank_code, #new_bank_ifsc").prop("readonly", isChecked);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Session timeout. Please login again.");
                        window.location.reload();
                    }
                });
            });

            $('#mobileCheck').click(function() {
                var mobile_no = $("#new_mobile_no").val().trim();
                var isChecked = $(this).is(":checked");
                var ben_id = $('#id').val();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('mobileDupCheck') }}",
                    data: {
                        mobile_no: mobile_no,
                        mobileSameVal: isChecked ? 1 : 0,
                        ben_id: ben_id,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            $('#mobileCheck').prop('checked', false);
                            alert(response.msg);
                        } else {
                            $("#new_mobile_no").prop("readonly", isChecked);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Session timeout. Please login again.");
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
                    $("#new_bank_ifsc").prop("readonly", true);
                    $("#new_bank_code").prop("readonly", true);
                } else {
                    $("#new_bank_ifsc").prop("readonly", false);
                    $("#new_bank_code").prop("readonly", false);
                }
                var val = $(this).val();
                $('#bank_checked').val(val);
            });

            // *******Hide Keep Same******* //
            $('#new_aadhar_no').on('keyup', function() {
                // alert($(this).val());
                if ($('#old_aadhar').val() != $(this).val()) {
                    $('#aadharCheckDiv').hide();
                } else {
                    $('#aadharCheckDiv').show();
                }
            });
            $('#new_bank_ifsc').on('keyup', function() {
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
            $('#new_bank_ifsc').blur(function() {
                $ifsc_data = $.trim($('#new_bank_ifsc').val());
                $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
                if ($ifscRGEX.test($ifsc_data)) {
                    $('#new_bank_ifsc').removeClass('has-error');
                    $('#error_new_bank_ifsc').text('');
                    $('#error_new_bank_name').html('<img src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');
                    $('#error_new_bank_branch').html('<img src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');
                    $.ajax({
                        type: 'POST',
                        url: '{{ url('legacy/getBankDetails') }}',
                        data: {
                            ifsc: $ifsc_data,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(data) {
                            // console.log(data);
                            if (!data || data.length === 0 || data == 'null') {
                                // alert('not found');
                                $('#error_new_bank_ifsc').text('No data found with the IFSC');
                                $('#new_bank_ifsc').addClass('has-error');
                                // $('#new_bank_name').val('');
                                // $('#new_bank_branch').val('');
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
                        error: function(ex) {
                            alert(sessiontimeoutmessage);
                            window.location.href = base_url;
                        }
                    });

                } else {
                    $('#error_new_bank_ifsc').text('IFSC format invalid please check the code');
                    $('#new_bank_ifsc').addClass('has-error');
                }
            });


            $('.update-btn').click(function() {
                var designation_id = $("#designation_id").val();
                var new_aadhar = $('#new_aadhar_no').val();
                var old_aadhar = $('#old_aadhar').val();
                var new_bank = $('#new_bank_code').val();
                var old_bank = $('#old_bank_code').val();
                var new_ifsc = $('#new_bank_ifsc').val();
                var old_ifsc = $('#old_bank_ifsc').val();
                var new_bank_branch = $('#new_bank_branch').val();
                var new_bank_name = $('#new_bank_name').val();
                var new_mobile = $('#new_mobile_no').val();
                var old_mobile = $('#old_mobile').val();
                var bank_check = $('#bank_checked').val();
                var aadhar_check = $('#aadhar_checked').val();
                var mobile_check = $('#mobile_checked').val();
                var is_verifier = $('#is_verifier').val();
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
                        $('#modalUpate').modal();
                    }
                
                // $('#modalUpate').modal();
            });


            $(document).ready(function () {
                $('.modal-submitapprove').on('click', function () {
                    // alert('Hlw');
                    $(this).hide();
                    $('#submittingapprove').show();

                    var error_new_aadhar_no = '';
                    var error_new_aadhar_doc = '';
                    var error_new_bank_ifsc = '';
                    var error_new_bank_code = '';
                    var error_new_bank_doc = '';
                    var error_new_mobile_no = '';
                    var dup_bank = $('#dup_bank').val();
                    var dup_mobile = $('#dup_mobile').val();
                    var dup_aadhar = $('#dup_aadhar').val();
                    var no_aadhar = $('#no_aadhar').val();
                    var no_mobile = $('#no_mobile').val();
                    var aadhar_doc = $('#aadhar_doc').val();
                    var is_bank_failed = $('#is_bank_failed').val();

                    if (dup_bank == 1 || is_bank_failed == 1 || is_bank_failed == 2 || is_bank_failed == 3) {
                        if ($.trim($('#new_bank_ifsc').val()).length == 0) {
                            error_new_bank_ifsc = 'IFSC is required';
                            $('#error_new_bank_ifsc').text(error_new_bank_ifsc);
                            $('#new_bank_ifsc').addClass('has-error');
                        } else {
                            $('#error_new_bank_ifsc').text('');
                            $('#new_bank_ifsc').removeClass('has-error');
                        }

                        if ($.trim($('#new_bank_code').val()).length == 0) {
                            error_new_bank_code = 'Bank Code is required';
                            $('#error_new_bank_code').text(error_new_bank_code);
                            $('#new_bank_code').addClass('has-error');
                        } else {
                            $('#error_new_bank_code').text('');
                            $('#new_bank_code').removeClass('has-error');
                        }

                        if ($.trim($('#new_bank_doc').val()).length == 0) {
                            error_new_bank_doc = 'Bank Document is required';
                            $('#error_new_bank_doc').text(error_new_bank_doc);
                            $('#new_bank_doc').addClass('has-error');
                        } else {
                            $('#error_new_bank_doc').text('');
                            $('#new_bank_doc').removeClass('has-error');
                        }
                    }

                    if (dup_mobile == 1 || no_mobile == 1) {
                        if ($.trim($('#new_mobile_no').val()).length == 0) {
                            error_new_mobile_no = 'Mobile is required';
                            $('#error_new_mobile_no').text(error_new_mobile_no);
                            $('#new_mobile_no').addClass('has-error');
                        } else {
                            $('#error_new_mobile_no').text('');
                            $('#new_mobile_no').removeClass('has-error');
                        }
                    }

                    if (dup_aadhar == 1 || no_aadhar == 1 || (dup_aadhar == 1 && aadhar_doc == 0)) {
                        if ($.trim($('#new_aadhar_no').val()).length == 0) {
                            error_new_aadhar_no = 'Aadhar is required';
                            $('#error_new_aadhar_no').text(error_new_aadhar_no);
                            $('#new_aadhar_no').addClass('has-error');
                        } else {
                            $('#error_new_aadhar_no').text('');
                            $('#new_aadhar_no').removeClass('has-error');
                        }

                        if ($.trim($('#new_aadhar_doc').val()).length == 0) {
                            error_new_aadhar_doc = 'Aadhar Document is required';
                            $('#error_new_aadhar_doc').text(error_new_aadhar_doc);
                            $('#new_aadhar_doc').addClass('has-error');
                        } else {
                            $('#error_new_aadhar_doc').text('');
                            $('#new_aadhar_doc').removeClass('has-error');
                        }
                    }

                    if (error_new_aadhar_no != '' || error_new_aadhar_doc != '' || error_new_bank_ifsc != '' ||
                        error_new_bank_code != '' || error_new_bank_doc != '' || error_new_mobile_no != '') {
                        $('#modalReject').modal('hide');
                        $('#submittingapprove').hide();
                        $('.modal-submitapprove').show();
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                        return false;
                    } else {
                        var new_aadhar = $('#new_aadhar_no').val();
                        var old_aadhar = $('#old_aadhar').val();
                        var new_bank = $('#new_bank_code').val();
                        var old_bank = $('#old_bank_code').val();
                        var new_ifsc = $('#new_bank_ifsc').val();
                        var old_ifsc = $('#old_bank_ifsc').val();
                        var new_mobile = $('#new_mobile_no').val();
                        var old_mobile = $('#old_mobile').val();
                        var isMobileChecked = $('#mobileCheck').is(':checked');
                        var isAadharChecked = $('#aadharCheck').is(':checked');
                        var isBankChecked = $('#bankCheck').is(':checked');

                        if (dup_aadhar == 1 && new_aadhar == old_aadhar && !isAadharChecked) {
                            alert('Aadhaar details remain the same. Click Keep Same if unchanged.');
                        } else if (dup_bank == 1 && new_bank == old_bank && !isBankChecked) {
                            alert('Bank details remain the same. Click Keep Same if unchanged.');
                        } else if (dup_mobile == 1 && new_mobile == old_mobile && !isMobileChecked) {
                            alert('Mobile number remains the same. Click Keep Same if unchanged.');
                        } else {
                            $('#formSubmit').submit();
                        }
                    }
                });
            });



    });


    function View_enclosure_modal(doc_name, doc_type, application_id) {
        $('#enclosure_name').html(doc_name + ' (' + application_id + ')');
        $('#enclosure_content').html('<img src="{{ asset('images/loading.gif') }}" width="80px" height="80px" alt="Loading..."/>');

        var url = '{{ url('ajaxGetEncloser') }}';

        $.ajax({
            url: url,
            type: "POST",
            data: {
                doc_type: doc_type,
                scheme_id: {{ $scheme_id }},
                application_id: application_id,
                _token: '{{ csrf_token() }}',
            },
            success: function (data) {
                if (data.return_status) {
                    $('#enclosure_content').html(data.htmlText);
                    $('.ben_doc_button, .ben_reject_button').attr('disabled', false);
                    $("#enclosure_modal").modal('show');
                } else {
                    alert(data.return_msg);
                }
            },
            error: function () {
                $('.ben_doc_button, .ben_reject_button').attr('disabled', false);
                $('#enclosure_content').html('<p class="text-danger">An error occurred. Please try again.</p>');
                alert('Session timeout! Redirecting...');
                window.location.href = base_url;
            }
        });
    }



       

</script>