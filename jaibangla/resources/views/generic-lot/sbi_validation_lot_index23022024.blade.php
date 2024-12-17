@extends('generic-lot.base')
@section('action-content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Account Validation Lot
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Account Validation Lot</a></li>
            <!-- <li class="active">Duplicate Approve</li> -->
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    {{-- <div class="box-header with-border">
          <h3 class="box-title">Lot creation form</h3>
        </div> --}}
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form class="form-horizontal" role="form" id="repeat_lot_form">
                        {{ csrf_field() }}
                        <input type="hidden" id="hiddenBencount" value="" />
                        <div class="box-body">
                            <div id="loadingDiv">
                            </div>
                            <div class="form-group">
                                <label for="select_scheme" class="col-md-3 control-label required">Select Scheme</label>

                                <div class="col-md-9">
                                    <select name="select_scheme" id="select_scheme" required class="form-control"
                                        onchange="divChange(),pendingBeneficiary();">
                                        <option value="" selected>---Select Scheme---</option>
                                        @foreach ($reports as $report)
                                            <option value="{{ $report->id }}"
                                                @if (Session::get('schemeSession') == $report->id) selected @endif>{{ $report->scheme_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="select_bank_div">
                                <label for="select_bank" class="col-md-3 control-label required">Select Bank </label>

                                <div class="col-md-9">
                                    <select name="select_bank" id="select_bank" class="form-control select2"
                                        onchange="pendingBeneficiary();">
                                        <option value="" selected>---Select Bank---</option>
                                        {{-- <option value="SBIN">State Bank of India</option>
                                        <option value="PUNB">PUNJAB NATIONAL BANK</option> --}}
                                        @foreach ($bankLists as $k)
                                        <option value="{{ $k->bank_code}}">{{$k->bank_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="lotsize_div" style="display: none;">
                                <label for="lot_size" class="col-md-3 control-label required">Select Lot Size</label>

                                <div class="col-md-9">
                                    <select class="form-control " name="lot_size" required id="lot_size">
                                        <option value="">--Select Lot Size--</option>

                                        @foreach (Config::get('constants.lot_size') as $key => $lot)
                                            <option value="{{ $key }}">{{ $lot }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="input_lot_size_div" style="display: none;">
                                <label for="lot_size_input" class="col-md-3 control-label required">Enter Lot Size</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control " name="lot_size_input" required
                                        id="lot_size_input" value="" onkeypress="return isNumber(event)"
                                        maxlength="5">

                                </div>
                            </div>
                            <div class="form-group" id="pending_div">
                                <label for="beneficiary_count" class="col-md-3 control-label required ">Beneficiary
                                    Pending</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="beneficiary_count" disabled>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer" style="text-align: center">
                            {{-- <button type="button" class="btn btn-warning">Reset</button> --}}
                            <button type="button" class="btn btn-success" id="btnSubmit">Create</button>
                        </div>
                        <!-- /.box-footer -->
                    </form>


                </div>
                <!-- /.box -->
                <!-- general form elements disabled -->

                <!-- /.box -->
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>

@endsection
@section('script')

    <script>
        $(function() {
            @if (Session::has('schemeSession'))
                getPaymentMode();
            @endif
            @if (Session::has('yearSession'))
                monthChange();
            @endif
            @if (Session::has('lotTypeSession'))
                lot_type_change();
                categoryChange();
            @endif


            $('.successErrorMessage').delay(30000).slideUp(300);
            $('#btnSubmit').click(function() {
                validate_form();
                var validator = $('#repeat_lot_form').data('bootstrapValidator');
                validator.validate();
                if (validator.isValid()) {
                    saveLotCreation();


                }
            });
        });

        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 46 || charCode > 57 || charCode == 47)) {
                return false;
            }
            return true;
        }

        function validate_form() {
            $('#repeat_lot_form')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        select_scheme: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select scheme'
                                }

                            }
                        },
                        select_bank: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select bank'
                                }

                            }
                        },
                        lot_size: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select lot size'
                                }
                            }
                        },
                        lot_size_input: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter lot size'
                                }
                            }
                        }

                    }
                }).on('success.form.bv', function(e) {

                });
        }

        function saveLotCreation() {
            var hiddenBencount = $('#hiddenBencount').val();
            var lotScheme = $('#select_scheme').val();
            var selectBank = $('#select_bank').val();
            if (hiddenBencount == 0) {
                $.confirm({
                    type: 'red',
                    icon: 'fa fa-warning',
                    title: 'Error!!',
                    content: 'You can not generate lot when beneficiary pending is 0.',
                });
                return false;
            }
            // console.log(selectBank);
            // console.log(lotScheme);
            if ((selectBank != "") && (lotScheme != "")) {
                //  e.preventDefault();
                var selectBank = $('#select_bank').val();
                var select_scheme = $('#select_scheme').val();
                var lot_size = $('#lot_size').val();
                var token = $("input[name='_token']").val();

                var fd = new FormData();
                fd.append('select_bank', selectBank);
                fd.append('select_scheme', select_scheme);
                fd.append('lot_size', lot_size);

                fd.append('_token', token);
                $('#loadingDiv').show();
                $.ajax({
                    type: 'post',
                    url: "{{ route('storeAccountValidationLot') }}",
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#loadingDiv').hide();

                        $.confirm({
                            title: response.title,
                            type: response.type,
                            icon: response.icon,
                            content: response.msg,
                            buttons: {
                                Ok: {
                                    text: 'Ok',
                                    btnClass: 'btn-green',
                                    keys: ['enter', 'shift'],
                                    // action: function(){
                                    //   reload_table();
                                    // }

                                }

                            }
                        });
                    },
                    complete: function() {
                        pendingBeneficiary();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#loadingDiv').hide();
                        ajax_error(jqXHR, textStatus, errorThrown);
                    }
                });
            } else {
                $.confirm({
                    type: 'red',
                    icon: 'fa fa-warning',
                    title: 'Error!!',
                    content: 'Please select all the (*) mark fields.',
                });
                console.log(2);
                return false;
            }
        }

        function divChange() {
            var scheme_id_div = $('#select_scheme').val();
            if (scheme_id_div == 6) {
                $('#input_lot_size_div').show();
                $('#lotsize_div').hide();

            } else {
                $('#input_lot_size_div').hide();
                $('#lotsize_div').show();
            }
        }

        function pendingBeneficiary() {
            var select_scheme = $('#select_scheme').val();
            var select_bank = $('#select_bank').val();
            var token = $("input[name='_token']").val();

            if ((select_bank != "") && (select_scheme != "")) {
                var fd = new FormData();
                var action_url = "{{ route('pendingBenAccValidationLot') }}";

                fd.append('select_bank', select_bank);
                fd.append('select_scheme', select_scheme);
                fd.append('_token', token);
                $('#loadingDiv').show();
                $.ajax({
                    type: 'post',
                    url: action_url,
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {

                        $('#pending_div').removeClass('has-feedback has-success');
                        $('#pending_div #spanpeningid').removeClass(
                            'glyphicon glyphicon-ok form-control-feedback');
                        $("#beneficiary_count").css({
                            'color': '',
                            'font-weight': ''
                        });
                        $('#loadingDiv').hide();
                        if (response.lot != 2) {
                            $('#hiddenBencount').val(response.bencount);
                            $('#lot_size').html('');
                            $('#lot_size_input').val('');
                            if (select_scheme != 6) {

                                // $('#lot_size').append('<option value="">--Select Size--</option>');
                                if (response.bencount > 0 && response.bencount < 10000) {
                                    $('#lot_size').append('<option value=' + response.bencount + ' selected>' +
                                        response.bencount + '</option>');
                                }

                                @foreach (Config::get('constants.lot_size') as $key => $lot)
                                    $('#lot_size').append(
                                        '<option value="{{ $key }}">{{ $lot }}</option>'
                                    );
                                @endforeach
                            }
                            $('#beneficiary_count').val(response.bencount);
                            if (response.bencount > 0) {
                                $('#beneficiary_count').css({
                                    "color": "green",
                                    "font-weight": "bold"

                                });
                                $('#pending_div').addClass('has-feedback has-success');
                                $('#pending_div #spanpeningid').addClass(
                                    'glyphicon glyphicon-ok form-control-feedback');
                            }
                        }
                    },
                    complete: function() {
                        // getPaymentMode();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        ajax_error(jqXHR, textStatus, errorThrown);
                    }
                });
            }
        }
    </script>
@stop
