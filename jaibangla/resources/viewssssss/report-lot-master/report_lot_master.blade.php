<section class="content">
  <div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block successErrorMessage">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>{{ $message }}</strong>
    </div>
    @elseif ($message = Session::get('danger'))
    <div class="alert alert-danger alert-block successErrorMessage">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>{{ $message }}</strong>
    </div>
    @endif
  </div>
  {{-- <h3>Report Lot Master</h3> --}}

  <input style="display: none" type="hidden" value="{{$status}}" id="record_status">
  <table id="example" class="display" cellspacing="0" width="100%">

    <thead>
      <tr role="row" class="sorting_asc" style="font-size: 12px;">
        <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Name: activate to sort column descending">Serial No</th>
        <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Name: activate to sort column descending">Lot No</th>
        <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Name: activate to sort column descending">Year Month</th>
        <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Name: activate to sort column descending">Status</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">No. of Beneficiary</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">No. of IFMS Wrong Data</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">Pmt Mandate</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">No. of RBI Failed</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">No. of RBI Success</th>
        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending" style="text-align: center">File Name</th>
        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending" style="text-align: center">Action</th>
      </tr>
    </thead>
    <tbody>

      @php $i=1; @endphp
      @foreach($reports as $report)
      <tr>
        <td>@php print $i++; @endphp</td>
        <td>{{ $report->lot_no }}</td>
        <td>{{ $report->lot_year }} {{ $report->lot_month }}</td>
        <td>
          @php
          if($report->push_to_ifms_status==1 and $report->dotdone_status=='' and $report->ack_status=='' and
          $report->wrongdata_status=='' and
          $report->voucher_no == '')
          {print 'Pushed to IFMS';}
          elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status=='' and
          $report->wrongdata_status=='' and
          $report->voucher_no == '')
          {print 'Pushed to IFMS<br />Received by IFMS';}
          elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status==1 and
          $report->wrongdata_status=='' and
          $report->voucher_no == '')
          {print 'Pushed to IFMS<br />Received by IFMS<br />Reference generated<br />Ref# '.$report->ref_no.'<br />No
          Wrong Data';}
          elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status==1 and
          $report->wrongdata_status==1 and
          $report->voucher_no == '')
          {print 'Pushed to IFMS<br />Received by IFMS<br />Reference generated<br />Ref# '.$report->ref_no.'<br />Wrong
          Data received';}
          elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status==1 and
          $report->wrongdata_status=='' and
          $report->voucher_no > 0)
          {print 'Pushed to IFMS<br />Received by IFMS<br />Reference generated<br />Ref# '.$report->ref_no.'<br />No
          Wrong Data<br />RBI Report received';}
          elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status==1 and
          $report->wrongdata_status==1 and
          $report->voucher_no > 0)
          {print 'Pushed to IFMS<br />Received by IFMS<br />Reference generated<br />Ref# '.$report->ref_no.'<br />Wrong
          Data received<br />RBI Report received';}
          elseif($report->ref_no==-1 and $report->ack_status==-1)
          {print 'Defunct Lot';}
          elseif($report->lot_status=1 and $report->push_to_ifms_status=='' and $report->dotdone_status=='' and
          $report->ack_status=='' and
          $report->wrongdata_status=='' and $report->voucher_no =='')
          {print 'Lot ready to Push to IFMS';}
          elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status=='' and
          $report->wrongdata_status==1 and
          $report->voucher_no == '')
          {print 'Pushed to IFMS<br />Received by IFMS<br />Alert: No Reference generated<br />Wrong Data received';}
          elseif($report->lot_status=0 and $report->push_to_ifms_status=='' and $report->dotdone_status=='' and
          $report->ack_status=='' and
          $report->wrongdata_status=='' and $report->voucher_no =='')
          {print 'Alert: Lot Pushed--No confirmation from IFMS';}
          elseif($report->lot_status=0 and $report->push_to_ifms_status=='' and $report->dotdone_status=='' and
          $report->ack_status=='' and
          $report->wrongdata_status==1 and $report->voucher_no =='')
          {print 'Alert: ONLY Wrongdata file received';}
          /*elseif($report->repeat_lot==1)
          {print 'Repeat lot generated vide lot no- '.$report->repeat_drn_part;} */

          @endphp

        </td>

        {{-- <form method="POST" action="{{ route('lot_payment_xls_generate') }}"> --}}
          <td style="text-align: center">
            {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
            <input type="hidden" name="error_type" value="COUNT"> --}}
            <button class="btn btn-xs btn-margin excel_btn" onmouseover="$(this).toggleClass('btn-primary');"
              onmouseout="$(this).toggleClass('btn-primary');" style="font-size: 16px;"
              title="Total Beneficiary - {{ $report->ben_count }}" value="{{$report->lot_no}}_{{$report->scheme_id}}" data-toggle="tooltip"
              data-placement="bottom">
              {{ $report->ben_count }}
            </button>
          </td>
        {{-- </form> --}}
        @if($report->ifms_wrongdata_count != '')
        {{-- <form method="POST" action="{{ route('lot_payment_xls_generate') }}"> --}}
          <td style="text-align: center;">
            {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
            <input type="hidden" name="error_type" value="E1"> --}}
            <button class="btn btn-xs btn-margin excel_btn_ifms_failed" onmouseover="$(this).toggleClass('btn-danger');"
              onmouseout="$(this).toggleClass('btn-danger');" style="font-size: 16px;"
              title="IFMS Wrong Data - @php if($report->ifms_wrongdata_count == '') {print '0';} else {print $report->ifms_wrongdata_count;} @endphp"
              value="{{$report->lot_no}}_{{$report->scheme_id}}" data-toggle="tooltip"
              data-placement="bottom">
              @php if($report->ifms_wrongdata_count == '') {print '0';} else {print $report->ifms_wrongdata_count;}
              @endphp
            </button>
          </td>
        {{-- </form> --}}
        @else
        <td style="text-align: center">@php if($report->ifms_wrongdata_count == '') {print '0';} else {print
          $report->ifms_wrongdata_count;} @endphp</td>
        @endif
        <td style="text-align: center">@php if($report->pmt_mandate == '' or $report->ack_status=='') {print '0';} else
          {print $report->pmt_mandate;} @endphp</td>
        @if($report->rbi_failed_count != '')
        {{-- <form method="POST" action="{{ route('lot_payment_xls_generate') }}"> --}}
          <td style="text-align: center">
            {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
            <input type="hidden" name="error_type" value="E2"> --}}
            <button class="btn btn-xs btn-margin excel_btn_failed" onmouseover="$(this).toggleClass('btn-danger');"
              onmouseout="$(this).toggleClass('btn-danger');" style="font-size: 16px;"
              title="RBI Failed - @php if($report->rbi_failed_count == '') {print '0';} else {print $report->rbi_failed_count;} @endphp"
              value="{{$report->lot_no}}_{{$report->scheme_id}}" data-toggle="tooltip"
              data-placement="bottom">
              @php if($report->rbi_failed_count == '') {print '0';} else {print $report->rbi_failed_count;} @endphp
            </button>
          </td>
        {{-- </form> --}}
        @else
        <td style="text-align: center">@php if($report->rbi_failed_count == '') {print '0';} else {print
          $report->rbi_failed_count;} @endphp</td>
        @endif
        @if($report->rbi_success_count != '')
        {{-- <form method="POST" action="{{ route('lot_payment_xls_generate') }}"> --}}
          <td style="text-align: center">
            {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
            <input type="hidden" name="error_type" value="S0"> --}}
            <button class="btn btn-xs btn-margin excel_btn_success" onmouseover="$(this).toggleClass('btn-success');"
              onmouseout="$(this).toggleClass('btn-success');" style="font-size: 16px; "
              title="RBI Success - @php if($report->rbi_success_count == '') {print '0';} else {print $report->rbi_success_count;} @endphp"
              value="{{$report->lot_no}}_{{$report->scheme_id}}" data-toggle="tooltip"
              data-placement="bottom">
              @php if($report->rbi_success_count == '') {print '0';} else {print $report->rbi_success_count;} @endphp
            </button>
          </td>
        {{-- </form> --}}
        @else
        <td style="text-align: center">@php if($report->rbi_success_count == '') {print '0';} else {print
          $report->rbi_success_count;} @endphp</td>
        @endif
        <td style="text-align: center">{{$report->file_name}}</td>

        @if($report->lot_status==1 and $report->ref_no!=-1 and $report->ack_status!=-1)
        @if($report->pmt_mandate==0)
        <td style="text-align: center">
          <i class="glyphicon glyphicon-ok"></i>
        </td>
        @else
        {{-- <form method="POST" action="{{ route('push-to-ifms.export') }}" class="submit-once"
        onSubmit="if(!confirm('Please click on OK if you are sure to export the Lot to IFMS')){return false;}"> --}}
        <td>
          <!-- <input type="hidden" name="_method" value="DELETE"> -->
          {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
          <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
          <input type="hidden" name="lot_year" value="{{$report->lot_year}}">
          <input type="hidden" name="lot_month" value="{{$report->lot_month}}"> --}}
          
          {{-- <button type="button" id="pushtoifms_btn_{{$report->lot_no}}_{{$report->scheme_id}}"
            class="btn btn-info btn-margin"
            value="" disabled>
          Push Temporarily Suspended.
          </button> --}}

          <button type="button" id="pushtoifms_btn_{{$report->lot_no}}_{{$report->scheme_id}}"
            class="btn btn-info btn-margin pushtoifms_btn"
            value="{{$report->lot_no}}_{{$report->scheme_id}}_{{$report->lot_year}}_{{$report->lot_month}}">
            Push to IFMS
          </button>


        </td>
        {{-- </form> --}}
        @endif
        @elseif($report->lot_status==0 and $report->push_to_ifms_status ==1 and $report->dotdone_status =='' and
        $report->ref_no!=-1 and $report->ack_status!=-1)
        {{-- <form class="row" method="POST" action="{{ route('receive_status') }}"> --}}
        <td>
          {{-- <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
          <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
          <input type="hidden" name="lot_year" value="{{$report->lot_year}}">
          <input type="hidden" name="lot_month" value="{{$report->lot_month}}">
          <!-- <input type="hidden" name="_method" value="DELETE"> -->
          <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}

          <button type="button" id="ifmsreceived_btn_{{$report->lot_no}}_{{$report->scheme_id}}"
            class="btn btn-success btn-margin ifmsreceived_btn"
            value="{{$report->lot_no}}_{{$report->scheme_id}}_{{$report->lot_year}}_{{$report->lot_month}}">
            IFMS received?
          </button>
        </td>
        {{-- </form> --}}
        @elseif($report->lot_status==0 and $report->push_to_ifms_status ==1 and $report->dotdone_status==1 and
        $report->ack_status=='' and $report->ref_no!=-1 and $report->ack_status!=-1)
        {{-- <form method="POST" action="{{ route('ack_status') }}" class="submit-once"
        onSubmit="if(!confirm('Please click on OK if you are sure that the Lot has been billed in IFMS and submitted to
        treasury')){return false;}"> --}}
        <td>
          {{-- <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
          <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
          <input type="hidden" name="lot_year" value="{{$report->lot_year}}">
          <input type="hidden" name="lot_month" value="{{$report->lot_month}}"> --}}
          <!-- <input type="hidden" name="_method" value="DELETE"> -->
          {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}



          <button type="button" id="treasurysubmit_btn_{{$report->lot_no}}_{{$report->scheme_id}}"
            class="btn btn-danger btn-margin treasurysubmit_btn"
            value="{{$report->lot_no}}_{{$report->scheme_id}}_{{$report->lot_year}}_{{$report->lot_month}}">
            Submitted To Treasury
          </button>
        </td>
        {{-- </form> --}}
        @elseif($report->lot_status==0 and $report->push_to_ifms_status ==1 and $report->dotdone_status==1 and
        $report->ack_status==1 and $report->voucher_no =='' and $report->ref_no!=-1 and $report->ack_status!=-1)
        @if($report->rbi_flag)
        {{-- <form method="POST" action="{{ route('rbi_payment_status') }}" class="submit-once"
          onSubmit="if(!confirm('Please click on OK if you are sure to import the RBI Report')){return false;}"> --}}
          <!--<form method="GET" action="{{ route('wrong_file_test') }}" class="submit-once" onSubmit="if(!confirm('Please click on OK if you are sure to import the RBI Report')){return false;}">-->
          <td>
            {{-- <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
            <input type="hidden" name="lot_year" value="{{$report->lot_year}}">
            <input type="hidden" name="lot_month" value="{{$report->lot_month}}">
            <!-- <input type="hidden" name="_method" value="DELETE"> -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
            <button type="button" id="importrbi_btn_{{$report->lot_no}}_{{$report->scheme_id}}"
              class="btn btn-warning btn-margin importrbi_btn"
              value="{{$report->lot_no}}_{{$report->scheme_id}}_{{$report->lot_year}}_{{$report->lot_month}}">
              Import RBI Report
            </button>
          
          </td>
        {{-- </form> --}}
        @else
        <td style="text-align: center">
          Complete RBI Report Pending
        </td>
        @endif
        @elseif($report->ref_no==-1 and $report->ack_status==-1)
        <td style="text-align: center">
          <i class="glyphicon glyphicon-remove"></i>
        </td>
        @else
        @if($report->rbi_flag and $report->repeat_lot!=1)
        {{-- <form method="POST" action="{{ route('rbi_payment_status') }}" class="submit-once"
          onSubmit="if(!confirm('Please click on OK if you are sure to import the RBI Report again!')){return false;}"> --}}
          <!--<form method="GET" action="{{ route('wrong_file_test') }}" class="submit-once" onSubmit="if(!confirm('Please click on OK if you are sure to import the RBI Report')){return false;}">-->
          <td>
            {{-- <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
            <input type="hidden" name="lot_year" value="{{$report->lot_year}}">
            <input type="hidden" name="lot_month" value="{{$report->lot_month}}">
            <!-- <input type="hidden" name="_method" value="DELETE"> -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}

          

            <button type="button" id="reimportrbi_btn_{{$report->lot_no}}_{{$report->scheme_id}}"
              class="btn btn-warning btn-margin reimportrbi_btn"
              value="{{$report->lot_no}}_{{$report->scheme_id}}_{{$report->lot_year}}_{{$report->lot_month}}">
              Re-Import RBI Report
            </button>
          </td>
        {{-- </form> --}}
        @else
        <td style="text-align: center">
          <i class="glyphicon glyphicon-ok"></i>
        </td>
        @endif
        @endif
      </tr>
      @endforeach
    </tbody>
    <!-- <tfoot> -->

    <!-- </tfoot> -->
  </table>

  </div>

  </div>

  <!-- /.row -->

</section>



<script>
  $(function(){
  
    $('[data-toggle="tooltip"]').tooltip();
    $('.excel_btn').click(function(){
      var val = $(this).val();
      var array = val.split("_");
      var lot_no = array[0];
      var scheme = array[1];
      var  data= {'_token': '{{csrf_token()}}', 'lot_no': lot_no, 'scheme_id': scheme, 'error_type': 'COUNT'};
      redirectPostExcel('{{route("lot_payment_xls_generate_new")}}', data, 'get');
    });
    
    $('.excel_btn_ifms_failed').click(function(){
      var val = $(this).val();
      var array = val.split("_");
      var lot_no = array[0];
      var scheme = array[1];
      var  data= {'_token': '{{csrf_token()}}', 'lot_no': lot_no, 'scheme_id': scheme, 'error_type': 'E1'};
      redirectPostExcel('{{route("lot_payment_xls_generate_new")}}', data, 'get');
    });
    $('.excel_btn_failed').click(function(){
      var val = $(this).val();
      var array = val.split("_");
      var lot_no = array[0];
      var scheme = array[1];
      var  data= {'_token': '{{csrf_token()}}', 'lot_no': lot_no, 'scheme_id': scheme, 'error_type': 'E2'};
      redirectPostExcel('{{route("lot_payment_xls_generate_new")}}', data, 'get');
    });

    $('.excel_btn_success').click(function(){
      var val = $(this).val();
      var array = val.split("_");
      var lot_no = array[0];
      var scheme = array[1];
      var  data= {'_token': '{{csrf_token()}}', 'lot_no': lot_no, 'scheme_id': scheme, 'error_type': 'S0'};
      redirectPostExcel('{{route("lot_payment_xls_generate_new")}}', data, 'get');
    });
  $('.pushtoifms_btn').click(function(){
    var val = $(this).val();
    $.confirm({
          title: 'Confirm!',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<strong>Please click on Confirm if you are sure to export the Lot to IFMS ?</strong>',
          buttons: {
            confirm: function () {
              commonifms("{{ route('push-to-ifms.export') }}",val)
            },
            cancel: function () {
            }
          }
        });
   
    
    
    });

    $('.ifmsreceived_btn').click(function(){
      var val = $(this).val();
      commonifms("{{ route('receive_status') }}",val)
     });
    
     $('.treasurysubmit_btn').click(function(){
      var val = $(this).val();
      $.confirm({
          title: 'Confirm!',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<strong>Please click on Confirm if you are sure that the Lot has been billed in IFMS and submitted to treasury.</strong>',
          buttons: {
            confirm: function () {
              commonifms("{{ route('ack_status') }}",val)
            },
            cancel: function () {
            }
          }
        });
      });
      $('.importrbi_btn').click(function(){
        var val = $(this).val();
        $.confirm({
          title: 'Confirm!',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<strong> Please click on Confirm if you are sure to import the RBI Report.</strong>',
          buttons: {
            confirm: function () {
              commonifms("{{ route('rbi_payment_status') }}",val)
            },
            cancel: function () {
            }
          }
        });

       
      });

      $('.reimportrbi_btn').click(function(){
        var val = $(this).val();
        $.confirm({
          title: 'Confirm!',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<strong> Please click on Confirm if you are sure to import the RBI Report again.</strong>',
          buttons: {
            confirm: function () {
              commonifms("{{ route('rbi_payment_status') }}",val)
            },
            cancel: function () {
            }
          }
        });

       
      });
      
$('#example').DataTable( {
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":20,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "scrollX": true,
      buttons: [
       {
           extend: 'pdf',
           title: 'Lot Report- IFMS Payment',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9],

            }
       },
       {
           extend: 'excel',
           title: 'Lot Report- IFMS Payment',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,3,4,5,6,7,8,9],
                stripHtml: true,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    });
});


function reload_table(){
    $('#loader_img').show();
    $('#res_div').show();
    $(".content").addClass("disabledcontent");
    var msg = 'Scheme : '+$( "#select_scheme option:selected" ).text()+' , Financial Year : '+$('#lot_year').val()+' , Month : '+$( "#lot_month option:selected" ).text();
    $.ajax({
      url: "{{ route('report_lot_master_main') }}",
      method: 'post',
      data: {
        select_scheme: $('#select_scheme').val(),
        lot_year: $('#lot_year').val(),
        lot_month: $('#lot_month').val(),
        lot_status:$('#lot_status').val(),
        _token:"{{csrf_token()}}"
      },
      success: function(result) {
        $('#loader_img').hide();
        $('#res_div').show();
        $('#ifmslot_data').html('');
        $('#ifmslot_data').html(result);
        $('#panel_head').text(msg);
        $(".content").removeClass("disabledcontent");
      },
      error: function (jqXHR, textStatus, errorThrown) {
	$(".content").removeClass("disabledcontent");
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }
function commonifms(route,val){
  $('#loader_img').show();
  $(".content").addClass("disabledcontent");
        // $('#pushmodal_sbi').modal('show');
        
      
         var array = val.split("_");
         var lot_no = array[0];
         var scheme = array[1];
         var lotyear = array[2];
         var lotmonth = array[3];
         $.ajax({
           url: route,
           method: 'post',
           data: {
             scheme_id: scheme,
             lot_no: lot_no,
             lotyear:lotyear,
             lotmonth:lotmonth,
             _token:"{{csrf_token()}}"
           },
           success: function(result) {
             $('#loader_img').hide();
             $(".content").removeClass("disabledcontent");
             $.confirm({
                     title: result.title,
                     type: result.type,
                     icon: result.icon,
                     content: result.msg,
                     buttons: {
                       ok: function () {
                         reload_table();
                       }
                     }
                   });
            
           
       
            
           },
           error: function (jqXHR, textStatus, errorThrown) {
             $('#load_div').hide();
            // $('#pushmodal_sbi').modal('hide');
	     $(".content").removeClass("disabledcontent");
             ajax_error(jqXHR, textStatus, errorThrown);
           }
         });
}
  function ajax_error(jqXHR, textStatus, errorThrown){
    $('#loader_img').hide();
        $('#res_div').show();
      
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
          buttons: {
            ok: function () {
             // reload_table();
            }
          }
        });
  }

  function redirectPostExcel(url, data , method = 'get'){
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