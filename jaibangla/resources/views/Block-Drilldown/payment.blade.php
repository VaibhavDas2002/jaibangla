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
        {{$type}} Payment Failure of </b> {{$district_name}} </b>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><i class="fa fa-clock-o"></i> Date :> <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span></li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
      <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ $message }} </strong>
      </div>
      @endif
      @if(count($errors) > 0)
      <div class="alert alert-danger alert-block">
        <ul>
          @foreach($errors->all() as $error)
          <li><strong> {{ $error }}</strong></li>
          @endforeach
        </ul>
      </div>
      @endif
      <div class="box box-default">
        <div class="box-body">
          <div class="row">
            <div class="form-group col-md-4">
              <label class=" control-label">Scheme Name</label>
              <select class="form-control select2 full-width "  name="level1a" id='level1a'>
                <option value="">--All--</option>
                @foreach($schemes as $scheme)
                <option value="{{$scheme->id}}">{{$scheme->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-2">
              <label class=" control-label">Level</label>
              <select class="form-control select2 full-width "  name="level3" id='level3'>
                <option value="">--All--</option>
                <option value="Rural">Rural</option>
                <option value="Urban">Urban</option>
              </select>
            </div>
            <div class="form-group col-md-2" style="margin-top: 23px;">
              <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
              <!-- <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button> -->
            </div>
            <input type="hidden" name="_token" id="token1" value="{{ csrf_token() }}">
            <input type="hidden" id="level1data" name="level1data" value="{{$type}}">
            <input type="hidden" id="level2data" name="level2data">
            <input type="hidden" id="level3data" name="level3data">
            <input type="hidden" id="level4data" name="level4data">
            <input type="hidden" id="level1adata" name="level1adata">
            <input type="hidden" id="level1bdata" name="level1bdata">
            <input type="hidden" id="level1cdata" name="level1data">
          </div>
          <p style="border: 1px solid whitesmoke;"></p>
          <table id="example" class="display" cellspacing="0" width="100%">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="district_code" id="district_code" value="{{ $district_code }}">
            <thead>
              <tr role="row">
                <th></th><th></th>
                <th class="sorting_asc" tabindex="0" aria-controls="example2" colspan="3">Failure from {{$type}} due to Wrong Account No</th>
              </tr>
              <tr role="row" style="font-size: 12px;">
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending">Level</th>
                <th  width="35%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending">Block/Sub-Division Names</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Total</th>     
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Corrected</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Pending</th>       
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th></th><th></th><th></th><th></th><th></th>    
              </tr>
            </tfoot>
          </table> 
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

$('#posting_place_div').hide();
$('#level1').change(function () {

   level1_val=$('option:selected', this).val();
   
  $('#level1data').val(level1_val);

  $('#level2data').removeAttr('value');
 
  $('#level3data').removeAttr('value');
  
  $('#level4data').removeAttr('value');
  

});





$('#level1a').change(function(){
  
   level1a_val=$('option:selected', this).val();
  $('#level1adata').val(level1a_val);
  
  console.log(level1a_val); 
});

level3_val=0;
$('#level3').change(function(){
  
   level3_val=$('option:selected', this).val();
  //$('#level1adata').val(level1a_val);
  
  console.log(level3_val);
});
level1a_val="";
level3_val="";
$('#filter').click(function(){
  
  level1a_val=$('#level1a').children('option:selected').val();
    // level1b_val=$('#level1b').children('option:selected').val();
    // level1c_val=$('#level1c').children('option:selected').val();
    level3_val=$('#level3').children('option:selected').val();

    $('#level1adata').val(level1a_val);
    // $('#level1bdata').val(level1b_val);
    // $('#level1cdata').val(level1c_val);
    $('#level3data').val(level3_val);

    table.clear().draw();
    table.ajax.reload();
        
 });

$('#reset').click(function(){
        $('#level1a').val('');
        $('#level3').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
});

//  console.log("hi");
var table=$('#example').DataTable( {
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":50,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "oLanguage": {
        "sProcessing": '<div class="preloader1" align="center"><img src=\'{{ asset("images/ZKZg.gif") }}\' width="150px"></div>' 
      },
      "scrollX": true,
      "serverSide": true,
      "processing":true,
       "bRetrieve": true,
      "ajax": 
      {
        url: "{{ url('getdatas_payment') }}",
        type: "POST",
        data:function(d){
            d.level1a=  $('#level1adata').val(),
            d.level2= $('#district_code').val(),
            d.level3=  $('#level3data').val(),
            d.type = $('#level1data').val();
            d._token= "{{csrf_token()}}"
          }
      } ,
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
            total = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            rectified = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            pending = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );    

 
            // Update footer
            $( api.column( 1 ).footer() ).html(
                "Total: "
            );
            $( api.column( 2 ).footer() ).html(
                total
            );
            $( api.column( 3 ).footer() ).html(
              rectified
            );
            $( api.column( 4 ).footer() ).html(
              pending
            );
        },
       "columns": [
                { "data": "level","defaultContent":"Null" },
                { "data": "block_name","defaultContent":"Null" },
                { "data": "failed","defaultContent":"0" },
                { "data": "rectified","defaultContent":"0" },
                { "data": "pending","defaultContent":"0" },
            ], 
        "columnDefs": [
                { "orderable": false, },
                { "targets": 2, "className": "text-left", },
                { "targets": 3, "className": "text-left", },
                { "targets": 4, "className": "text-left", },
              ],  
      "buttons": [
       {
           extend: 'pdf',
           title: 'Failure from {{$type}} due to Wrong Account No',
           messageTop: function () {
            //return(level1a_val);
                    if ( level1a_val == 1 ) {
                      if( level3_val=='Rural'){
                        return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Jai Johar (for ST)\n Level:Block';
                      }
                      if( level3_val=='Urban'){
                        return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Jai Johar (for ST)\n Level:Municipality';
                      }
                      return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Jai Johar (for ST)';
                    }
                    else if ( level1a_val == 2 ) {
                      if(level3_val=='Rural'){
                        return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Manabik\n Level: Block';
                      }
                      if(level3_val=='Urban'){
                        return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Manabik\n Level: Municipality';
                      }
                      return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Manabik';
                      //return 'Manabik';
                    }
                    else if( level1a_val ==3){
                      if(level3_val=='Rural'){
                        return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Taposili Bandhu(for SC)\n Level:Block';
                      }
                      if(level3_val=='Urban'){
                        return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Taposili Bandhu(for SC)\n Level:Municipality';
                      }

                      return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: Taposili Bandhu(for SC)';
                      //return 'Taposili Bandhu(for SC)';
                    }
                    else {
                      if(level3_val=='Rural'){
                        return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: '+$( "#level1a option:selected" ).text()+'\n Level:Block';
                      }
                      if(level3_val=='Urban'){
                        return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: '+$( "#level1a option:selected" ).text()+'\n Level:Municipality';
                      }

                      return 'Filter Criteria:\n District:{{$district_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: '+$( "#level1a option:selected" ).text();
                      //return 'Taposili Bandhu(for SC)';
                    }
                },
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4],

            }
       },
      ],

    } );
});

</script>