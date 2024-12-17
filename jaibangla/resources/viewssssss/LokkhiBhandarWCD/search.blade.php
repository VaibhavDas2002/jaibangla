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
  #submitting
  {
   margin-top:20px;
  }
 .required-field::after {
    content: "*";
    color: red;
}

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
.greenDefault{
  color: green;
}
.yellowDefault{
   background-color: yellow;
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
            
              Search Lakkhi Bhandar Data
            
             </b></h3>
                <!-- <p><h3 class="box-title"><b>Bandhu Prakalpa (for SC)</b></h3></p> -->
            </div>

            <div>
             @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with Application ID: {{$id}}</strong>
               
               
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
            <form method="post" id="register_form" action="{{url('lb-wcd-search')}}"  class="submit-once" >
              {{ csrf_field() }}
              <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}"/>
            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Search Criteria</b></h4></div>
               <div class="panel-body">
               <div class="row">
                  

                        <div class="form-group col-md-4" id="search_textDiv">
                            <label class="required-field" id="search_txt_label">Family No.</label>

                             <input type="text" name="family_no" id="family_no" class="form-control"
                            placeholder="" maxlength="26" tabindex="1"  value="{{$fill_array['family_no']}}"/>
                            <span id="error_search_by" class="text-danger"></span>
                          </div>
                           <button type="submit"  name="submit" id="submitting" value="Submit" class="btn btn-success success btn-lg modal-search form-submitted" >Search </button>
                        </div>
                 
                <br />
               </div>
              </div>
             </div>

       <div class="tab-content" style="margin-top:16px;">

               <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
               <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
               <ul></ul>
               </div>
                @if($isSubmitted && $valid==0)
                <div class="alert alert-danger" role="alert">
                  {{$msg}}
                 </div>
               @endif
     @if($isSubmitted && $valid==1)
          <table id="example" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
                <th>AHL Tin.</th>
                <th>Member Name</th>
                <th>Sex</th>
                <th>Age</th>
                <th>Relationship</th>
                <th>Mobile No.</th>
                <th>Aadhaar No.</th>
                <th colspan="2">Action</th>
            </tr>
         </thead>
        <tbody>
         @php
          $i=1;
        @endphp
        @foreach($family_list_arr as $result)
       
           <tr>
                <td>{{$result->ahl_tin}}</td>
                <td>{{trim($result->ben_fname)}} {{trim($result->ben_mname)}} {{trim($result->ben_lname)}}</td>
                <td>{{$result->gender}}</td>
                <td>{{$result->ben_age}}</td>
                <td>{{$result->relationship}}</td>
                <td>{{$result->mobile_no}}</td>
                <td>{{$result->aadhar_no}}</td>
                <td>@if($result->hof==true) <span class="greenDefault" id="hof_{{$result->id}}">Default Family Head</span> @else <button type="button" class="btn btn-info markBtn" id="markbtn_{{$result->id}}">Mark as New Female Famaily Head</button>@endif </td> 
                <td><button type="button" class="btn btn-success procced" id="proccedbtn_{{$result->id}}" @if($result->hof==false) disabled @endif>Proceed</button> </td> 
           </tr>     
        @php
          $i=$i+1;
        @endphp
       @endforeach   
       </tbody>
       <tfoot>
       <tr>
       <td colspan="5" align="left">@if($valid==1) <a class="btn btn-danger" id="for_add" href="{{ url('lb-wcd?family_no='.$fill_array['family_no']) }}">Add New Female Family Head</a>@endif</td>
     
       </tr>
       </tfoot>
     @endif
  
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
var scheme_id='{{$scheme_id}}';
$('.is_head').click(function() {
      $('#for_edit').removeAttr("disabled");
});
$('.procced').click(function() {
       var id = $(this).attr('id');
       var new_id=id.split('_');
       window.location.href = "lb-wcd-family-edit?id="+new_id[1]+"&scheme_id="+scheme_id;
      return false;
     
});
$('.markBtn').click(function() {
       var id = $(this).attr('id');
       var new_id=id.split('_');
       
        var family_no =$("#family_no").val();
        var scheme_id =$("#scheme_id").val();
       $.ajax( 
        {
           url: '{{ url('lkwcd-mark-family-head') }}',
           type: 'GET',
            dataType: 'json', // type of response data
            data: {
                  family_no: family_no,
                  id: new_id[1],
                  _token: '{{ csrf_token() }}',
            },
            success: function (data,status,xhr) {   
              if(data.return_status){
                
                $("#proccedbtn_"+new_id[1]).removeAttr("disabled");
                $("#hof_"+new_id[1]).html("Default Family Head");
               
              }
              else{
                     $("#example").hide();
                     printMsg(data.return_msg,'0','errorDiv');
                    
            }
              
            },
            error: function (jqXhr, textStatus, errorMessage) { // error callback 
              alert('Something wrong..may be session timeout. please logout and then login again');
             
            }
        });
      
     
});
function getData(){
     var selected = $(".is_head:checked");
    // console.log(selected);
     if(!selected.val()){
        alert('No Female Head selected!')
     }
     else{
        var selectedValue = selected.val();
        var family_no =$("#family_no").val();
        var scheme_id =$("#scheme_id").val();
        $.ajax( 
        {
           url: '{{ url('lkwcd-mark-family-head') }}',
           type: 'GET',
            dataType: 'json', // type of response data
            data: {
                  family_no: family_no,
                  id: selectedValue,
                  _token: '{{ csrf_token() }}',
            },
            success: function (data,status,xhr) {   
              if(data.return_status){
               window.location.href = "lb-wcd-family-edit?id="+selectedValue+"&scheme_id="+scheme_id;
              }
              else{
                     $("#example").hide();
                     printMsg(data.return_msg,'0','errorDiv');
            }
              
            },
            error: function (jqXhr, textStatus, errorMessage) { // error callback 
              alert('Something wrong..may be session timeout. please logout and then login again');
              location.reload();
            }
        });
    }
    return false;
}
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


