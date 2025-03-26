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

    #loadingDi {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url('../images/ajaxgif.gif');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        /* For IE8 and earlier */
    }

    .loadingDivModal {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url('../images/ajaxgif.gif');
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

    #name_div {
        color: #0275d8;
        font-weight: 400;
    }

    #av_name_response {
        color: #5cb85c;
        font-weight: 400;
    }

    /* #failed_reason_id{
        color:#d9534f;
        
    } */
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Sarasori Mukhyamantri (CMO Grievance) Entry List
            </h1>
            <ol class="breadcrumb">
                <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                        class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDi"></div>
                    <div class="panel panel-default">
                        <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span
                                id="panel-icon">Enter Filter Criteria</div>
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
                                                <label class=" control-label">Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control select2" name="scheme_type" id='scheme_type'
                                                    required>
                                                    @foreach ($schemes as $scheme)
                                                        <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_scheme_type"></span>
                                            </div>
                                            @if($mapLevel == 'BlockOperator')
                                                {{-- <div class="col-md-3">
                                                    <label class=" control-label">Jai Bangla Municipality</label>
                                                    <select name="filter_1" id="filter_1"
                                                        class="form-control select2 full-width js-municipality">
                                                        <option value="">-----All----</option>
                                                        @foreach ($urban_bodys as $urban_body)
                                                        <option value="{{$urban_body->urban_body_code}}">
                                                            {{$urban_body->urban_body_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div> --}}
                                                {{-- <div class="col-md-3">
                                                    <label class=" control-label">Jai Bangla Wards</label>
                                                    <select name="filter_2" id="filter_2"
                                                        class="form-control select2 full-width js-wards">
                                                        <option value="">-----All----</option>
                                                    </select>
                                                </div> --}}

                                                
                                                <input type="hidden" name="local_body" id="local_body"
                                                    value={{$local_body_code}}>
                                            @endif
                                            <input type="hidden" name="mapLevel" id="mapLevel" value={{$mapLevel}}>
                                            @if($mapLevel != 'Department')
                                                <input type="hidden" name="district_code" id="district_code"
                                                    value="{{$district_code}}">
                                            @endif
                                            <div class="col-md-3" style="margin-top: 24px;">
                                                <button class="btn btn-primary" name="search_btn" id="search_btn"
                                                    type="button" disabled><i class="fa fa-search"></i>
                                                    Search</button>&nbsp;
                                                {{-- <button class="btn btn-default" name="reset_btn" id="reset_btn"
                                                    type="button" disabled><i class="fa fa-refresh"></i> Reset</button> --}}
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
                                style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table display" cellspacing="0" width="100%">
                                        <thead style="font-size: 12px;">
                                            <th>Grievance ID</th>
                                            <th>Caller Name</th>
                                            <th>Caller Mobile No</th>
                                            <th>CMO Received Date</th>
                                            <!-- <th >CMO GP/Ward Name</th> -->
                                            {{-- <th> Description</th> --}}
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
<script src="{{ URL::asset('js/confirmation_of_bank_account_validation.js') }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
    $(document).ready(function () {
        var interval = setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        $('#loadingDi').hide();
        $('#search_btn').removeAttr('disabled');
        var error_scheme_type = '';
        $('#search_btn').click(function () {
            tableLoaded();
        });
        function tableLoaded() {
            if ($.trim($('#scheme_type').val()).length == 0) {
                error_scheme_type = 'Scheme name is required';
                $('#error_scheme_type').text(error_scheme_type);
            }
            else {
                error_scheme_type = '';
                $('#error_scheme_type').text(error_scheme_type);
            }
            if (error_scheme_type != '') {
                return false;
            } else {
                $('#loadingDi').show();
                $('#res_div').show();
                var msg = 'Grievance List';
                $('#panel_head').text(msg);
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
                    "pageLength": 25,
                    'lengthMenu': [[10, 20, 25, 50, 100, -1], [10, 20, 25, 50, 100, 'All']],
                    "serverSide": true,
                    "processing": true,
                    "bRetrieve": true,
                    "oLanguage": {
                        "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
                    },
                    "ajax":
                    {
                        url: "{{ url('cmo-op_entryList') }}",
                        type: "post",
                        data: function (d) {
                            d.scheme_id = $('#scheme_type').val(),
                                d.filter_1 = $('#filter_1').val(),
                                d.filter_2 = $('#filter_2').val(),
                                d.mapLevel = $('#mapLevel').val(),
                                d.local_body = $('#local_body').val(),
                                d.process_type = $('#process_type').val(),
                                d.district_code = $('#district_code').val(),
                                d._token = "{{csrf_token()}}"
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#loadingDi').hide();
                            $('.preloader1').hide();
                            ajax_error(jqXHR, textStatus, errorThrown);
                        }
                    },
                    "initComplete": function () {
                        $('#loadingDi').hide();
                        //console.log('Data rendered successfully');
                    },
                    "columns": [
                        { "data": "grievance_id" },
                        { "data": "grievance_name" },
                        { "data": "sm_mobile_no" },
                        { "data": "cmo_receive_date" },


                        { "data": "view" }
                    ],
                    "buttons": [
                        {
                            extend: 'pdf',
                            footer: true,
                            pageSize: 'A4',
                            //orientation: 'landscape',
                            pageMargins: [40, 60, 40, 60],
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6],

                            }
                        },
                        {
                            extend: 'excel',
                            footer: true,
                            pageSize: 'A4',
                            //orientation: 'landscape',
                            pageMargins: [40, 60, 40, 60],
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                stripHtml: false,
                            }
                        },
                    ],
                });
            }
        }
        $('.js-municipality').change(function () {
            municipality = $('.js-municipality').val();
            loadGPWard_1(municipality);
            // console.log('on change municipality:'+municipality);   
        });
        function loadGPWard_1(municipality) {
            $('.js-wards').empty().append('<option value="">-- Select --</option>');
            loadwards1(municipality, '../api/gpward/', '.js-wards');
        }
        function loadwards1(municipality, path, selectInputClass) {
            var selectedVal = municipality;
            if (selectedVal == -1) {
                return;
            }
            // alert(path +'1/'+ selectedVal);
            $.ajax({
                type: 'GET',
                url: path + '1/' + selectedVal,
                success: function (datas) {
                    if (!datas || datas.length === 0) {
                        //alert("sucess with 0 data");
                        return;
                    }
                    //alert('success url:'paths);
                    for (var i = 0; i < datas.length; i++) {
                        $(selectInputClass).append($('<option>', {
                            //value: datas[i].name,
                            value: datas[i].id,
                            text: datas[i].name,
                            id: datas[i].id
                        }));
                    }
                },
                error: function (ex) {
                    //alert('error url:'paths);
                }
            });
        }
        $('.modalEncloseClose').click(function () {
            $('.encolser_modal').modal('hide');
        });
        $('entry_scheme_id').val() = '';
        $(document).on('click', '.entry_applicant', function () {
            var val = $(this).val();
            var array = val.split("_");
            var grievance_id = array[0];
            var scheme_id = array[1];
            var grievance_mobile_no = array[2];
            var data = {
                _token: '{{ csrf_token() }}',
                grievance_id: grievance_id,
                scheme_id: scheme_id,
                grievance_mobile_no: grievance_mobile_no
            };

            // $('entry_scheme_id').val(scheme_id)            

            // var filter_scheme_id = $('#scheme_type').val();
            // console.log(filter_scheme_id);
            // // var encrypted_scheme_id = "{{ encrypt(1 ) }}";

            // // Construct the URL dynamically with the JavaScript scheme_id
            // var url = 'jb-pension?scheme_id=' + encodeURIComponent(1) + '&type=4';

            // // Redirect using GET request
            // window.location.href = url;



        });
        tableLoaded();
    });
    function redirectPost(url, data, method = 'get') {
        var form = document.createElement('form');
        form.method = method;
        form.action = url;
        for (var name in data) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = data[name];
            form.appendChild(input);
        }
        $('body').append(form);
        form.submit();
    }
</script>