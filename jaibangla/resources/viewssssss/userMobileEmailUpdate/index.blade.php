
<style type="text/css">
.requied{
  color:red;
}
.hasError {
  border: 2px solid red;
  border-radius: 4px;
}
#menu_role_mapping_panel{
  margin-left:250px;
  margin-top: 120px;
}

.modal-full {
    min-width: 80%;
  
}
#duty_add #btnaddrole{
  margin-top:25px;
  margin-left:10px;
}
.row{
  margin-right: 10px!important;
  margin-left: 60px!important;
  margin-top: 1%!important;
}
.form-control{
 width:95%!important;
}
#btnSearch{
  margin-top:20px;
  margin-left:10px;
}
#bulkSearch{
  margin-top:25px;
  margin-left:10px;
}
#btnaddrole{
  margin-top:25px;
  margin-left:10px;
}
#duty_add{
  margin-top:25px;
  margin-left:10px;
}
</style>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
@extends('users-mgmt.base')
@section('action-content')
  <section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
            <div class="col-sm-8"></div>
        </div>
      </div>
      <div class="box-body">
        @if(count($errors) > 0)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
        <div class="alert alert-danger alert-block">
          <ul>
          @foreach($errors->all() as $error)
          <li><strong> {{ $error }}</strong></li>
          @endforeach
          </ul>
        </div>
        @endif
        <div class="panel-group">
          <div class="panel panel-default">
           
            <div id="scheme_workflow" class="panel-collapse collapse show">
              <div class="panel-body" id="level_map"> <!-- hidden by default-->
                <div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
                <div class="alert print-error-msg" style="display:none" id="crud_msg_Crud">
                <button type="button" class="close"  aria-label="Close" onclick="closeError('crud_msg_Crud')"><span aria-hidden="true">&times;</span></button>
                <ul></ul></div>
            
                <form id="Searchmodal" >
                  
                  
                 
                  
                 
                 
                      <div class="form-group col-md-3">
                             <label class="control-label">Stake Level</label>
                           
                                <select class="form-control" name="stake_level" id="stake_level">
                                <option value="">Select Stake Level</option>
                               
                                   <option value="Subdiv" >Subdiv</option>
                                   <option value="Block" >Block</option>
                                        
                                </select>
                                
                           
                        </div>
                 
                 
                 
                  
                 
                  <button type="button" id="btnSearch" class="btn btn-primary">Search</button>
                 
                 </form>
                    
                  <div class="col-md-12 text-center" id="loaderdiv" hidden>
                    <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
                  </div>  
              
                  <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
                  <div class="row">
                           
                  <div class="col-md-9"></div>
                            <div class="col-md-3 pull-right">
                                <input id="dtSearch" type="text" class="form-control" name="dtSearch" value="" autocomplete="off" >

                               
                            </div>
                  </div>
                  <br/>
                    <table id="example" class="display" cellspacing="0" width="100%">
                      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                      <thead>
                        <tr role="row">
                          
                         
                          <th width="25%" class="text-left">User Name</th>     
                          <th width="7%">Designation</th>
                          <th width="7%">Mobile Number</th>
                          <th width="7%">Email</th>
                         
                          <th width="15%" class="text-left">Action</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                        
                        
                          <th width="25%" class="text-left">User Name</th>     
                          <th width="7%">Designation</th>
                          <th width="7%">Mobile Number</th>
                          <th width="7%">Email</th>
                         
                          <th width="15%" class="text-left">Action</th>
                        </tr>
                      </tfoot>     
                    </table>  
                    <div class="row">
                      <div class="col-sm-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                          
                        </div>
                      </div>
                    </div>  
                  </div>
                </div>
              </div>
              <!-- <div class="panel-footer">Panel Footer</div> -->
            </div>
          </div>
      
        </div>        
      </div>
    </div>



<!--Add Update Level Modal -->
<div id="UserformModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-full">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><span class="crud-txt">Add User</span></h4>
      </div>
      <div class="modal-body">
        <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader" width="50px" height="50px"  ></div>
        <div class="alert print-error-msg" style="display:none" id="crud_msg_CrudModal">
        <button type="button" class="close"  aria-label="Close" onclick="closeError('crud_msg_CrudModal')"><span aria-hidden="true">&times;</span></button><ul></ul></div>
        <form id="userform" class="form-horizontal">
                        
            <input type="hidden" name="id" id="id" value="">
            <div class="row">   
            <div class="form-group col-md-3">
                            <label for="username" class="control-label">User Name:</label>

                            
                               <span id="usernametxt"></span>

                               
                           
                        </div>
                     
                       
                        
                               


                        

                 

                        <div class="form-group col-md-3">
                            <label for="email" class="control-label">Designation:</label>

                           
                            <span id="designationtxt"></span>

                               
                            
                        </div>
                     
            </div> 
                  
            <div class="row">   
            <div class="form-group col-md-3">
                            <label for="mobile_no" class="control-label">Mobile Number <span class="requied">*</span></label>

                            
                                <input id="mobile_no" type="text" class="form-control NumOnly" name="mobile_no" value="" maxlength="10">

                               
                           
                        </div>
                     
                       
                        
                               


                        

                 

                        <div class="form-group col-md-9">
                            <label for="email" class="control-label">E-Mail Address <span class="requied">*</span></label>

                           
                                <input id="email" type="email" class="form-control" name="email" value="">

                               
                            
                        </div>
            </div> 
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-submit" >
        <span class="crud-txt">Add</span>
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
                        
                       
                       
                   
      
 <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!--<script src="{{ asset ("js/treeview.js") }}" type="text/javascript"></script>
<script src="{{ asset ("js/duellistUser.js") }}" type="text/javascript"></script>
<link href="{{ asset ("css/treeview.css") }}" rel="stylesheet">
<link href="{{ asset ("css/duellist.css") }}" rel="stylesheet"> -->

<script type="text/javascript"> 
 
var table=""; 
var listItemtable = "";
var myTable = "";

function fill_datatable(stake_level = '',district_code = '',search_value = ''){
  if(table!=null && table != ''){
    $('#example').DataTable().destroy();
    //alert(service_designation_id_old);
  }
  
    table=$('#example').DataTable( {
      dom: "Blfrtip",
      "paging": true,
      "pageLength":10,
      "lengthMenu": [[10,20, 50, 80, 120, 150, 180, 500,1000, 2000], [10,20, 50, 80, 120, 150, 180, 500,1000, 2000]],
      "serverSide": true,
      "deferRender": true,
      "processing":true,
      "bRetrieve": true,
      "ordering":false,
      "searching": false,
      "language": {
        "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
      },
      "ajax": {
        url: "{{ url('userMobileEmailUpdate/userManagementSearch') }}",
        type: "GET",
        data:{
          stake_level:stake_level,
          district_code:district_code,
          search_value:search_value

        }
      } ,
      "columns": [
        { "data": "username","defaultContent":"" },
        { "data": "designation_id_old","defaultContent":"" },
        { "data": "mobile_no"},
        { "data": "email"},
        { "data": "action"} 
      ],
     
      
    }); 
    //console.log( table.data() );
  }
$(document).ready(function(){ 
  $('.txtOnly').keypress(function (e) {
            var regex = new RegExp(/^[a-zA-Z\s]+$/);
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            else {
                e.preventDefault();
                return false;
            }
    });
  $(".NumOnly").keyup(function(event){
              $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
       }
  }); 
  $('.special-char').keyup(function()
  {
    var yourInput = $(this).val();
    re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
    var isSplChar = re.test(yourInput);
    if(isSplChar)
    {
      var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
      $(this).val(no_spl_char);
    }
  });
   $(".dataTables_scrollHeadInner").css({"width":"100%"});
 
   $(".table ").css({"width":"100%"}); 

  fill_datatable();


});
$("#btnSearch").click(function(){
  
    var stake_level='';
    var district_code='';
    var stake_level=$("#stake_level").val();
    var district_code=$("#district_code_home").val();
    fill_datatable(stake_level,district_code);
});





  function addUpdateUserForm(id){
    $(".print-error-msg").hide();
    $("#email").val(''); 
    $("#mobile_no").val(''); 
    $("#usernametxt").text(''); 
    $(".submit_loader").hide();
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    if(id){
      
      $(".submit_loader").show();
    $.ajax({
      type: 'POST',  
      url: "{{url('userMobileEmailUpdate/getUserInfo') }}",
      data:{
        id: id
      },
      dataType: 'json',
      success: function (data) {
        if (!data) {
          $(".submit_loader").hide();
          return;
        }else{
          
          $("#email").val(data.email); 
          $("#mobile_no").val(data.mobile_no); 
          $("#id").val(data.id); 
          //alert(data.username);
          $("#usernametxt").text(data.username); 
          $("#designationtxt").text(data.designation_id_old); 
          $(".submit_loader").hide(); 

        }
        
      },
      error: function (ex) {
        $(".submit_loader").hide();
        alert("problem loading value");
      }
    });
    }
    if(id){
     
      var crud_txt='Update User';
    }
    else{

      var crud_txt='Add User';
    }
    $("#id").val(id);
    $(".crud-txt").text(crud_txt);
    $("#UserformModal").modal();
  
  }
$("#dtSearch").keyup(function(){
                var s_val = $("input[name='dtSearch']").val(); 
                var stake_level=$("#stake_level").val();
                var district_code=$("#district_code_home").val();
                fill_datatable(stake_level,district_code,s_val);
               
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
$("#btn-submit").click(function(){
    
    $email = $("#email").val();
    $mobile_no = $("#mobile_no").val();
   
    $id = $('#id').val();
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    //alert($id);
    $.ajax({
      url: "{{url('userMobileEmailUpdate/updateMobileOrEmail') }}",
      type:'POST',
      dataType: "json",
      data: { 
        id:$id,
        email:$email,
        mobile_no:$mobile_no
      },
      success: function(data) {
        //console.log(data);
        if(data.return_status){
          $("#UserformModal").modal('hide');
          table.ajax.reload(null,false);
          $("html, body").animate({ scrollTop: "0" }); 
          printMsg(data.return_msg,'1','crud_msg_Crud');
          
        }else{
          $('#UserformModal').animate({ scrollTop: 0 }, 'slow');
          printMsg(data.return_msg,'0','crud_msg_CrudModal');
        }
        $('#btn-submit').prop('disabled', false);
      },
      error: function (ex) {
        $('#UserformModal').animate({ scrollTop: 0 }, 'slow');
        alert('Something wrong..may be session timeout. please logout and then login again');
        //location.reload();
        $('#btn-submit').prop('disabled', false);
      }
    });
  });
</script>
</section>
@endsection