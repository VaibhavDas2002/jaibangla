<style type="text/css">
    .has-error {
        border-color: #cc0000;
        background-color: #ffff99;
    }

    .preloader1 {
        position: fixed;
        top: 40%;
        left: 52%;
        z-index: 999;
    }

    .preloader1 {
        background: transparent !important;
    }

    .loadingDivModal {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url('images/ajaxgif.gif');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        /* For IE8 and earlier */
    }

    #updateDiv {
        border: 1px solid #d9d9d9;
        padding: 8px;
        box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Find CMO Grievance Beneficiary
            </h1>
            <ol class="breadcrumb">
                <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                        class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDiv"></div>
                    <div class="panel panel-default">
                        <div class="panel-heading"
                            style="font-size: 17px; font-weight: bold; font-style: italic; padding: 5px 15px;"><span
                                id="panel-icon"><a href="cmo-grievance-workflow?scheme_id={{$scheme_id}}&type=1">
                                    <img width="40px;" style="pull-right" src="{{ asset('images/back.png') }}"
                                        alt="Back" /></a> Grievance Details</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (($message = Session::get('success')))
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }} </strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('message')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('msg1')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="col-md-3">
                                                <strong>Grievance ID : </strong>
                                                <span style="font-size: 14px;">{{$grievance_id}}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Grievance No. : </strong>
                                                <span style="font-size: 14px;">{{$row->grievance_no}}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Caller Name : </strong>
                                                <span style="font-size: 14px;">{{$row->applicant_name}}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Caller Mobile No. : </strong>
                                                <span style="font-size: 14px;">{{$grievance_mobile_no}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="margin-bottom: 10px;">

                                            <div class="col-md-3">
                                                <strong>Age : </strong>
                                                <span style="font-size: 14px;">{{$row->applicant_age}} years</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="col-md-12">
                                                <strong>Address : </strong>
                                                <span style="font-size: 14px;">{{$row->applicant_address}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="col-md-12">
                                                <strong>Description : </strong>
                                                <span style="font-size: 14px;">{{$row->grievance_description}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default" id='atr_tagging'>
                        <div class="panel-heading"
                            style="font-size: 17px; font-weight: bold; font-style: italic; padding: 5px 15px;"><span
                                id="panel-icon">ATR Tagging</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (($message = Session::get('success')))
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }} </strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('message')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('msg1')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    <form class="form-horizontal">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">ATR Type <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="atr_type" id="atr_type" {{ isset($row->atr_type) ? 'disabled' : '' }}>
                                                    <option value="">-----ATR Type----</option>
                                                    @foreach ($atr as $atrs)
                                                        <option value="{{$atrs->atn_id}}" {{ (isset($row) && $row->atr_type == $atrs->atn_id) ? 'selected' : '' }}>
                                                            {{$atrs->atr_desc}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_atr_type"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" id='district_div'>
                                            <label class="col-md-4 control-label">District<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="district" id="district">
                                                    <option value="">-----Select District----</option>
                                                    @foreach ($districtList as $districtLists)
                                                        <option value="{{$districtLists->district_code}}">
                                                            {{$districtLists->district_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_district"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" id='urban_code_div'>
                                            <label class="col-md-4 control-label">Rural/Urban<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="urban_code" id="urban_code">
                                                    <option value="">-----Select Rural/Urban----</option>
                                                    @foreach (Config::get('constants.rural_urban') as $key => $value)
                                                        <option value="{{$key}}"> {{$value}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_urban_code"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" id='block_div'>
                                            <label class="col-md-4 control-label" id="blk_sub_txt">Block/Subdivision<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="block" id="block">
                                                    <option value="">-----Select Block/Subdivision----</option>

                                                </select>
                                                <span class="text-danger" id="error_block"></span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Remarks<span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                {{-- <input type="text" class="form-control" id="remarks"> --}}
                                                <textarea class="form-control" id="remarks" rows="3" {{ (isset($row) && $row->atr_type == 2) ? 'disabled' : '' }}>
                                                        {{ isset($row->atr_type) && $row->atr_type == 2 ? $row->remarks : '' }}
                                                    </textarea>
                                                <span class="text-danger" id="error_remarks"></span>
                                                @if (isset($row->atr_type) && $row->atr_type == 2)
                                                    <input type="hidden" name="remarks" value="{{ $row->remarks }}">
                                                @endif
                                            </div>
                                        </div>
                                        <input type="hidden" name="pension_id" id="pension_id" value="">
                                        <input type="hidden" name="grievance_id" id="grievance_id"
                                            value="{{$grievance_id}}">
                                        <input type="hidden" name="grievance_mobile_no" id="grievance_mobile_no"
                                            value="{{$grievance_mobile_no}}">
                                        <div style="text-align: center; justify-content: center;">
                                            <button class="btn  btn-info" name="map_applicant" id="map_applicant"
                                                type="button">Map Applicant</button>
                                            <button class="btn  btn-danger" name="redress" id="redress" type="button"
                                                style="margin-left: 10px">Grievance Redressed</button>
                                            <button class="btn  btn-info" name="send_another_block" id="send_another_block"
                                                type="button" style="margin-left: 10px">Send to another
                                                Block/Subdivision</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default" id='search_panel'>
                        <div class="panel-heading"
                            style="font-size: 15px; font-weight: bold; font-style: italic; padding: 5px 15px;"><span
                                id="panel-icon">Search using Beneficiary id, Beneficiary name, Applicant mobile no, Aadhaar
                                no, Bank account number</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (($message = Session::get('success')))
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }} </strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('message')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('msg1')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12 form-group"
                                            style="margin-bottom: 10px; text-align: center; justify-content: center; margin-top: 5px;">
                                            <div class="col-md-3" style="text-align: right;">
                                                <label>Please select which one do you want to Search?</label>
                                            </div>
                                            <div class="col-md-6">
                                                <label style="cursor: pointer; margin-bottom: 5px;" id="radio_button">
                                                    <input type="radio" name="process_type" class="process_type_radio"
                                                        value="1">
                                                    Beneficiary ID
                                                </label>&nbsp;&nbsp;
                                                <label style="cursor: pointer; margin-bottom: 5px;" id="radio_button">
                                                    <input type="radio" name="process_type" class="process_type_radio"
                                                        value="5">
                                                    Beneficiary Name
                                                </label>&nbsp;&nbsp;
                                                <label style="cursor: pointer; margin-bottom: 5px;" id="radio_button">
                                                    <input type="radio" name="process_type" class="process_type_radio"
                                                        value="2">
                                                    Mobile Number
                                                </label>&nbsp;&nbsp;
                                                <label style="cursor: pointer; margin-bottom: 5px;" id="radio_button">
                                                    <input type="radio" name="process_type" class="process_type_radio"
                                                        value="3">
                                                    Aadhaar Number
                                                </label>&nbsp;&nbsp;
                                                <label style="cursor: pointer; margin-bottom: 5px;" id="radio_button">
                                                    <input type="radio" name="process_type" class="process_type_radio"
                                                        value="4">
                                                    Bank Account Number
                                                </label>
                                            </div>
                                            <input type="hidden" name="grievance_id" id="grievance_id"
                                                value={{$grievance_id}}>
                                            <input type="hidden" name="grievance_mobile" id="grievance_mobile"
                                                value={{$grievance_mobile_no}}>
                                            <input type="hidden" name="scheme_id" id="scheme_id" value={{$scheme_id}}>
                                            <input type="hidden" name="new_process_id" id="new_process_id" value="" />
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row" id="search_level">
                                        <div class="col-md-12" style="text-align: center; justify-content: center;">
                                            <div style="text-align: center; justify-content: center;">
                                                <label id="input_label"></label>
                                                <input value="" name="input_value" id="input_value"
                                                    style="font-size: 16px; background-color: #f2f2f2;">
                                                <span id="error_input_value" class="text-danger"></span>
                                                &nbsp;&nbsp;
                                                <button class="btn btn-sm btn-info" name="search_btn" id="search_btn"
                                                    type="button"><i class="fa fa-search"></i> Search</button>
                                                  <button class="btn btn-sm btn-warning" name="send_to_operator"
                                                    id="send_to_operator" type="button" style="margin-left: 100px">Send To
                                                    Operator For New Entry</button> 
                                                <!-- <button class="btn btn-sm btn-danger" name="send_to_operator" id="send_to_operator" type="button" style="margin-left: 10px">Reject</button>  --> 
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="res_div" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head"
                                style="font-size: 15px; font-weight: bold; font-style: italic; padding: 5px 15px;">List of
                                Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table display" cellspacing="0" width="100%">
                                        <thead style="font-size: 12px;">
                                            <th>Beneficiary ID</th>
                                            <th>Scheme Name</th>
                                            <th>Applicant Name</th>
                                            <th>Father's Name</th>
                                            <th>Mobile No</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody style="font-size: 14px;"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
<script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
    $(document).ready(function () {
        var interval = setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        // $('#search_level').hide();
        $('#loadingDiv').hide();
        $('#submit_btn').removeAttr('disabled');
        $('#res_div').hide();
        $('#search_panel').hide();
        $('#map_applicant').hide();
        $('#redress').hide();
        $('#send_another_block').hide();
        $('#district_div').hide();
        $('#urban_code_div').hide();
        $('#block_div').hide();
        $('#send_to_operator').show();

        var applicant_mobile_no = '{{$row->pri_cont_no}}';
        // if(applicant_mobile_no){
        //     $('input[name="process_type"][value="2"]').attr('checked', true);
        //     $('#input_value').val(applicant_mobile_no);
        //     $("#new_process_id").val(2);
        //     var label_text = 'Mobile Number :';
        //     $("#input_label").text(label_text);
        //     performSearch();
        // }

        var error_input_value = '';
        $('#search_btn').click(function () {
            performSearch();
        });

        function performSearch() {
            if ($.trim($('#input_value').val()).length == 0) {
                error_input_value = 'Input value is required';
                $('#error_input_value').text(error_input_value);
                $('#input_value').addClass('has-error');
            } else {
                error_input_value = '';
                $('#error_input_value').text(error_input_value);
                $('#input_value').removeClass('has-error');
            }

            if (error_input_value != '') {
                return false;
            } else {
                var scheme_id = $('#scheme_id').val();
                var grievance_mobile = $('#grievance_mobile').val();
                var grievance_id = $('#grievance_id').val();
                var new_process_id = $('#new_process_id').val();
                var input_value = $('#input_value').val();

                var ajaxData = {
                    'scheme_id': scheme_id,
                    'grievance_mobile': grievance_mobile,
                    'grievance_id': grievance_id,
                    'new_process_id': new_process_id,
                    'input_value': input_value,
                    _token: "{{ csrf_token() }}"
                };
                loadBenListData(ajaxData);
            }
        }

        $(document).on('change', '.process_type_radio', function () {
            $('#res_div').hide();
            var process_type = $(this).val();
            $('#input_value').val('');
            var label_text = '';
            if (process_type == 1) {
                $("#new_process_id").val(1);
                label_text = 'Beneficiary ID :';
            } else if (process_type == 2) {
                $("#new_process_id").val(2);
                label_text = 'Mobile Number :';
            } else if (process_type == 3) {
                $("#new_process_id").val(3);
                label_text = 'Aadhaar Number :';
            } else if (process_type == 4) {
                $("#new_process_id").val(4);
                label_text = 'Bank Account Number :';
            } else if (process_type == 5) {
                $("#new_process_id").val(5);
                label_text = 'Beneficiary Name :';
            }

            $("#input_label").text(label_text);
        });





        $(document).on('click', '#send_to_operator', function () {
            $.confirm({
                title: 'Confirm!',
                type: 'orange',
                icon: 'fa fa-warning',
                content: '<strong>Are you want to send grievance details to the Operator end?</strong>',
                buttons: {
                    confirm: function () {
                        $('#loadingDiv').show();
                        $.ajax({
                            type: 'post',
                            url: "{{ route('cmo-sent-to-operator') }}",
                            data: {
                                scheme_id: $('#scheme_id').val(),
                                grievance_mobile_no: $('#grievance_mobile').val(),
                                grievance_id: $('#grievance_id').val(),
                                atr_type: $('#atr_type').val(), 
                                remarks: $('#remarks').val(),
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                $('#loadingDiv').hide();
                                // console.log(response);
                                if (response.status == 1) {
                                    $.confirm({
                                            title: response.title,
                                            type: response.type,
                                            icon: response.icon,
                                            content: response.msg,
                                            buttons: {
                                                Confirm: {
                                                    text: 'Ok',
                                                    btnClass: 'btn-green',
                                                    keys: ['enter', 'shift'],
                                                    action: function () {
                                                        window.location.href = "cmo-grievance-workflow?scheme_id=" + $('#scheme_id').val() + "&type=1";
                                                    }
                                                },
                                                // cancel: function() {}
                                            }
                                        });
                                    $('#res_div').hide();
                                    $('#select_type').val('').trigger('change');
                                    $('#scheme_type').val('').trigger('change');
                                    $("html, body").animate({ scrollTop: 0 }, "slow");
                                }
                                else {
                                    $.alert({
                                        title: response.title,
                                        type: response.type,
                                        icon: response.icon,
                                        content: response.msg
                                    });
                                }
                            },
                            complete: function () {
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                $('#loadingDiv').hide();
                                ajax_error(jqXHR, textStatus, errorThrown);
                            }
                        });
                    },
                    cancel: function () {
                    }
                }
            });
        });

        $(document).on('click', '#redress', function () {
            if ($.trim($('#atr_type').val()).length == 0) {
                error_atr_type = 'ATR Type is required';
                $('#error_atr_type').text(error_atr_type);
                $('#atr_type').addClass('has-error');
            } else {
                error_atr_type = '';
                $('#error_atr_type').text(error_atr_type);
                $('#atr_type').removeClass('has-error');
            }
            if ($.trim($('#remarks').val()).length == 0) {
                error_remarks = 'Remarks is required';
                $('#error_remarks').text(error_remarks);
                $('#remarks').addClass('has-error');
            } else {
                error_remarks = '';
                $('#error_remarks').text(error_remarks);
                $('#remarks').removeClass('has-error');
            }

            if (error_atr_type != '' || error_remarks != '') {
                return false;
            } else {
                $.confirm({
                    title: 'Confirm!',
                    type: 'red',
                    icon: 'fa fa-warning',
                    content: '<strong>Are you want to redressed the grievance?</strong>',
                    buttons: {
                        confirm: function () {
                            $('#loadingDiv').show();
                            $.ajax({
                                type: 'post',
                                url: "{{ route('cmo-grievance-redress') }}",
                                data: { scheme_id: $('#scheme_id').val(), grievance_mobile_no: $('#grievance_mobile').val(), grievance_id: $('#grievance_id').val(), atr_type: $('#atr_type').val(), remarks: $('#remarks').val(), _token: '{{ csrf_token() }}' },
                                success: function (response) {
                                    $('.loadingDivModal').hide();
                                    if (response.status == 1) {
                                        $('#modalUpdateatr').modal('hide');
                                        $('#res_div').hide();
                                        $('#scheme_type').val('').trigger('change');
                                        $("html, body").animate({ scrollTop: 0 }, "slow");
                                        $.confirm({
                                            title: response.title,
                                            type: response.type,
                                            icon: response.icon,
                                            content: response.msg,
                                            buttons: {
                                                Confirm: {
                                                    text: 'Ok',
                                                    btnClass: 'btn-green',
                                                    keys: ['enter', 'shift'],
                                                    action: function () {
                                                        window.location.href = "cmo-grievance-workflow?scheme_id=" + $('#scheme_id').val() + "&type=1";

                                                    }
                                                },
                                                // cancel: function() {}
                                            }
                                        });


                                    } else {
                                        var html = '';
                                        html += '<ul>';
                                        if (Array.isArray(response.msg)) {
                                            $.each(response.msg, function (key, value) {
                                                html += '<li>' + value + '</li>';
                                            });
                                        }
                                        else {
                                            html = '<li>' + response.msg + '</li>';
                                        }
                                        html += '<ul>';
                                        $.alert({
                                            title: response.title,
                                            type: response.type,
                                            icon: response.icon,
                                            content: html
                                        });
                                    }
                                },
                                complete: function () {
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    $('#loadingDiv').hide();
                                    ajax_error(jqXHR, textStatus, errorThrown);
                                }
                            });
                        },
                        cancel: function () {
                        }
                    }
                });
            }
        });

        $(document).on('click', '#send_another_block', function () {
            if ($.trim($('#atr_type').val()).length == 0) {
                error_atr_type = 'ATR Type is required';
                $('#error_atr_type').text(error_atr_type);
                $('#atr_type').addClass('has-error');
            } else {
                error_atr_type = '';
                $('#error_atr_type').text(error_atr_type);
                $('#atr_type').removeClass('has-error');
            }
            if ($.trim($('#district').val()).length == 0) {
                error_district = 'District is required';
                $('#error_district').text(error_district);
            } else {
                error_district = '';
                $('#error_district').text(error_district);
            }
            if ($.trim($('#urban_code').val()).length == 0) {
                error_urban_code = 'Rural/Urban is required';
                $('#error_urban_code').text(error_urban_code);
            } else {
                error_urban_code = '';
                $('#error_urban_code').text(error_urban_code);
            }
            if ($.trim($('#block').val()).length == 0) {
                error_block = 'Block/Subdiv is required';
                $('#error_block').text(error_block);
            } else {
                error_block = '';
                $('#error_block').text(error_block);
            }
            if ($.trim($('#remarks').val()).length == 0) {
                error_remarks = 'Remarks is required';
                $('#error_remarks').text(error_remarks);
                $('#remarks').addClass('has-error');
            } else {
                error_remarks = '';
                $('#error_remarks').text(error_remarks);
                $('#remarks').removeClass('has-error');
            }
            if (error_atr_type != '' || error_district != '' || error_urban_code != '' || error_block != '' || error_remarks != '') {
                return false;
            } else {
                var block_subdiv_text = $('#block option:selected').text();
                $.confirm({
                    title: 'Confirm!',
                    type: 'orange',
                    icon: 'fa fa-warning',
                    content: '<strong>Are you want to send the grievance to block/Subdivision: ' + block_subdiv_text + '?</strong>',
                    buttons: {
                        confirm: function () {
                            $('#loadingDiv').show();
                            $.ajax({
                                type: 'post',
                                url: "{{ route('cmo-grievance-transfar') }}",
                                data: { scheme_id: $('#scheme_id').val(), grievance_mobile_no: $('#grievance_mobile').val(), grievance_id: $('#grievance_id').val(), atr_type: $('#atr_type').val(), remarks: $('#remarks').val(), district: $('#district').val(), rural_urban: $('#urban_code').val(), block: $('#block').val(), _token: '{{ csrf_token() }}' },
                                success: function (response) {
                                    $('.loadingDivModal').hide();
                                    if (response.status == 1) {
                                        $('#modalUpdateatr').modal('hide');
                                        $('#res_div').hide();
                                        $('#scheme_type').val('').trigger('change');
                                        $("html, body").animate({ scrollTop: 0 }, "slow");
                                        $.confirm({
                                            title: response.title,
                                            type: response.type,
                                            icon: response.icon,
                                            content: response.msg,
                                            buttons: {
                                                Confirm: {
                                                    text: 'Ok',
                                                    btnClass: 'btn-green',
                                                    keys: ['enter', 'shift'],
                                                    action: function () {
                                                        window.location.href = "cmo-grievance-workflow?scheme_id=" + $('#scheme_id').val() + "&type=1";
                                                    }
                                                },
                                                // cancel: function() {}
                                            }
                                        });


                                    } else {
                                        var html = '';
                                        html += '<ul>';
                                        if (Array.isArray(response.msg)) {
                                            $.each(response.msg, function (key, value) {
                                                html += '<li>' + value + '</li>';
                                            });
                                        }
                                        else {
                                            html = '<li>' + response.msg + '</li>';
                                        }
                                        html += '<ul>';
                                        $.alert({
                                            title: response.title,
                                            type: response.type,
                                            icon: response.icon,
                                            content: html
                                        });
                                    }
                                },
                                complete: function () {
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    $('#loadingDiv').hide();
                                    ajax_error(jqXHR, textStatus, errorThrown);
                                }
                            });
                        },
                        cancel: function () {
                        }
                    }
                });
            }
        });


        $(document).on('click', '.process_applicant', function () {
            var val = $(this).val();
            var atr_type = $('#atr_type').val();
            var atr_type_text = $('#atr_type option:selected').text();
            // console.log(atr_type);
            var remarks = $('#remarks').val();
            var array = val.split("_");
            var ben_id = array[0];
            var scheme_id = array[1];
            var grivence_mobile_no = array[2];
            var grivence_id = array[3];
            $.confirm({
                title: 'Confirm!',
                type: 'orange',
                icon: 'fa fa-warning',
                content: '<strong>Are you want to process the beneficiary with Id: ' + ben_id + '? </strong>',
                buttons: {
                    confirm: function () {
                        $('#loadingDiv').show();
                        $.ajax({
                            type: 'post',
                            url: "{{ route('cmo-grievance-process-post') }}",
                            data: { ben_id: ben_id, scheme_id: scheme_id, grievance_mobile_no: grivence_mobile_no, grievance_id: grivence_id, atr_type: atr_type, remarks: $('#remarks').val(), atr_type: atr_type, remarks: remarks, _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                $('.loadingDivModal').hide();
                                if (response.status == 1) {
                                    $('#modalUpdateatr').modal('hide');
                                    $('#res_div').hide();
                                    $('#scheme_type').val('').trigger('change');
                                    $("html, body").animate({ scrollTop: 0 }, "slow");
                                    $.confirm({
                                        title: response.title,
                                        type: response.type,
                                        icon: response.icon,
                                        content: response.msg,
                                        buttons: {
                                            Confirm: {
                                                text: 'Ok',
                                                btnClass: 'btn-green',
                                                keys: ['enter', 'shift'],
                                                action: function () {
                                                    window.location.href = "cmo-grievance-workflow?scheme_id=" + $('#scheme_id').val() + "&type=1";
                                                }
                                            },
                                            // cancel: function() {}
                                        }
                                    });


                                } else {
                                    var html = '';
                                    html += '<ul>';
                                    if (Array.isArray(response.msg)) {
                                        $.each(response.msg, function (key, value) {
                                            html += '<li>' + value + '</li>';
                                        });
                                    }
                                    else {
                                        html = '<li>' + response.msg + '</li>';
                                    }
                                    html += '<ul>';
                                    $.alert({
                                        title: response.title,
                                        type: response.type,
                                        icon: response.icon,
                                        content: html
                                    });
                                }
                            },
                            complete: function () {
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                $('#loadingDiv').hide();
                                ajax_error(jqXHR, textStatus, errorThrown);
                            }
                        });
                    },
                    cancel: function () {
                    }
                }
            });
        });

        $(document).on('click', '#map_applicant', function () {
            if ($.trim($('#atr_type').val()).length == 0) {
                error_atr_type = 'ATR Type is required';
                $('#error_atr_type').text(error_atr_type);
                $('#atr_type').addClass('has-error');
            } else {
                error_atr_type = '';
                $('#error_atr_type').text(error_atr_type);
                $('#atr_type').removeClass('has-error');
            }
            if ($.trim($('#remarks').val()).length == 0) {
                error_remarks = 'Remarks is required';
                $('#error_remarks').text(error_remarks);
            } else {
                error_remarks = '';
                $('#error_remarks').text(error_remarks);
            }
            if (error_atr_type != '' || error_remarks != '') {
                return false;
            } else {

                $('#search_panel').show();
                if (applicant_mobile_no) {
                    $('input[name="process_type"][value="2"]').attr('checked', true);
                    $('#input_value').val(applicant_mobile_no);
                    $("#new_process_id").val(2);
                    var label_text = 'Mobile Number :';
                    $("#input_label").text(label_text);
                    performSearch();
                }
            }
        });
        if ($('#atr_type').val() == 2) {
            $('#map_applicant').show();
        } else {
            $('#map_applicant').hide();
        }
        $('#atr_type').change(function () {
            $('#search_panel').hide();
            $('#res_div').hide();
            $('#remarks').val('');
            $('#district').val('');
            $('#urban_code').val('');
            $('#block').val('');
            if ($(this).val() == 1 || $(this).val() == 3 || $(this).val() == 5 || $(this).val() == 7 || $(this).val() == 8 || $(this).val() == 11) { // Assuming the value of the third option is '003'
                $('#redress').show();
                $('#map_applicant').hide();
                $('#send_another_block').hide();
                $('#district_div').hide();
                $('#urban_code_div').hide();
                $('#block_div').hide();
            } else if ($(this).val() == 2) {
                $('#send_another_block').show();
                $('#redress').hide();
                $('#map_applicant').hide();
                $('#district_div').show();
                $('#urban_code_div').show();
                $('#block_div').show();
            } else {
                $('#redress').hide();
                $('#map_applicant').show();
                $('#send_another_block').hide();
                $('#district_div').hide();
                $('#urban_code_div').hide();
                $('#block_div').hide();
            }
        });

        function loadBenListData(ajaxData) {
            $('#loadingDiv').show();
            $('#res_div').show();
            // var msg = 'Scheme : ' + $("#scheme_type option:selected").text();
            // $('#panel_head').text(msg);

            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().destroy();
            }
            $('#example tbody').empty();

            var table = $('#example').DataTable({
                dom: 'Blfrtip',
                "scrollX": true,
                "paging": true,
                "searchable": true,
                "ordering": false,
                "bFilter": true,
                "bInfo": true,
                "pageLength": 10,
                'lengthMenu': [[10, 20, 25, 50, 100, -1], [10, 20, 25, 50, 100, 'All']],
                "serverSide": true,
                "processing": true,
                "bRetrieve": true,
                "oLanguage": {
                    "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
                },
                "ajax": {
                    url: "{{ url('cmo-grievance-benLising') }}",
                    type: "post",
                    data: ajaxData,
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#loadingDiv').hide();
                        $('.preloader1').hide();
                        ajax_error(jqXHR, textStatus, errorThrown);
                    }
                },
                "initComplete": function () {
                    $('#loadingDiv').hide();
                },
                "columns": [
                    { "data": "id" },
                    { "data": "scheme_name" },
                    { "data": "name" },
                    { "data": "father_name" },
                    { "data": "mobile_no" },
                    { "data": "address" },
                    { "data": "status" },
                    { "data": "view" }
                ],
                "buttons": [
                    {
                        extend: 'pdf',
                        footer: true,
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                        }
                    },
                    {
                        extend: 'excel',
                        footer: true,
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            stripHtml: false,
                        }
                    },
                ],
            });
        }

        $('#district').change(function () {
            var district = $(this).val();
            //alert(district);
            $('#urban_code').val('');
            $('#block').html('<option value="">--All --</option>');
            $('#muncid').html('<option value="">--All --</option>');
        });

        $('#urban_code').change(function () {
            var urban_code = $(this).val();
            if (urban_code == '') {
                $('#muncid').html('<option value="">--All --</option>');
            }
            $('#muncid').html('<option value="">--All --</option>');
            $('#block').html('<option value="">--All --</option>');
            $('#gp_ward').html('<option value="">--All --</option>');
            select_district_code = $('#district').val();
            if (select_district_code == '') {
                alert('Please Select District First');
                $("#district").focus();
                $("#urban_code").val('');
            }
            else {
                select_body_type = urban_code;
                var htmlOption = '<option value="">--All--</option>';
                $("#gp_ward_div").show();
                if (select_body_type == 2) {
                    $("#blk_sub_txt").text('Block');
                    $("#gp_ward_txt").text('GP');
                    $("#municipality_div").hide();
                    $.each(blocks, function (key, value) {
                        if (value.district_code == select_district_code) {
                            htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
                        }
                    });
                } else if (select_body_type == 1) {
                    $("#blk_sub_txt").text('Subdivision');
                    $("#gp_ward_txt").text('Ward');
                    $("#municipality_div").show();
                    $.each(subDistricts, function (key, value) {
                        if (value.district_code == select_district_code) {
                            htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
                        }
                    });
                }
                else {
                    $("#blk_sub_txt").text('Block/Subdivision');
                }
                $('#block').html(htmlOption);
            }

        });
        $('#block').change(function () {
            var block = $(this).val();
            var district = $("#district").val();
            var urban_code = $("#urban_code").val();
            if (district == '') {
                $('#urban_code').val('');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                alert('Please Select District First');
                $("#district").focus();

            }
            if (urban_code == '') {
                alert('Please Select Rural/Urban First');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                $("#urban_code").focus();
            }
            if (block != '') {
                var rural_urbanid = $('#urban_code').val();
                if (rural_urbanid == 1) {
                    var sub_district_code = $(this).val();
                    if (sub_district_code != '') {
                        $('#muncid').html('<option value="">--All --</option>');
                        select_district_code = $('#district').val();
                        var htmlOption = '<option value="">--All--</option>';
                        $.each(ulbs, function (key, value) {
                            if ((value.district_code == select_district_code) && (value.sub_district_code == sub_district_code)) {
                                htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
                            }
                        });
                        $('#muncid').html(htmlOption);
                    }
                    else {
                        $('#muncid').html('<option value="">--All --</option>');
                    }
                }
                else if (rural_urbanid == 2) {
                    $('#muncid').html('<option value="">--All --</option>');
                    $("#municipality_div").hide();
                    var block_code = $(this).val();
                    select_district_code = $('#district').val();

                    var htmlOption = '<option value="">--All--</option>';
                    $.each(gps, function (key, value) {
                        if ((value.district_code == select_district_code) && (value.block_code == block_code)) {
                            htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
                        }
                    });
                    $('#gp_ward').html(htmlOption);
                    $("#gp_ward_div").show();


                }
                else {
                    $('#muncid').html('<option value="">--All --</option>');
                    $("#municipality_div").hide();
                }
            }
            else {
                $('#muncid').html('<option value="">--All --</option>');
                $('#gp_ward').html('<option value="">--All --</option>');
            }

        });
        $('#muncid').change(function () {
            var muncid = $(this).val();
            var district = $("#district").val();
            var urban_code = $("#urban_code").val();
            if (district == '') {
                $('#urban_code').val('');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                alert('Please Select District First');
                $("#district").focus();

            }
            if (urban_code == '') {
                alert('Please Select Rural/Urban First');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                $("#urban_code").focus();
            }
            if (muncid != '') {
                var rural_urbanid = $('#urban_code').val();
                if (rural_urbanid == 1) {
                    var municipality_code = $(this).val();
                    if (municipality_code != '') {
                        $('#gp_ward').html('<option value="">--All --</option>');
                        var htmlOption = '<option value="">--All--</option>';
                        $.each(ulb_wards, function (key, value) {
                            if (value.urban_body_code == municipality_code) {
                                htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
                            }
                        });
                        $('#gp_ward').html(htmlOption);
                    }
                    else {
                        $('#gp_ward').html('<option value="">--All --</option>');
                    }
                }

                else {
                    $('#gp_ward').html('<option value="">--All --</option>');
                    $("#gp_ward_div").hide();
                }
            }
            else {
                $('#gp_ward').html('<option value="">--All --</option>');
            }

        });
    });

</script>