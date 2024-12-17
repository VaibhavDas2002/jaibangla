@extends('NoAadhaar.base')
@section('action-content')
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

            /*.row{
      margin-right: 0px!important;
      margin-left: 0px!important;
    }
    .section1{
        border: 1.5px solid #9187878c!important;
        margin: 0.25cm!important;
        padding: 0.25cm!important;
        page-break-inside : avoid;
    }
    .color1{
      margin: 0%!important;
      background-color: #5f9ea061!important;
      -webkit-print-color-adjust: exact;
    }
    .modal_field_name{
      float:left!important;
      font-weight: 700!important;
      margin-right:0.5cm!important;

    }

    .modal_field_value{
      padding-top:0.30cm!important;

    }
    .color1{
      margin: 0%!important;
      background-color: #7fffd4!important;
     -webkit-print-color-adjust: exact;
    }

    .modal-header{
      background-color: #7fffd4!important;
     -webkit-print-color-adjust: exact;
    }
    #divToPrint{
    }*/
        }
    </style>
    <section>
        <div class="modal-fade" tabindex="-1" role="document">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="example-screen">
                        <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                        <!-- <span aria-hidden="true">&times;</span> -->
                        <!-- </button> -->

                        <a href="noaadharlist?scheme_id={{ $scheme_id }}">
                            <img width="50px;" style="pull-right" src="{{ asset('images/back.png') }}"
                                alt="Back" /></a>
                    </div>
                    <div class="modal-body">
                        <div class='row'>
                            <div>
                                @if (($message = Session::get('success')) && ($id = Session::get('id')))
                                    <div class="alert alert-success alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>{{ $message }} with Beneficiary ID: {{ $id }}</strong>


                                    </div>
                                @endif
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger alert-block">
                                        <ul>
                                            @foreach ($errors as $error)
                                                <li><strong> {{ $error }}</strong></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>


                            <!-- We display the details entered by the user here -->
                            <div class="section1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 style="text-align: center; color:red;">Beneficiary ID:{{ $id }}

                                        </h3>
                                    </div>


                                </div>

                                <div class="row color1">
                                    <div class="col-md-12">
                                        <h3>Personal Details</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div><strong>Name :</strong> {{ $row->ben_fname }} {{ $row->ben_mname }}
                                            {{ $row->ben_lname }}</div>
                                    </div>
                                </div>




                                <div class="row">


                                    @if (!is_null($row->dob))
                                        <div class="col-md-6">
                                            <div><strong>Date of Birth (DD-MM-YYYY):</strong>
                                                {{ date('d/m/Y', strtotime($row->dob)) }}</div>

                                        </div>
                                    @endif







                                    <div class="col-md-6">
                                        <div><strong>Father's Name :</strong> {{ $row->father_fname }}
                                            {{ $row->father_mname }} {{ $row->father_lname }}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <div><strong>Mother's Name :</strong> {{ $row->mother_fname }}
                                            {{ $row->mother_mname }} {{ $row->mother_lname }}</div>
                                    </div>



                                    <div class="col-md-6">
                                        <div><strong>Digital Ration Card No. :</strong> 
                                            {{trim($row->ration_card_cat)}}-{{trim($row->ration_card_no)}}</div>
                                    </div>

















                                </div>



                                @if (!empty($old_aadhar))
                                    <div class="row">
                                        <div class="col-md-12 color1" style="margin:10px 0px">
                                            <h3>Existing Aadhaar Details</h3>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div><strong>Aadhaar Number:</strong> {{ trim($old_aadhar) }}</div>

                                    </div>
                                @endif

                                @if (!empty($new_aadhar))
                                    <div class="row">
                                        <div class="col-md-12 color1" style="margin:10px 0px">
                                            <h3>New Aadhaar Details</h3>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div><strong>Aadhaar Number:</strong> {{ trim($new_aadhar) }}</div>

                                    </div>
                                @endif






                            </div>
                            @if ($designation_id_old == 'Approver' && $row->aadhar_edit_role_id == 1)
                                <div class="row">
                                    <div class="col-md-12" style="margin:10px 0px">
                                        <h3>{{ $doc_man->doc_name }}</h3>
                                    </div>
                                </div>
                                @if (strtolower($ext) == 'jpg' ||
                                        strtolower($ext) == 'jpeg' ||
                                        strtolower($ext) == 'jfif' ||
                                        strtolower($ext) == 'png' ||
                                        strtolower($ext) == 'gif')
                                    <div class="col-md-12">
                                        <a class="example-image-link" data-lightbox="example-1">
                                            <img class="example-image" src="{{ $image }}" alt="image-1"
                                                width="250" height="380" /></a>
                                    </div>
                        </div>
                    @elseif(strtolower($ext) == 'pdf')
                        <div class="col-md-12">
                            <a id="link"
                                href="{{ route('jbDownload', ['beneficiary_id' => $id, 'created_by_dist_code' => $row->created_by_dist_code, 'scheme_id' => $scheme_id, 'document_type' => $doc_type_id]) }}"
                                target="_blank" style="color: #4324ef" width="">Download PDF Document</a>



                        </div>
                        <br />
                        @endif
                        <br />
                        <div style="text-align: center; align-content: center;">
                            <button type="button" id="confirm" value="Approve" class="btn btn-success btn-lg confirm">Approve
                            </button>&nbsp;&nbsp;&nbsp;
                            <button type="button" id="back_to_verifier" value="Back TO Verifer" class="btn btn-danger btn-lg back_to_verifier float-right">Back To Verifier
                            </button>
                        </div>
                        
                        @endif

                        @if ($designation_id_old == 'Verifier' || ($designation_id_old == 'Approver' && ($scheme_id == '8' || $scheme_id == '9')))
                            <form method="post" id="register_form" action="{{ url('noaadharPost') }}"
                                enctype="multipart/form-data" class="submit-once" onsubmit="return client_validation()">

                                <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}" />
                                <input type="hidden" name="first_name" id="first_name" value="{{ $row->ben_fname }}" />
                                <input type="hidden" name="middle_name" id="middle_name" value="{{ $row->ben_mname }}" />
                                <input type="hidden" name="last_name" id="last_name" value="{{ $row->ben_lname }}" />
                                <input type="hidden" name="dob" id="dob" value="{{ $row->dob }}" />
                                <input type="hidden" name="id" id="id" value="{{ $id }}" />

                                {{ csrf_field() }}

                                <div id="new_info_div">
                                    <div class="row">
                                        <div class="col-md-12 color1" style="margin:10px 0px">
                                            <h3>Upload Aadhaar Details</h3>
                                        </div>
                                    </div>

                                    <div class="row">


                                        <div class="form-group col-md-6">
                                            <label class="required-field">Aadhaar Number</label>
                                            <input type="text" name="aadhaar_no" id="aadhaar_no"
                                                class="form-control NumOnly" placeholder="Aadhaar No."
                                                value="{{ trim($decrypt_aadhar_old) }}" maxlength='12' />
                                            <span id="error_aadhaar_no" class="text-danger"></span>
                                        </div>








                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="required-field">{{ $doc_man['doc_name'] }}</label>
                                            <input type="file" name="doc_{{ $doc_man['id'] }}"
                                                id="doc_{{ $doc_man['id'] }}" class="form-control" tabindex="1" />
                                            <div class="imageSize">(Image type must be {{ $doc_man['doc_type'] }} and
                                                image size max {{ $doc_man['doc_size_kb'] }}KB)</div>
                                            <span id="error_doc_{{ $doc_man['id'] }}" class="text-danger"></span>
                                        </div>
                                    </div>
                                </div>

                                <center> <button type="submit" id="submit" value="Submit"
                                        class="btn btn-success success btn-lg modal-submit">Submit </button>
                                    <button type="button" id="submitting" value="Submit" class="btn btn-danger btn-lg"
                                        disabled style="display:none;">Submitting please wait</button>
                                </center>
                    </div>


                    </form>
                    @endif

                    <div class="row">

                    </div>


                </div>













            </div>


        </div>




        </div>


        </div>


        </div>
        <div id="modalConfirm" class="modal fade">

            <form method="post" id="approval_form" action="{{ url('BulkApprovenoaadhar') }}" class="submit-once">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="text" name="action_type" id="action_type" value="" />
                <input type="text" id="approvalcheck" name="approvalcheck[]" value="{{ $id }}">
                {{-- <input type="text" id="backToVerifier" name="backToVerifier" value="{{ $id }}"> --}}
                <input type="text" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}" />

                <div class="modal-dialog modal-confirm">
                    <div class="modal-content">
                        <div class="modal-header flex-column">


                        </div>
                        <div class="modal-body">
                            <h4 class="modal-title w-100">Do you really want to <span
                                    id="verify_revert_reject">Approve</span>?</h4>


                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-info" id="confirm_yes">OK</button>
                            <button type="button" id="submittingapprove" value="Submit" class="btn btn-success btn-lg"
                                disabled>Submitting please wait</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script src="{{ asset('/bower_components/AdminLTE/dist/js/app.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $("#submitting").hide();
        $("#submittingapprove").hide();
        $(".NumOnly").keyup(function(event) {

            $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
        var acc_validated_aadhar = '{{ $row->acc_validated_aadhar }}';
        if (acc_validated_aadhar == '-1') {
            $("#new_is_required").val(1);
        } else {
            $("#new_is_required").val(0);
        }

        $(document).on('change', '#process_type', function() {
            var processVal = this.value;
            if (processVal == 1) {
                $('#new_info_div').hide();
                $('#aadhaar_no').val('');
                $("#new_is_required").val(0);
            } else if (processVal == 2) {
                $('#new_info_div').show();
                $('#remarks').val('');
                $("#new_is_required").val(1);
            } else if (processVal == 3) {
                $('#new_info_div').hide();
                $('#aadhaar_no').val('');
                $("#new_is_required").val(0);
            } else {

            }
        });
        $('.confirm').click(function() {
            $("#action_type").val('');
            var button_val = $(this).val();
            //console.log(button_val);
            $('#verify_revert_reject').text(button_val);
            if (button_val == 'Approve') {
                $("#action_type").val(1);
            }

            $('#modalConfirm').modal();
        });

        $('.back_to_verifier').click(function() {
            $("#action_type").val('');
            // var ben_id = $("#beneficiaryId").val();
            var button_val = $(this).val();
            // alert(ben_id);
            $('#verify_revert_reject').text(button_val);
            if (button_val == 'Back TO Verifer') {
                $("#action_type").val(9);
            }
            $('#modalConfirm').modal();
        });
        $('#confirm_yes').on('click', function() {
            $("#confirm_yes").hide();
            $("#submittingapprove").show();
            $("#approval_form").submit();


        });

    });

    function client_validation() {
        var error_process_type = '';
        var error_doc_6 = '';
        var aadhaar_no = $.trim($('#aadhaar_no').val());
        var doc_6 = $('#doc_6').val();
        if (aadhaar_no == "") {
            error_aadhaar_no = 'Aadhaar Number is required';
            $('#error_aadhaar_no').text(error_aadhaar_no);
            $('#aadhaar_no').addClass('has-error');
        } else {
            if (aadhaar_no.length != 12) {
                valid_aadhar = 0;
                error_aadhar_no = 'Aadhar No should be 12 digit ';
                $('#error_aadhar_no').text(error_aadhar_no);
                $('#aadhar_no').addClass('has-error');
            } else {

                var aadhar_valid = validate_adhar(aadhaar_no);
                // aadhar_valid=1;
                if (aadhar_valid) {
                    valid_aadhar = 1;
                    error_aadhaar_no = '';
                    $('#error_aadhaar_no').text(error_aadhaar_no);
                    $('#aadhar_no').removeClass('has-error');
                } else {
                    valid_aadhar = 0;
                    error_aadhaar_no = 'Invalid Aadhar No.';
                    $('#error_aadhaar_no').text(error_aadhaar_no);
                    $('#aadhar_no').addClass('has-error');
                }

            }
            if (valid_aadhar == 1) {
                error_aadhaar_no = '';
                $('#error_aadhaar_no').text(error_aadhaar_no);
                $('#aadhar_no').removeClass('has-error');
            }
        }

        if (doc_6 == "") {
            error_doc_6 = 'Copy of New  Aadhar Card Required';
            $('#error_doc_6').text(error_doc_6);
            $('#doc_6').addClass('has-error');
        } else {
            error_doc_6 = '';
            $('#error_doc_6').text(error_doc_6);
            $('#doc_6').removeClass('has-error');
        }


        if (error_aadhaar_no == '' && error_doc_6 == '') {

            //console.log(y_n);

            $("#submit").hide();
            $("#submitting").show();
            return true;

        } else {
            return false;
        }
    }
</script>
