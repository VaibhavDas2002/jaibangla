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
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Duplicate Reject Beneficiary
      </h1>
      <ol class="breadcrumb">
        <li class="active"><i class="fa fa-clock-o"></i> Date :> <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span></li>
      </ol>
    </section>
    <section class="content">
      <div class="box box-default">
        <!-- <div class="box-header with-border">
          <div class="row">
            <div class="col-sm-8">
              <h3 class="box-title">Lot Transaction IFMS</h3>
            </div>
          </div>
        </div> -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              @if (($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }} with Application ID: {{$id}}</strong>
                </div>
              @endif
              @if (($message = Session::get('message')))
                  <div class="alert alert-danger alert-block">
                      <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
                  </div>
              @endif
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group col-md-4">
                    <label class=" control-label">Scheme</label>
                    <select class="form-control select2" name="scheme"  id="scheme">
                      <option value="">---Select---</option>
                      @foreach($schemes as $scheme)
                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                      @endforeach
                    </select>
                    <span class="text-danger" id="error_scheme"></span>
                  </div>
                  <div class="form-group" style="margin-top: 23px;">
                    <button class="btn btn-info" name="filter" id="filter" type="button">Filter</button>
                    <button class="btn btn-default" name="reset" id="reset" type="button">Reset</button>
                  </div>
                </div>
              </div>
              <p style="border: 1px solid whitesmoke;"></p>
              <div id="res_div">
                <table id="example" class="display" width="100%">
                  <thead>
                    <tr role="row" style="font-size: 12px;">
                      <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Beneficiary ID</th>
                      <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Name</th>
                      <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Scheme Name</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Ration Card</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Block/Municipllity</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank A/c No.</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank IFSC</th>
                    </tr>
                  </thead>
                  <tbody style="font-size: 14px;"></tbody>       
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
$(document).ready(function(){
  // Live Clock
  var interval = setInterval(function () {
  var momentNow = moment();
    $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
    $('.time-part').html(momentNow.format('hh:mm:ss A'));
  }, 100);

  $('#loader_img').hide();
  //$('#res_div').hide();
  fill_datatable();
  function fill_datatable(filter_1 = ''){
    var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      scrollX: true,
      paging: true,
      pageLength:20,
      lengthMenu: [[20, 50,100,500,1000,2000, -1], [20, 50,100,500,1000,2000, 'All']],
      "oLanguage": {
        "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="150px"></div>' 
      },
      processing: true,
      serverSide: true,
      ajax:{
        url: "{{ url('report-duplicate-approve') }}",
        type: "POST",
        data:function(d){
          d.filter_1= filter_1,
          d._token= "{{csrf_token()}}"
        },
        error: function (jqXHR, textStatus, errorThrown) {
          ajax_error(jqXHR, textStatus, errorThrown);
        }                
      },
      columns: [
                
        { "data": "original_application_id" },
        { "data": "name" },
        { "data": "scheme_name"},
        { "data": "epic_voter_id"},
        { "data": "ration_card" },
        { "data": "block_ulb_name" },
        { "data": "bank_code" },
        { "data": "bank_ifsc" }
      ],          

      buttons: [
       {
          extend: 'pdf',
          title : 'Duplicate Approval Report',
          messageTop: 'Date: <?php echo date('d/m/Y'); ?>',
          footer: true,
          pageSize:'A4',
          orientation: 'landscape',
          pageMargins: [ 40, 60, 40, 60 ],
          exportOptions: {
              columns: [0,1,2,3,4,5,6,7],

          }
       },
       {
           extend: 'print',
           title : 'Duplicate Approval Report',
           messageTop: 'Date: <?php echo date('d/m/Y'); ?>',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           title : 'Duplicate Approval Report',
           messageTop: 'Date: <?php echo date('d/m/Y'); ?>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );


   }

    $('#filter').click(function(){
      var filter_1 = $('#scheme').val();
      if(filter_1 != '')
      {
        $('#example').DataTable().destroy();
        fill_datatable(filter_1);
      }
      else{
        alert('Please select filter criterias');
      }
    });

    $('#reset').click(function(){
      $('#scheme').val('');
      $('#select2-scheme-container').text('---Select---');
      $('#example').DataTable().destroy();
      fill_datatable();
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