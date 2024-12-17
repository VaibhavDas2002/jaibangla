<!DOCTYPE html>

<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
     <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">

  <!-- Font Awesome -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->
  <!-- Ionicons -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />

   <!-- bootstrap wysihtml5 - text editor -->
  <!-- <link rel="stylesheet" href="{{ asset("/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}"> -->

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />

  <style>
  .box
  {
   width:800px;
   margin:0 auto;
  }
  .active_tab1
  {
   background-color:#fff;
   color:#333;
   font-weight: 600;
  }
  .inactive_tab1
  {
   background-color: #f5f5f5;
   color: #333;
   cursor: not-allowed;
  }
  .has-error
  {
   border-color:#cc0000;
   background-color:#ffff99;
  }
  .select2{
    width:100%!important;
  }
  .select2 .has-error {
    border-color:#cc0000;
   background-color:#ffff99;
}
.modal_field_name{
  float:left;
  font-weight: 700;
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}

.modal_field_value{
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}
.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
  margin-top: 1%!important;
}

.section1{
    border: 1.5px solid #9187878c;
    margin: 2%;
    padding: 2%;
}
.color1{
  margin: 0%!important;
  background-color: #5f9ea061;
}

.modal-header{
  background-color: #7fffd4;
}
.required-field::after {
    content: "*";
    color: red;
}
 .imageSize{
  font-size: 9px;
  color: #333;
 }

  </style>


</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  @include('layouts.header')
  <!-- Sidebar -->
  @include('layouts.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
  <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div> <!-- class="box box-primary" -->
            <div class="box-header with-border">
             <h3 class="box-title"><b>
            
              Save Lakkhi Bhandar Data to PDF
            
             </b></h3>
                <!-- <p><h3 class="box-title"><b>Bandhu Prakalpa (for SC)</b></h3></p> -->
            </div>

            <div>
             @if (!empty($msg))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $msg }}</strong>
               
               
              </div>
              @endif
               @if ($message = Session::get('error') )
              <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
               
               
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
             <!--   @if ($message = Session::get('failure'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif -->
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" id="register_form" action="{{url('lkwcd-download-pdf-admin')}}"  class="submit-once" >
              {{ csrf_field() }}
       <input type="hidden" name="code" id="code" value="{{$code}}">
            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Search Criteria</b></h4></div>
               <div class="panel-body">

               

               <div class="row">
                
               
               
                
               
              
                 
              <div class="form-group col-md-4" id="">
                <label class="required-field">District</label>
                
                <select name="district_code" id="district_code" class="form-control" tabindex="10" >
                  <option value="">--Select  --</option>
                  @foreach($districts as $distric)
                  <option value="{{$distric->district_code}}" @if( $fill_array['district_code'] == $distric->district_code)  selected  @endif >{{trim($distric->district_name)}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_district_code" class="text-danger"></span>
              </div>
             
             
                
              <div class="form-group col-md-4" id="divUrbanCode">
                <label class="required-field">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control" tabindex="10" >
                  <option value="">--Select  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}" @if( $fill_array['urban_code'] == $key)  selected  @endif >{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>
             
                <div class="form-group col-md-4" id="divBodyCode">
                <label class="required-field">Block/Municipality</label>
                
                <select name="block" id="block" class="form-control" tabindex="11" >
                  <option value="">--Select --</option>
                  @if(count($block_munc)>0)
                     @foreach($block_munc as $blk)
                     @if($fill_array['urban_code']==1)
                     <option value="{{$blk->urban_body_code}}" @if( $fill_array['block'] == $blk->urban_body_code)  selected  @endif >{{trim($blk->urban_body_name)}}</option>
                     @else
                     <option value="{{$blk->block_code}}" @if( $fill_array['block'] == $blk->block_code)  selected  @endif >{{trim($blk->block_name)}}</option>
                     @endif
                     @endforeach    
                  @endif
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>
                <div class="form-group col-md-4" id="divBodyCode">
                            <label class="required-field">GP/Ward No.</label>

                            <select name="gp_ward" id="gp_ward" class="form-control"
                              tabindex="155">
                              <option value="">--Select --</option>

                          @if(count($gp_ward)>0)
                                              @foreach($gp_ward as $gpward)
                                              @if($fill_array['urban_code']==1)
                                              <option value="{{$gpward->urban_body_ward_code}}" @if( $fill_array['gp_ward'] == $gpward->urban_body_ward_code)  selected  @endif >{{trim($gpward->urban_body_ward_name)}}</option>
                                              @else
                                              <option value="{{$gpward->gram_panchyat_code}}" @if( $fill_array['gp_ward'] == $gpward->gram_panchyat_code)  selected  @endif >{{trim($gpward->gram_panchyat_name)}}</option>
                                              @endif
                                              @endforeach    
                            
                          @endif
                            </select>
                            <span id="error_gp_ward" class="text-danger"></span>
                          </div>
                          <div class="form-group col-md-4" id="divBodyCode">
                            <label class="required-field">Limit</label>

                               <select name="limit_no" id="limit_no" class="form-control" tabindex="10" >
                              <option value="">--Select Limit --</option>
                              @foreach($limit_array as $key=>$val)
                              <option value="{{$val}}" @if( $fill_array['limit_no'] == $val)  selected  @endif >{{$val}}</option>
                              @endforeach     
                              
                            </select>
                            <span id="error_limit_no" class="text-danger"></span>
                          </div>


                       

                        </div>
              
            
            
 
                
              
                 
                   
                <div class="col-md-12" align="center">

                  <button type="submit" name="submit" value="Submit" class="btn btn-success" >Save to PDF </button>
                 
                
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                
                 
                <br />
               </div>
              </div>
             </div>

       <div class="tab-content" style="margin-top:16px;">
        @if ($issubmitted && $valid==1)
              <div class="alert alert-info alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>Total No Benficiary: {{$total_data}}, Beneficiary Count Pdf generated: {{$already_pdf_data}},Pending Beneficiary Yet to Be Pdf generated: {{$total_data-$already_pdf_data}}</strong>
               
               
              </div>
        @endif        
       @if($issubmitted  && count($old_files)>0)
       <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
            <th>Sl No.</th>
            <th>File Name</th>
            <th>Operation</th>
            </tr>
         </thead>   
         <tbody>
       
        @foreach($old_files as $d_file)
        @php
          $i=1;
          $file_name='';
          $explode_f_name=explode('/',$d_file);
          $file_name = array_pop($explode_f_name);
        @endphp
           <tr>
           <td>{{$i}}</td>
           <td>{{$file_name}}</td>
           <td><a target="_blank" href="lk_download_pdf_static?file_name={{$d_file}}" title="DownLoad PDF">Download</a></td>
           </tr>
        @php
        $i=$i+1;
        @endphp
        @endforeach   
       @endif
         

  </tbody>
  </table>



           </form>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
        
      </div>
    

      
</section>

    <!-- Main content -->
   <!--  <section class="content">

      Your Page Content Here



    </section> -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  @include('layouts.footer')
  
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}" type="text/javascript" ></script>
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<script src="{{ URL::asset('js/site.js') }}"></script>

<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script>

$(document).ready(function(){
$('#district_code').change(function() {
   $("#urban_code").val('');
   $('#block').html('<option value="">--Select --</option>');
   $('#gp_ward').html('<option value="">--Select --</option>');
});
$('#urban_code').change(function() {
       var urban_code=$(this).val();
       
        $('#block').html('<option value="">--Select --</option>');
        $('#gp_ward').html('<option value="">--Select --</option>');
        select_district_code= $('#district_code').val();
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district").focus();
               $("#urban_code").val('');
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--Select--</option>';
        if(select_body_type==2){
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $.each(ulbs, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }    
        $('#block').html(htmlOption);
        }
});
$('#block').change(function() {
      var block=$(this).val();
      var district=$("#district_code").val();
      var urban_code=$("#urban_code").val();
     if(urban_code==''){
        alert('Please Select Rural/Urban First');
        $("#block").val('');
        $("#gp_ward").val('');
        $("#urban_code").focus();
    }
    else{
       select_body_type= urban_code;
      // alert(select_body_type);
        var htmlOption='<option value="">--Select--</option>';
        if(select_body_type==2){
            $.each(gps, function (key, value) {
                if(value.district_code==district && value.block_code==block){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $.each(ulb_wards, function (key, value) {
                if((value.urban_body_code==block)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }
        $('#gp_ward').html(htmlOption);
    }
    });

});
function printMsg (msg,msgtype,divid) {
            $("#"+divid).find("ul").html('');
            $("#"+divid).css('display','block');
			if(msgtype=='0'){
				//alert('error');
				$("#"+divid).removeClass('alert-success');
				//$('.print-error-msg').removeClass('alert-warning');
				$("#"+divid).addClass('alert-warning');
			}
			else{
				$("#"+divid).removeClass('alert-warning');
				$("#"+divid).addClass('alert-success');
			}
			if(Array.isArray(msg)){
            $.each( msg, function( key, value ) {
                $("#"+divid).find("ul").append('<li>'+value+'</li>');
            });
			}
			else{
				$("#"+divid).find("ul").append('<li>'+msg+'</li>');
			}
  }
   function closeError(divId){
   $('#'+divId).hide();
  }
  
</script>
</body>
</html>


