<style>
  table.dataTable thead .sorting:after {
    content: none!important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #3c8dbc;
    border-color: #367fa9;
    padding: 1px 10px;
    color: #fff;
    font-size: 14px;
  }
</style>

<!--data table--->
<link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
<link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">
<!---data table end---->
@extends('add-scheme-existing-user.base')
@section('action-content')
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-12">
          <h3 class="box-title">Configurable Duty Setting</h3>
        </div> 
      </div>
    </div>
    <div class="box-body">
      <div class="row" style="">
        <div class="form-group col-md-8" >
          <label class=" control-label" >Search User Using Mobile Number</label>
          <input type="number" class="form-control" name="mobile" id="mobile"  pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;">
        </div>        
        <div class="form-group col-md-4" style="margin-top:22px">
          <button type="button" name="filter" id="filter" class="btn btn-info">Search</button>
          <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
        </div>
      </div>

      <table id="example" class="display" cellspacing="0" width="100%"> 
        <thead>
          <tr role="row" class="" style="font-size: 12px;">
            <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Name</th>
            <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">User Name</th>
            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Designation</th>
            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Mobile Number</th>
            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
            <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Scheme List</th>               
            <!-- <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>  -->
          </tr>
        </thead>
        <tbody>         
        </tbody>       
      </table>
    </div>
  </div> 
</section>

@endsection
<script src="{{asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")  }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script src="{{ URL::asset('js/site.js') }}"></script>

<!-- <script>
  $('.select2').select2();
</script> -->
<!---data table------->
<!--  <script src="{{ asset("js/jquery-1.12.4.js") }}"></script> -->
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script>

<!---data table end--->

<script>

  $(document).ready(function() {
  fill_datatable();
  function fill_datatable(mobile = ''){
      var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Brti',
      processing: true,
      serverSide: true,
      ajax:{
        url: "{{ url('add-scheme-existing-user') }}",
        type: "POST",
        data:function(d){
          d.filter_1= mobile,
          d._token= "{{csrf_token()}}"
        }                
      },
      columns: [        
        { "data": "emp_name" },
        { "data": "username"},
        { "data": "designation_id" },
        { "data": "mobile_no" },
        { "data": "email" },
        { "data": "scheme_list"},
        //{ "data": "action" }       
      ],
      drawCallback: function() {
     $('.select2').select2();
     }          
    });
   }
  $('#filter').click(function(){
    var mobile = $('#mobile').val();

    if(mobile != '')
    {
      $('#example').DataTable().destroy();
      fill_datatable(mobile);
    }
    else{
      alert('Please Enter Mobile Number');
    }
  });

  $('#reset').click(function(){
    $('#mobile').val('');
    $('#example').DataTable().destroy();
    fill_datatable();
  });
  } );
</script>

