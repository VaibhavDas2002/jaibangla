<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Jb | Jai Bangla</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link href="{{ asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" />
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" />

    <link href="{{ asset('/bower_components/AdminLTE/dist/css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css') }}" rel="stylesheet"
        type="text/css" />


    <link href="{{ asset('/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet"
        type="text/css" />

    <style>
        .box {
            width: 800px;
            margin: 0 auto;
        }

        .active_tab1 {
            background-color: #fff;
            color: #333;
            font-weight: 600;
        }

        .inactive_tab1 {
            background-color: #f5f5f5;
            color: #333;
            cursor: not-allowed;
        }

        .has-error {
            border-color: #cc0000;
            background-color: #ffff99;
        }

        .select2 {
            width: 100% !important;
        }

        .select2 .has-error {
            border-color: #cc0000;
            background-color: #ffff99;
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

        .row {
            margin-right: 0px !important;
            margin-left: 0px !important;
            margin-top: 1% !important;
        }

        .section1 {
            border: 1.5px solid #9187878c;
            margin: 2%;
            padding: 2%;
        }

        .color1 {
            margin: 0% !important;
            background-color: #5f9ea061;
        }

        .modal-header {
            background-color: #7fffd4;
        }

        .required-field::after {
            content: "*";
            color: red;
        }

        .imageSize {
            font-size: 9px;
            color: #333;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3c8dbc;
            border-color: #367fa9;
            padding: 0px 5px;
            color: #fff;
        }
    </style>


</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->

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
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div> <!-- class="box box-primary" -->


                            <div>
                                @if (!empty($msg))
                                    <div class="alert alert-success alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>{{ $msg }}</strong>


                                    </div>
                                @endif
                                @if ($message = Session::get('error'))
                                    <div class="alert alert-danger alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>{{ $message }}</strong>


                                    </div>
                                @endif

                                @if (!empty($errors))
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors as $error)
                                                <li>
                                                    @if (is_array($error) && isset($error['errors']))
                                                        @foreach ($error['errors'] as $errorMsg)
                                                            {{ $errorMsg }}
                                                        @endforeach
                                                    @else
                                                        {{ $error }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif





                                {{-- @if (count($errors) > 0)
            <div class="alert alert-danger alert-block">
              <ul>
               @foreach ($errors as $error)
               <li><strong> {{ $error }}</strong></li>
               @endforeach
              </ul>
            </div>
            @endif --}}



                                <!--   @if ($message = Session::get('failure'))
<div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                      <strong>{{ $message }}</strong>
              </div>
@endif -->
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form method="post" id="register_form" action="{{ url('upload-user-manual') }}"
                                enctype="multipart/form-data" class="submit-once">
                                {{ csrf_field() }}

                                <div class="tab-content" style="margin-top:16px;">






                                    <div class="tab-pane active" id="personal_details">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4><b>Upload User Manual</b></h4>
                                            </div>
                                            <div class="panel-body">



                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="">
                                                            <label class="required-field">Scheme</label>
                                                            <select name="scheme_id[]" id="scheme_id"
                                                                class="form-control" multiple required>
                                                                <option value="">--Select--</option>
                                                                <option value="all"
                                                                    @if (isset($fill_array['scheme_id']) && is_array($fill_array['scheme_id']) && in_array('all', $fill_array['scheme_id'])) selected @endif>
                                                                    Select All
                                                                </option>
                                                                @foreach ($scheme_arr as $scheme)
                                                                    <option value="{{ $scheme->id }}"
                                                                        @if (isset($fill_array['scheme_id']) &&
                                                                                is_array($fill_array['scheme_id']) &&
                                                                                in_array($scheme->id, $fill_array['scheme_id'])) selected @endif>
                                                                        {{ $scheme->scheme_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span id="error_scheme_id" class="text-danger"></span>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="">
                                                            <label class="required-field">Designation</label>
                                                            <select name="designation_id[]" id="designation_id"
                                                                class="form-control" multiple tabindex="2" required>
                                                                <option value="">--Select--</option>
                                                                <option value="all"
                                                                    @if (isset($fill_array['designation_id']) && in_array('all', $fill_array['designation_id'])) selected @endif>
                                                                    Select All
                                                                </option>
                                                                @foreach ($designation_arr as $designation)
                                                                    <option value="{{ $designation->name }}"
                                                                        @if (isset($fill_array['designation_id']) &&
                                                                                is_array($fill_array['designation_id']) &&
                                                                                in_array($designation->name, $fill_array['designation_id'])) selected @endif>
                                                                        {{ $designation->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span id="error_designation_id" class="text-danger"></span>
                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group" id="divBodyCode">
                                                            <label class="required-field">Manual Name</label>

                                                            <input type="text" name="file_name" id="file_name"
                                                                class="form-control" placeholder="File Name"
                                                                maxlength="100" tabindex="3"
                                                                value="{{ $fill_array['file_name'] }}" />
                                                            <span id="error_limit_no" class="text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="divBodyCode">
                                                            <label class="required-field">Upload File</label>

                                                            <input type="file" name="uploaded_file"
                                                                id="uploaded_file" class="form-control"
                                                                placeholder="Upload File" maxlength="30"
                                                                tabindex="4" />
                                                            <div class="imageSize">(File type must be in pdf format)
                                                            </div>

                                                            <span id="error_limit_no" class="text-danger"></span>
                                                        </div>
                                                    </div>

                                                </div>







                                                <div class="col-md-12" align="center">

                                                    <button type="submit" name="submit" value="Submit"
                                                        class="btn btn-success savepdf">Upload</button>
                                                    <button type="button" id="submitting" value="Submit"
                                                        class="btn btn-success success btn-lg" disabled>Submitting
                                                        please wait</button>
                                                    <div class=""><img src="{{ asset('images/ZKZg.gif') }}"
                                                            id="submit_loader" width="150px" height="150px"></div>

                                                    <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                                                </div>


                                                <br />
                                            </div>
                                        </div>
                                    </div>








                            </form>
                        </div>
                        <!-- /.box -->
                    </div>
                    <!--/.col (left) -->

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
        <script src="{{ asset('js/select2.full.min.js') }}"></script>

        <!-- Bootstrap 3.3.2 JS -->
        <script src="{{ asset('/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>

        <script src="{{ URL::asset('js/site.js') }}"></script>

        <script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
        <!-- AdminLTE App -->
        <script src="{{ asset('/bower_components/AdminLTE/dist/js/app.min.js') }}" type="text/javascript"></script>
        {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
        <script>
            $(document).ready(function() {
                $("#submit_loader").hide();
                $("#submitting").hide();
                $('#district_code').change(function() {
                    $("#urban_code").val('');
                    $('#block').html('<option value="">--Select --</option>');
                    $('#gp_ward').html('<option value="">--Select --</option>');
                });
                $('#urban_code').change(function() {
                    var urban_code = $(this).val();

                    $('#block').html('<option value="">--Select --</option>');
                    $('#gp_ward').html('<option value="">--Select --</option>');
                    select_district_code = $('#district_code').val();
                    if (select_district_code == '') {
                        alert('Please Select District First');
                        $("#district").focus();
                        $("#urban_code").val('');
                    } else {
                        select_body_type = urban_code;
                        var htmlOption = '<option value="">--Select--</option>';
                        if (select_body_type == 2) {
                            $.each(blocks, function(key, value) {
                                if (value.district_code == select_district_code) {
                                    htmlOption += '<option value="' + value.id + '">' + value.text +
                                        '</option>';
                                }
                            });
                        } else if (select_body_type == 1) {
                            $.each(ulbs, function(key, value) {
                                if (value.district_code == select_district_code) {
                                    htmlOption += '<option value="' + value.id + '">' + value.text +
                                        '</option>';
                                }
                            });
                        }
                        $('#block').html(htmlOption);
                    }
                });
                $('#block').change(function() {

                    var block = $(this).val();
                    var district = $("#district_code").val();
                    var urban_code = $("#urban_code").val();
                    if (urban_code == '') {
                        alert('Please Select Rural/Urban First');
                        $("#block").val('');
                        $("#gp_ward").val('');
                        $("#urban_code").focus();
                    } else {
                        select_body_type = urban_code;

                        var htmlOption = '<option value="">--Select--</option>';
                        if (select_body_type == 2) {
                            $.each(gps, function(key, value) {
                                if (value.district_code == district && value.block_code == block) {
                                    htmlOption += '<option value="' + value.id + '">' + value.text +
                                        '</option>';
                                }
                            });
                        } else if (select_body_type == 1) {
                            $.each(ulb_wards, function(key, value) {
                                if ((value.urban_body_code == block)) {
                                    htmlOption += '<option value="' + value.id + '">' + value.text +
                                        '</option>';
                                }
                            });
                        }
                        $('#gp_ward').html(htmlOption);
                    }
                });
                $('.savepdf').on('click', function() {
                    $(".savepdf").hide();
                    $("#submitting").show();
                    $("#submit_loader").show();
                    //$("#register_form").submit();
                });
            });

            function printMsg(msg, msgtype, divid) {
                $("#" + divid).find("ul").html('');
                $("#" + divid).css('display', 'block');
                if (msgtype == '0') {
                    //alert('error');
                    $("#" + divid).removeClass('alert-success');
                    //$('.print-error-msg').removeClass('alert-warning');
                    $("#" + divid).addClass('alert-warning');
                } else {
                    $("#" + divid).removeClass('alert-warning');
                    $("#" + divid).addClass('alert-success');
                }
                if (Array.isArray(msg)) {
                    $.each(msg, function(key, value) {
                        $("#" + divid).find("ul").append('<li>' + value + '</li>');
                    });
                } else {
                    $("#" + divid).find("ul").append('<li>' + msg + '</li>');
                }
            }

            function closeError(divId) {
                $('#' + divId).hide();
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#designation_id').select2({
                    placeholder: "--Select--"
                });
                $('#scheme_id').select2({
                    placeholder: "--Select--",
                });
            });
        </script>
</body>

</html>
