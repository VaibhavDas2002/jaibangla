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

        <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">

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

        #divScrool {
            overflow-x: scroll;
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
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div> <!-- class="box box-primary" -->
                            <div class="box-header with-border">
                                <h3 class="box-title"><b>

                                        Jai Bangla to Social Registry Data Share

                                    </b></h3>
                            </div>

                            <div>
                                @if (($message = Session::get('success')) && ($id = Session::get('id')))
                                    <div class="alert alert-success alert-block">
                                        <strong>{{ $message }}</strong>
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
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li><strong> {{ $error }}</strong></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->


                            <div class="tab-content" style="margin-top:16px;">

                                <div class="tab-pane active" id="personal_details">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4><b>Select Criteria</b></h4>
                                        </div>
                                        <div class="panel-body">

                                            {{ csrf_field() }}
                                            <div class="row">
                                                <div class="form-group col-md-4 mb-10">
                                                    <label for="scheme_id" class="required-field">Select
                                                        Scheme</label>
                                                    <select name="scheme_id" id="scheme_id" class="form-control">
                                                        <option value="">--Select Scheme--</option>
                                                        @foreach ($schemes as $scheme)
                                                            <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span id="error_scheme" class="text-danger"></span>
                                                </div>

                                                <div class="form-group col-md-4 mb-10">
                                                    <label for="fin_year" class="required-field">Select Financial
                                                        Year</label>
                                                    <select name="fin_year" id="fin_year" class="form-control">
                                                        <option value="">--Select Financial Year--</option>
                                                        @foreach (Config::get('constants.fin_year') as $key => $val)
                                                            <option value="{{ $key }}">{{ $val }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span id="error_fin_year" class="text-danger"></span>
                                                </div>

                                                <div class="form-group col-md-4 mb-10">
                                                    <label for="month" class="required-field">Select Month</label>
                                                    <select name="month" id="month" class="form-control">
                                                        <option value="">--Select Month--</option>
                                                        @foreach (Config::get('constants.monthlist') as $key => $val)
                                                            <option value="{{ $key }}">{{ $val }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span id="error_month" class="text-danger"></span>
                                                </div>

                                                <div class="col-md-12 text-center">
                                                    <button type="submit" class="btn btn-primary"
                                                        id="submit">Submit</button>
                                                </div>
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



        </section>

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
    <script src="{{ asset('/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ URL::asset('js/site.js') }}"></script>

    <script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('/bower_components/AdminLTE/dist/js/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/jquery.table2excel.js') }}"></script>
    <script src="{{ asset('js/jquery-confirm.min.js') }}"></script>


    <script>

        $(document).ready(function () {
            $('#scheme_id, #fin_year, #month').select2();

            $('#submit').click(function (e) {
                e.preventDefault(); // Always prevent default form submission

                var scheme_id = $('#scheme_id').val();
                var fin_year = $('#fin_year').val();
                var month = $('#month').val();
                var error = false;

                if (scheme_id == '') {
                    $('#error_scheme').text('Scheme is required');
                    error = true;
                } else {
                    $('#error_scheme').text('');
                }

                if (fin_year == '') {
                    $('#error_fin_year').text('Financial Year is required');
                    error = true;
                } else {
                    $('#error_fin_year').text('');
                }

                if (month == '') {
                    $('#error_month').text('Month is required');
                    error = true;
                } else {
                    $('#error_month').text('');
                }

                if (!error) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('jb_social-registryPost') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            scheme_id: scheme_id,
                            fin_year: fin_year,
                            month: month
                        },
                        success: function (data) {
                            if (data.status == 1) {
                                $.confirm({
                                    title: data.title,
                                    type: data.type,
                                    icon: data.icon,
                                    content: data.msg,
                                    buttons: {
                                        Ok: function () {
                                            window.location.reload();
                                        }
                                    }
                                });
                            } else {
                                $.alert({
                                    title: data.title,
                                    type: data.type,
                                    icon: data.icon,
                                    content: data.msg
                                });
                            }
                        },
                        error: function (xhr) {
                            $.confirm({
                                title: 'Error',
                                type: 'red',
                                icon: 'fa fa-warning',
                                content: 'Something went wrong in the approval!!',
                                buttons: {
                                    Ok: function () {
                                        // $("#verifyReject").show();
                                        //  $("#submitting").hide();
                                        location.reload();
                                    }
                                }
                            });
                        }
                    });
                }
            });
        });


    </script>
</body>

</html>