<style type="text/css">
    .has-error
    {
      border-color:#cc0000;
      background-color:#ffff99;
    }
    .preloader1{
      position: fixed;
      top:40%;
      left: 52%;
      z-index: 999;
    }
    .preloader1 {
      background: transparent !important;
    }
    #loadingDi {
      position:absolute;
      top:0px;
      right:0px;
      width:100%;
      height:100%;
      background-color:#fff;
      background-image:url('../images/ajaxgif.gif');
      background-repeat:no-repeat;
      background-position:center;
      z-index:10000000;
      opacity: 0.4;
      filter: alpha(opacity=40); /* For IE8 and earlier */
    }
    .loadingDivModal{
      position:absolute;
      top:0px;
      right:0px;
      width:100%;
      height:100%;
      background-color:#fff;
      background-image:url('../images/ajaxgif.gif');
      background-repeat:no-repeat;
      background-position:center;
      z-index:10000000;
      opacity: 0.4;
      filter: alpha(opacity=40); /* For IE8 and earlier */
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
              JB To DBT Shared Report
            </h1>
            <ol class="breadcrumb">
              <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDi"></div> 
                    <div class="panel panel-default">
                        <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span id="panel-icon">Enter Filter Criteria</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (($message = Session::get('success')) )
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
                                                <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                                                <select name="scheme_id" id="scheme_id" class="form-control" tabindex="6">
                                                    <option value="">--All --</option>
                                                    @foreach ($schemes as $scheme)
                                                    <option value="{{$scheme->dbt_scheme_code}}"> {{$scheme->scheme_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span id="error_scheme_id" class="text-danger"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="required-field">Financial Year <span class="text-danger">*</span></label>
                                                <select name="fin_year" id="fin_year" class="form-control" tabindex="6">
                                                    <option value="">--All --</option>
                                                    @foreach ($fin_year as $years)
                                                    <option value="{{$years->FinancialYear}}">{{$years->FinancialYear}}</option>
                                                    @endforeach
                                                </select>
                                                <span id="error_fin_year" class="text-danger"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Month</label>
                                                <select name="fin_month" id="fin_month" class="form-control" tabindex="6">
                                                <option value="">--All --</option>
                                                @foreach ($months as $key => $month)
                                                <option value="{{$key}}">{{$month}}</option>
                                                @endforeach
                                            </select>
                                            <span id="error_fin_month" class="text-danger"></span>
                                            </div>
                                            
                                            <div class="col-md-3" style="margin-top: 24px;">
                                                <button class="btn btn-primary" name="search" id="search" type="button" disabled><i class="fa fa-search"></i> Search</button>&nbsp;
                                                {{-- <button class="btn btn-default" name="reset_btn" id="reset_btn" type="button" disabled><i class="fa fa-refresh"></i> Reset</button> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="res_div" style="display: none;">
                        <div class="panel panel-default">
                          <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                          <div class="panel-body" style="padding: 5px; font-size: 14px;">
                            <div class="table-responsive">
                              <table id="example" class="table display" cellspacing="0" width="100%"> 
                                <thead style="font-size: 12px;">
                                    <th>Month</th>
                                    <th>Total Beneficiary</th>
                                    <th>Total Beneficiary Digitized</th>
                                    <th>Beneficiary Aadhar Seeded</th>
                                    <th>Mobile Captured</th>
                                    <th>Fund Transfer Cash(₹)</th>
                                    <th>Amount Transfer Cash Electronic(₹)</th>
                                    <th>Transfer Aadhar Seeded</th>
                                    <th>No. of DeDuplicated</th>
                                    <th>Saving Amount</th>
                                    <th>Remarks</th>
                                </thead>
                                <tbody style="font-size: 14px;"></tbody> 
                                <tfoot style="font-size: 14px;"> 
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>  
                              </table>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Modal -->
         <!-- Modal -->

    <!-- /.modal -->
        
    </div>
  @endsection
  <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
  <script>
    $(document).ready(function(){
        var interval = setInterval(function () {
        var momentNow = moment();
        $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
        $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        $('#loadingDi').hide();
        $('#update_details').hide();
        $('#search').removeAttr('disabled');
        var error_scheme_id = '';
        var error_fin_year = '';
        $('#search').click(function(){
            if($.trim($('#scheme_id').val()).length == 0){
            error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            }
            else{
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if($.trim($('#fin_year').val()).length == 0){
                error_fin_year = 'Financial Year is required';
                    $('#error_fin_year').text(error_fin_year);
                }
                else{
                    error_fin_year = '';
                    $('#error_fin_year').text(error_fin_year);
            }
            if( error_scheme_id != '' || error_fin_year != ''){
                    return false;
            }
            else{
            $('#loadingDi').show();
             $('#res_div').show();
                var msg = 'Scheme : '+$( "#scheme_id option:selected" ).text();
                $('#panel_head').text(msg);
                if ( $.fn.DataTable.isDataTable('#example') ) {
                    $('#example').DataTable().destroy();
                }
                $('#example tbody').empty();
                var table=$('#example').DataTable( {
                dom: 'Blfrtip',
                "scrollX": true,
                "paging": false,
                "searchable": true,
                "ordering":false,
                "bFilter": true,
                "bInfo": true,
                "pageLength":25,
                'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
                "serverSide": true,
                "processing":true,
                "bRetrieve": true,
                "oLanguage": {
                    "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
                },
                "ajax": 
                {
                    url: "{{ url('dbt-scheme-wise-getData') }}",
                    type: "post",
                    data:function(d){
                    d.scheme_id = $('#scheme_id').val(),
                    d.fin_year = $('#fin_year').val(),
                    d.fin_month = $('#fin_month').val(),
                    d._token= "{{csrf_token()}}"
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    $('#loadingDi').hide();
                    $('.preloader1').hide();
                    ajax_error(jqXHR, textStatus, errorThrown);
                    }
                },
                "initComplete":function(){
                    $('#loadingDi').hide();
                    //console.log('Data rendered successfully');
                },
                "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over this page
            TotalBen = api
                .column( 1, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            TotalBenDigitized = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            BenAadharSeeded = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 ); 
            MobileCaptured = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            FundTrnsferCash = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            AmntTrnsCashElectronic = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );         
            TrnsAadharSeeded = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );     
            NoDeDuplicated = api
                .column( 8, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );     
            
            SavingAmnt = api
                .column( 9, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 ); 
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
                "Total: "
            );
            $( api.column( 1 ).footer() ).html(
                TotalBen
            );
            $( api.column( 2 ).footer() ).html(
                TotalBenDigitized
            );
            $( api.column( 3 ).footer() ).html(
                BenAadharSeeded
            );
            $( api.column( 4 ).footer() ).html(
                MobileCaptured
            );
            $( api.column( 5 ).footer() ).html(
                FundTrnsferCash
            );
            $( api.column( 6 ).footer() ).html(
                AmntTrnsCashElectronic
            );
            $( api.column( 7 ).footer() ).html(
                TrnsAadharSeeded
            );
            $( api.column( 8 ).footer() ).html(
                NoDeDuplicated
            );
            $( api.column( 9 ).footer() ).html(
                SavingAmnt
            );                     
        },
                "columns": [
                    { "data": "ReportingMonth" },
                    { "data": "TotalBen" },
                    { "data": "TotalBenDigitized" },
                    { "data": "BenAadharSeeded" },
                    { "data": "MobileCaptured"},
                    { "data": "FundTrnsferCash"},
                    {"data":"AmntTrnsCashElectronic"},
                    { "data": "TrnsAadharSeeded" },
                    { "data": "NoDeDuplicated" },
                    { "data": "SavingAmnt" },
                    { "data": "Remarks" },
                ],
      
                "buttons": [
                                {
                                extend: 'pdf',
                                title: "JB To DBT Shared Report Generated On-@php
                                    date_default_timezone_set('Asia/Kolkata');
                                    $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                                    $date = $date->format('F j, Y g:i:a');
                                    echo $date;
                                @endphp ",
                                messageTop: "Date: @php
                                    date_default_timezone_set('Asia/Kolkata');
                                    $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                                    $date = $date->format('F j, Y g:i:a');
                                    echo $date;
                                @endphp",
                                footer: true,
                                pageSize:'A4',
                                orientation: 'landscape',
                                pageMargins: [ 40, 60, 40, 60 ],
                                exportOptions: {
                                        columns: [0,1,2,3,4,5,6,7,8,9,10],

                                    }
                                },
                                {
                                    extend: 'excel',
                                    title: "JB To DBT Shared Report Generated On-@php
                                    date_default_timezone_set('Asia/Kolkata');
                                    $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                                    $date = $date->format('F j, Y g:i:a');
                                    echo $date;
                                    @endphp ",
                                    messageTop: "Date: @php
                                        date_default_timezone_set('Asia/Kolkata');
                                        $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                                        $date = $date->format('F j, Y g:i:a');
                                        echo $date;
                                    @endphp",
                                    footer: true,
                                    pageSize:'A4',
                                    //orientation: 'landscape',
                                    pageMargins: [ 40, 60, 40, 60 ],
                                    exportOptions: {
                                            columns: [0,1,2,3,4,5,6,7,8,9,10],
                                            stripHtml: false,
                                        }
                                },
                            ],
                    });
                }
            });
            $('.js-municipality').change(function() {
                municipality=$('.js-municipality').val();  
                loadGPWard_1(municipality);
                // console.log('on change municipality:'+municipality);   
            });

            function loadGPWard_1(municipality) {  
                $('.js-wards').empty().append('<option value="">-- Select --</option>');   
                loadwards1(municipality, '../api/gpward/', '.js-wards');
            }    
            $('.modalEncloseClose').click(function(){
              $('.encolser_modal').modal('hide');
            }); 
    });
    function ajax_error(jqXHR, textStatus, errorThrown){
    var msg = "<strong>Failed to Load data.</strong><br/>";
    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
      msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
    } 
    else {
      if (jqXHR.responseJSON.hasOwnProperty('exception')) {
        msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
      } 
      else {
        msg += "Error(s):<strong><ul>";
        $.each(jqXHR.responseJSON, function (key, value) {
          msg += "<li>" + value + "</li>";
        });
        msg += "</ul></strong>";
      }
    }
    $.alert({
      title: 'Error!!',
      type: 'red',
      icon: 'fa fa-warning',
      content: msg,
    });
  }
  </script> 