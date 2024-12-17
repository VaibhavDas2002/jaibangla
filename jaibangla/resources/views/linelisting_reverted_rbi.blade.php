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
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       RBI Failed
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Payment Faliure</a></li>
        <li class="active">RBI</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="box box-default">
        <div class="box-header with-border">
          <div class="row">
            <div class="col-sm-8">
              <span class="text-success box-title" style="font-size: 16px;"><a href="{{ url('scheme-selection-revert-rbi') }}" class="btn btn-success btn-xs" ><i class="fa fa-arrow-left"></i> Back </a>@if(isset($scheme_name)) Scheme : <span style="font-size: 16px; font-weight: bold;">{{$scheme_name}}</span>@endif</span>
            </div>
            <div class="col-sm-4">
              <span class="text-warning" style="font-size: 12px; float: right; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </div>
          </div>
        </div>
        <div class="box-body">

          @if($duty_level=='BlockVerifier')
          <input type="hidden" name="dist_code" value="{{ $dist_code }}" class="js-district_1">
           <div class="row" style="">

            <div class="form-group col-md-4">
              <label class=" control-label" >Select Filter Criteria :Gram Panchayat</label>
                <select name="filter_1" id="filter_1" class="form-control select2 full-width" >
                    <option value="">-----Select----</option>
                     @foreach ($gps as $gp)
                            <option value="{{$gp->gram_panchyat_code}}" > {{$gp->gram_panchyat_name}}</option>
                    @endforeach

                </select>
            </div>          
            <div class="form-group col-md-4" style="margin-top: 23px;">
              <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
              <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
            </div>
          </div>
          @endif
          <p style="border: 1px solid whitesmoke;"></p>

          <table id="example" class="display" cellspacing="0" width="100%"> 
            <thead>
              <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <th  width="7%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Aplication ID</th>
                <th width="17%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Applicant Name</th>
                 <!--//sayantika-21-04-2020 start-->
                <th width="12%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Gram Panchayat Name</th>
                <!--//sayantika-21-04-2020 end-->
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank A/C No</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Name</th>
                 <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Branch</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">IFSC Code</th>               
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody style="font-size: 14px;"></tbody>
          </table>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>

  $(document).ready(function() {
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    fill_datatable();
    function fill_datatable(filter_1 = ''){

    var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      scrollX: true,
      paging: true,
      pageLength:20,
      lengthMenu: [[20, 50,100,500,1000, -1], [20, 50,100,500,1000, 'All']],
      "oLanguage": {
        "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="150px"></div>' 
      },
      processing: true,
      serverSide: true,
      ajax:{
        url: "{{ url('revert-rbi') }}",
        type: "POST",
        data:function(d){
          d.filter_1= filter_1,
          d._token= "{{csrf_token()}}"
        }                
      },
      columns: [
                
        { "data": "id" },
        { "data": "name" },
         //sayantika-21-04-2020 start
        { "data": "gp_ward_name"},
        //sayantika-21-04-2020 end
        { "data": "bank_code"},
        { "data": "bank_name" },
        { "data": "branch_name" },
        { "data": "bank_ifsc" },
        { "data": "view" },
       // { "data": "check" },
               

      ],          

      buttons: [
       {
           extend: 'pdf',
           title : 'RBI Failure',
           messageTop: 'Date: <?php echo date('d/m/Y'); ?>',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],

            }
       },
       {
           extend: 'print',
           title : 'RBI Failure',
           messageTop: 'Date: <?php echo date('d/m/Y'); ?>',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           title : 'RBI Failure',
           messageTop: 'Date: <?php echo date('d/m/Y'); ?>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );


   }

    $('#filter').click(function(){
        var filter_1 = $('#filter_1').val();
        if(filter_1 != '')
        {
            $('#example').DataTable().destroy();
            fill_datatable(filter_1);
        }
        else{
          alert('Please select two Filter Criterias');
        }
    });

      $('#reset').click(function(){
        $('#filter_1').val('');

        //sayantika-21-04-2020 start
        $('#select2-filter_1-container').text('---Select---');
        //sayantika-21-04-2020 end
        

        //$('#filter_2').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });


  } );
</script>

