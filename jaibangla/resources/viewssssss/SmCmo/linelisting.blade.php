<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Jb | Jai Bangla</title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
      <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />  
  
   
   
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

   

   
   <style>
   .errorField{
    border-color: #990000;
  }
  .searchPosition{
    margin:70px;
  }
  .submitPosition{
    margin: 25px 0px 0px 0px;
  }
  .required-field::after {
      content: "*";
      color: red;
}
  
  .typeahead { border: 2px solid #FFF;border-radius: 4px;padding: 8px 12px;max-width: 300px;min-width: 290px;background: rgba(66, 52, 52, 0.5);color: #FFF;}
  .tt-menu { width:300px; }
  ul.typeahead{margin:0px;padding:10px 0px;}
  ul.typeahead.dropdown-menu li a {padding: 10px !important;  border-bottom:#CCC 1px solid;color:#FFF;}
  ul.typeahead.dropdown-menu li:last-child a { border-bottom:0px !important; }
  .bgcolor {max-width: 550px;min-width: 290px;max-height:340px;background:url("world-contries.jpg") no-repeat center center;padding: 100px 10px 130px;border-radius:4px;text-align:center;margin:10px;}
  .demo-label {font-size:1.5em;color: #686868;font-weight: 500;color:#FFF;}
  .dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover {
    text-decoration: none;
    background-color: #1f3f41;
    outline: 0;
  }
  table.dataTable thead th, table.dataTable thead td{
    padding:10px 13px;
  }
  table.dataTable tfoot th, table.dataTable tfoot td{
    padding:10px 5px;
  }

  .criteria1{
    text-transform: uppercase;
    font-weight: bold;
  }
  
  #example_length{
    margin-left: 40%;
    margin-top: 2px;
  }
  @keyframes spinner {
  to {transform: rotate(360deg);}
}
 
.spinner:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  width: 20px;
  height: 20px;
  margin-top: -10px;
  margin-left: -10px;
  border-radius: 50%;
  border: 2px solid #ccc;
  border-top-color: #333;
  animation: spinner .6s linear infinite;
}
.select2{
    width:100%!important;
  }
  .select2 .has-error {
    border-color:#cc0000;
   background-color:#ffff99;
}
</style>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Google Font -->
<link rel="stylesheet"
href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

</head>
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    @include('layouts.header')
    @include('layouts.sidebar')
    <div class="content-wrapper">
      <section class="content-header">
        <b>{{$type_des}} for the Scheme {{$scheme_name}}</b>
        <div class='row'>
          @if ( ($message = Session::get('message')))
            <div class="alert alert-success alert-block">
              <button type="button" class="close" data-dismiss="alert">×</button>
              <strong>{{ $message }}</strong>
            </div>
          @endif
          @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
              <button type="button" class="close" data-dismiss="alert">×</button>
              <strong>{{ $message }}</strong>
            </div>
          @endif
          @if ( ($error = Session::get('error')))
            <div class="alert alert-danger alert-block">
              <button type="button" class="close" data-dismiss="alert">×</button>
              <strong>{{ $error }}</strong>
            </div>
          @endif
          @if(count($errors) > 0)
            <div class="alert alert-danger alert-block">
              <ul>
                @foreach($errors as $error)
                  <li><strong> {{ $error }}</strong></li>
                @endforeach
              </ul>
            </div>
          @endif
        </div> 
      </section>
      <section class="content">
        <input type="hidden" name="action_type" id="action_type" value="1"/>
        <input type="hidden" name="dist_code" id="dist_code" value="{{ $district_code }}" class="js-district_1">
        <div class="row" style="">
          <div class="form-group col-md-4">
            <label class="required-field">Application Type</label>
            <select name="application_type" id="application_type" class="form-control"  >
              <option value="1" selected>Yet not Marked as Sarasori Mukhyamantri</option>
              <option value="2">Sent request to Operator for New Entry</option>
              <option value="3">Yet to Verified and Approved</option>
              <option value="4">Verified but Yet to Approved</option>
              <option value="5">Verified and Approved</option>
              <option value="6">Rejected</option>
            </select>
            <span id="error_application_type" class="text-danger"></span>
          </div>
          
          @if($verifier_type=='Subdiv')
            <div class="form-group col-md-3">
              <label class=" control-label" >Municipality</label>
              <select name="block_ulb_code" id="block_ulb_code" class="form-control select2 full-width js-municipality" >
                <option value="">-----Select----</option>
                @foreach ($urban_bodys as $urban_body)
                  <option value="{{$urban_body->urban_body_code}}" > {{$urban_body->urban_body_name}}</option>
                @endforeach
              </select>
            </div> 
        
            <input type="hidden" name="rural_urban_code"  id="rural_urban_code" value="{{$is_rural}}">
            <input value="{{$created_by_local_body_code}}" type="hidden" name="created_by_local_body_code"  id="created_by_local_body_code">
          @endif
          @if($designation_id=='Approver')
            <div class="form-group col-md-3">
              <label class=" control-label" >Urban/Rural</label>
              <select name="rural_urban_code" id="rural_urban_code" class="form-control" >
                  <option value="">-----All----</option>
                   @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}">{{$val}}</option>
                  @endforeach     
              </select>
            </div> 
            <div class="form-group col-md-3">
              <label class=" control-label" ><span id="blk_sub_txt">Block/Sub Division</span></label>
              <select name="created_by_local_body_code" id="created_by_local_body_code" class="form-control select2 full-width js-wards" >
                <option value="">-----Select----</option>
              </select>
            </div>
          @else 
            <input type="hidden" name="process_type"  id="process_type" value="">
          @endif
          
          <div class="form-group col-md-4">
            <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
            <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
          </div>
        </div>
     
        <table id="example" class="display" cellspacing="0" width="100%"> 
          <thead>
            <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
              <th width="12%">Name</th>
              <th width="10%">Cmo Grievance Mobile Number</th>
              @if($verifier_type=='Subdiv' || $verifier_type=='District')
                <th width="10%">Block/Munc Name</th>
              @endif
              <th width="17%">Jai Bangla Beneficiary ID</th> 
              <th width="20%">Action</th>
             
            </tr>
          </thead>
          <tbody>
          </tbody>     
        </table>
      
     
    

   
        </div>

        <form method="get"  action="{{url('checkCmo')}}"  class="submit-once" name="form" id="form-btn-processed">
         <input type="hidden" id="scheme_id" name="scheme_id" value="{{$scheme_id}}">
         <input type="hidden" id="cmo_id" name="cmo_id"/>
         <input type="hidden"  name="search_by_key" value="1"/>
         <input type="hidden"  name="search_by_value" id="search_by_value"/>

        </form>
      </section>
    </div>
  </div>
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>

<script>

  $(document).ready(function() {
    $("#confirm").hide();
    $("#submittingapprove").hide();
    $("#rejectingapprove").hide();
    $("#revertingapprove").hide();
    $(".NumOnly").keyup(function(event) {
              
              $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
    }); 
    var dataTable = "";
    var base_url='{{ url('/') }}';
    var block_ulb_code=$("#block_ulb_code").val();
    var application_type=$("#application_type").val();
    var process_type=$("#process_type").val();
  fill_datatable(block_ulb_code,application_type,process_type);
  function fill_datatable(block_ulb_code = '',application_type = '',process_type = ''){
    if ( $.fn.DataTable.isDataTable('#example') ) {
          $('#example').DataTable().destroy();
        }
   // console.log(process_type);
       var scheme_id=$("#scheme_id").val();
         dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      paging: true,
      pageLength:100,
      ordering: false,
      lengthMenu: [[20, 50,100,500,1000], [20, 50,100,500,1000]],
      processing: true,
      serverSide: true,
      ajax:{
            url: "{{ url('mark-sm-cmo-list') }}",
            type: "GET",
            data:function(d){
                 d.block_ulb_code= block_ulb_code,
                 d.scheme_id= scheme_id,
                 d.type= $('#type').val(),
                 d.application_type= application_type,
                 d.process_type= process_type,
                 d._token= "{{csrf_token()}}"
            },
            error: function (ex) {
              //console.log(ex);
             alert('Session time out..Please login again');
            // window.location.href=base_url;
           }                       
      },
      columns: [
                
        { "data": "name" },
        { "data": "mobile_no" },
        @if($verifier_type=='Subdiv')
        { "data": "block_ulb_name" },
        @endif
        { "data": "jb_beneficiary_id" },
        { "data": "action" },        

      ],          

    
    } );


   }

    $('#filter').click(function(){
        var block_ulb_code = $('#block_ulb_code').val();
        var application_type = $('#application_type').val();
        var process_type = $('#process_type').val();
        var designation_id = $('#designation_id').val();
        var error_application_type='';
        var error_process_type='';
        if(application_type=='')
        {
          error_application_type = 'Application Type is required';
          $('#error_application_type').text(error_application_type);
          $('#application_type').addClass('has-error');
        }
        else
        {
          error_application_type = '';
          $('#error_application_type').text(error_application_type);
          $('#application_type').removeClass('has-error');
        }
        if(designation_id=='Approver'){
        if(process_type=='')
        {
          error_process_type = 'Process Type is required';
          $('#error_application_type').text(error_process_type);
          $('#process_type').addClass('has-error');
        }
        else
        {
          error_process_type = '';
          $('#error_process_type').text(error_process_type);
          $('#process_type').removeClass('has-error');
        }
      }
      else{
        error_process_type=''
      }
        if(error_application_type=='' && error_process_type==''){
          //console.log(process_type);
          $('#example').DataTable().destroy();
          fill_datatable(block_ulb_code,application_type,process_type);
        }
        
       
    });
    $('#block_ulb_code').change(function() {
      var municipality_code=$(this).val();
       if(municipality_code!=''){
        $('#gp_ward').html('<option value="">--All --</option>');
        var htmlOption='<option value="">--All--</option>';
          $.each(ulb_wards, function (key, value) {
                if(value.urban_body_code==municipality_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        $('#gp_ward_code').html(htmlOption);
       }
       else{
          $('#gp_ward_code').html('<option value="">--All --</option>');
       } 
    });
    $('#rural_urban_code').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#created_by_local_body_code').html('<option value="">--All --</option>'); 
        }
        $('#created_by_local_body_code').html('<option value="">--All --</option>'); 
        select_district_code= $('#dist_code').val();
       //console.log(select_district_code);
        
        select_body_type= urban_code;
        var htmlOption='<option value="">--All--</option>';
        if(select_body_type==2){
            $("#blk_sub_txt").text('Block');
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $("#blk_sub_txt").text('Subdivision');
            $.each(subDistricts, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        } 
        else{
          $("#blk_sub_txt").text('Block/Subdivision');
        }   
        $('#created_by_local_body_code').html(htmlOption);
        

    });
      $('#reset').click(function(){
        $('#application_type').val('');
        $('#process_type').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });
    $(document).on('click', '.btn-processed', function() {
      $('#form-btn-processed #beneficiary_id').val('');
      $('.btn-processed').attr('disabled',false);
      var benid=$(this).val();
      var search_by_value = $(this).attr("search_by_value")
      $('#btn-processed-'+benid).attr('disabled',true);
      $('#form-btn-processed #cmo_id').val(benid);
      $('#form-btn-processed #search_by_value').val(search_by_value);

      $('#form-btn-processed').submit();
    });
 
  } );

</script>

</body>
</html>