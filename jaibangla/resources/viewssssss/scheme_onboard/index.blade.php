<style>
ul.timeline {
    list-style-type: none;
    position: relative;
}
ul.timeline:before {
    content: ' ';
    background: #d4d9df;
    display: inline-block;
    position: absolute;
    left: 29px;
    width: 2px;
    height: 100%;
    z-index: 400;
}
ul.timeline > li {
    margin: 20px 0;
    padding-left: 45px;
}
ul.timeline > li:before {
    content: ' ';
    background: white;
    display: inline-block;
    position: absolute;
    border-radius: 50%;
    border: 3px solid #22c0e8;
    left: 20px;
    width: 20px;
    height: 20px;
    z-index: 400;
}
</style>


@extends('scheme_onboard.base')
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
            <div class="panel-heading">
              <!-- <h3 class="panel-title">
                 <button class="btn btn-link" data-toggle="collapse" data-target="#scheme_workflow" aria-expanded="true" aria-controls="collapseOne"> 
                  Scheme Onboarding
                 </button> 
              </h3> -->
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="sel1">Select Scheme Type:</label>
                  <select class="form-control" id="sch_type">
                  <option value="">Select Scheme Type</option>
                  @foreach($scheme_types as $scheme_type)
                    <option value="{{$scheme_type->id}}">{{$scheme_type->scheme_type}}</option>
                  @endforeach  
                  </select>
                  <p id="add_scheme_type_div" hidden>
                    <input type="text" placeholder="Enter New Scheme Type" id="add_scheme_type">
                    <input type="hidden" id="action_type"/>
                    <button id="btn_add_new_scheme_type">Add <i class="fa fa-plus" aria-hidden="true"></i></button>
                    <button id="btn_add_close_scheme_type">Close <i class="fa fa-close" aria-hidden="true"></i></button>
                  </p>
                  <!-- <p id="update_scheme_type_div" hidden>
                    <input type="text" placeholder="Enter Updated Scheme Type" id="update_scheme_type">
                    <button id="btn_update_scheme_type">Update <i class="fa fa-edit" aria-hidden="true"></i></button>
                    <button id="btn_update_close_scheme_type">Close <i class="fa fa-close" aria-hidden="true"></i></button>
                  </p> -->
                  <button id="add_new_scheme_type">Add <i class="fa fa-plus" aria-hidden="true"></i></button>
                  <button id="edit_scheme_type" hidden>Update <i class="fa fa-edit" aria-hidden="true"></i></button>
                  <!-- <button id="edit_scheme_type" hidden>Update <i class="fa fa-edit" aria-hidden="true"></i></button> -->
                  <button id="list_scheme_type" onClick="listView(1)">List View <i class="fa fa-list" aria-hidden="true"></i></button>
                </div> 
                <div class="form-group col-md-6">
                  <label for="sel1">Select Scheme:</label>
                  <select class="form-control" id="sch">

                  </select>
                  <button id="add_new_scheme" hidden>Add <i class="fa fa-plus" aria-hidden="true"></i></button>
                  <button id="edit_scheme" hidden>Update <i class="fa fa-edit" aria-hidden="true"></i></button>
                  <button id="list_scheme" onClick="listView(2)" hidden>List View <i class="fa fa-list" aria-hidden="true"></i></button>

                </div> 
              </div>
            </div>
            <div id="scheme_workflow" class="panel-collapse collapse show">
              <div class="panel-body" id="level_map"> <!-- hidden by default-->
                <div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
                <div class="col-md-12 text-right" id="addButton" hidden>
                  <a class="btn btn-primary" href="javascript:void(0)" onClick="addUpdateLevelForm(0)">Add Level</a>
                </div>
                  <div class="col-md-12 text-center" id="loaderdiv" hidden>
                    <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
                  </div>  
              
                  <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
                    <table id="example" class="display" cellspacing="0" width="100%">
                      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                      <thead>
                        <tr role="row">
                          <th width="6%">Id</th>
                          <th width="25%" class="text-left">Role Name</th>
                          <th width="25%" class="text-left">User Level</th>     
                          <th width="7%">Parent Id</th>
                          <th width="6%">Entry Level</th>
                          <th width="6%">First Verifier</th>
                          <th width="6%" >Is Active</th>
                          <th width="15%" class="text-left">Action</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <th width="6%">Id</th>
                          <th width="25%" class="text-left">Role Name</th>
                          <th width="25%" class="text-left">User Level</th>     
                          <th width="7%">Parent Id</th>
                          <th width="6%">Entry Level</th>
                          <th width="6%">First Verifier</th>
                          <th width="6%" >Is Active</th>
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
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <!-- <a data-toggle="collapse" href="#menuRoleMapping">Menu Role Mapping</a> -->
                <button class="btn btn-link" data-toggle="collapse" data-target="#menuRoleMapping" aria-expanded="true" aria-controls="collapseOne">
                  <!-- User Role > Menu Mapping -->
                </button>
              </h4>
            </div>
            <div id="menuRoleMapping" class="panel-collapse collapse show">
              <div class="panel-body">
              <!--Workflow View start-->
                <div class="row" id="workflowdiv">
                  <!-- Workflow view area-->
                </div>
                <!--Workflow View end-->
              </div>
              <!-- <div class="panel-footer">Panel Footer</div> -->
            </div>
          </div>
        </div>        
      </div>
    </div>
  </section>
	

<!--Add Update Level Modal -->
<div id="levelformModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><span class="crud-txt">Add Map</span> Level</h4>
      </div>
      <div class="modal-body">
        <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader" width="50px" height="50px"  ></div>
        <div class="alert alert-danger print-error-msg" style="display:none"><ul></ul></div>
        <form id="levelform" class="form-horizontal">
            {{ csrf_field() }}                      
            <input type="hidden" name="id" id="id" value="">
            <div class="form-group">
              <label class="col-md-4 control-label">Scheme type: </label>
              <div class="col-md-6">
                <label class="form-control" id="scheme_type" name="scheme_type">
                </label> 
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label">Scheme Name: </label>
              <div class="col-md-6">
                <label class="form-control" id="scheme_name" name="scheme_name">
                </label> 
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label">User Role: <span class="requied">*</span></label>
              <div class="col-md-6">
                <select class="form-control select2" id="user_role" name="user_role">
                </select> 
              </div>
            </div>    
            <div class="form-group">
              <label class="col-md-4 control-label">Parent Role: <span class="requied">*</span></label>
              <div class="col-md-6">
                <select class="form-control" id="parent_role" name="parent_role">
                  <option value="">--select--</option>
                  <option value="0">Final Approver</option> 
                </select> 
              </div>
            </div>  
            <div class="form-group">
              <label class="col-md-4 control-label">User Level: <span class="requied">*</span></label>
              <div class="col-md-6">
                <select class="form-control" id="user_level" name="user_level">
                  <option value="">--Select--</option>
                  @foreach(Config::get('constants.user_level') as $key=>$val)
                  <option value="{{$key}}">{{$val}}</option>
                  @endforeach
                </select> 
              </div>
            </div>  
            <div class="form-group">
              <label class="col-md-4 control-label">First Level Verifier: <span class="requied">*</span></label>
              <div class="col-md-6">
                <input class="form-check-input" type="radio" name="is_first" id="is_first_no" value="0" checked>
                <label class="form-check-label" for="inlineRadio1">No</label>
              
                <input class="form-check-input" type="radio" name="is_first" id="is_first_yes" value="1">
                <label class="form-check-label" for="inlineRadio2">Yes</label>
              </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-submit" data-dismiss="modal">
        <span class="crud-txt">Add</span>
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--Add Update Level Modal -->


<!--Add Update Scheme Modal -->
<div id="schemeformModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><span class="crud-txt">Add New</span> Scheme</h4>
      </div>
      <div class="modal-body">
        <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader" width="50px" height="50px"  ></div>
        <div class="alert alert-danger print-error-msg" style="display:none"><ul></ul></div>
        <form id="schemeform" class="form-horizontal">
            {{ csrf_field() }}                      
            <input type="hidden" name="sch_id" id="sch_id" value="">
            <div class="form-group">
              <label class="col-md-4 control-label">Scheme type: </label>
              <div class="col-md-6">
                <label class="form-control" id="sch_scheme_type_name" name="sch_scheme_type_name">
                </label> 
                <input id="sch_scheme_type" type="hidden"  class="form-control" name="sch_scheme_type" value="{{ old('sch_scheme_type') }}" required autofocus disabled>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label">Scheme Name: </label>
              <div class="col-md-6">
                <label>
                </label> 
                <input  class="form-control" type="text"  id="sch_scheme_name_name" name="sch_scheme_name_name"  value="{{ old('sch_scheme_name_name') }}" required autofocus>
                <input id="sch_scheme_name" type="hidden"  class="form-control" name="sch_scheme_name" value="{{ old('scheme_name') }}" required autofocus disabled>
                @if ($errors->has('sch_scheme_name_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('sch_scheme_name_name') }}</strong>
                    </span>
                @endif
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label">Description</label>
              <div class="col-md-6">
                <textarea id="sch_description"  class="form-control" name="sch_description" value="{{ old('sch_description') }}" required autofocus></textarea>
                @if ($errors->has('sch_description'))
                    <span class="help-block">
                        <strong>{{ $errors->first('sch_description') }}</strong>
                    </span>
                @endif
              </div>
            </div>    
            <div class="form-group">
              <label class="col-md-4 control-label">Short Code</label>
              <div class="col-md-6">
                <input id="sch_shortcode" type="text" class="form-control" name="sch_shortcode" value="{{ old('sch_shortcode') }}" required autofocus>
                @if ($errors->has('sch_shortcode'))
                    <span class="help-block">
                        <strong>{{ $errors->first('sch_shortcode') }}</strong>
                    </span>
                @endif
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label">Active Status: <span class="requied">*</span></label>
              <div class="col-md-6">
                <input class="form-check-input" type="radio" name="sch_is_active" id="sch_is_active_no" value="0" checked>
                <label class="form-check-label" for="inlineRadio1">No</label>
              
                <input class="form-check-input" type="radio" name="sch_is_active" id="sch_is_active_yes" value="1">
                <label class="form-check-label" for="inlineRadio2">Yes</label>
              </div>
            </div>  
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-scheme-submit" data-dismiss="modal">
        <span class="crud-txt">Add</span>
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--Add Update Scheme Modal -->

<!--List Item Modal -->
<div id="listItemModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><span class="list-item-txt"></span></h4>
      </div>
      <div class="modal-body">
        <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader" width="50px" height="50px"  ></div>
        <div class="alert alert-danger print-error-msg" style="display:none"><ul></ul></div>
        <div class="col-md-12">
          <table id="itemlistview" class="display" cellspacing="0" width="100%">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <thead>
              <tr role="row">
                <th width="10%">Id</th>
                <th width="50%" class="text-left">Scheme Name</th>
                <th width="20%" >Is Active</th>
                <th width="20%" class="text-left">Action</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th width="10%">Id</th>
                <th width="50%" class="text-left">Scheme Name</th>
                <th width="20%" >Is Active</th>
                <th width="20%" class="text-left">Action</th>
              </tr>
            </tfoot>     
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--Add Update Scheme Modal -->


<script src="{{ asset ('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<script > 
var table=""; 
var listItemtable = "";

$(document).ready(function(){ 
  
   $(".dataTables_scrollHeadInner").css({"width":"100%"});
 
   $(".table ").css({"width":"100%"}); 

  fill_datatable();

  function fill_datatable(scheme_id = ''){
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
      "language": {
        "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
      },
      "ajax": {
        url: "{{ url('onboardscheme') }}",
        type: "GET",
        data:{
          _token: "{{csrf_token()}}",
          scheme_id: scheme_id
        }
      } ,
      "columns": [
        { "data": "id","defaultContent":""},
        { "data": "role_name","defaultContent":""},
        { "data": "stack_level","defaultContent":"" },
        { "data": "parent_id" },
        { "data": "first_node"},
        { "data": "is_first"},
        { "data": "is_active"},
        { "data": "action"} 
      ],
    }); 
  }

  $("#sch").change(function(){
    $scheme_id = $("#sch").val();
    
    if($scheme_id==''){
      $('#addButton').hide();
      $('#edit_scheme').hide();
    }else{
      $('#addButton').show();
      $('#edit_scheme').show();
    }
    $('#example').DataTable().destroy();
    fill_datatable($scheme_id);
    loadWorkFlows($scheme_id);
    //table.clear().draw();
    //table.ajax.reload();
  });
  $("#sch_type").change(function(){
    $scheme_type = $("#sch_type").val();

    $("#add_scheme_type_div").hide();
    $("#update_scheme_type_div").hide();
    $("#add_new_scheme_type").show();
    

    if($scheme_type==''){
      $('#deactivate_scheme_type').hide();
      $('#edit_scheme_type').hide();
    }else{
      //To be uncommented for active/deactive
      //$('#deactivate_scheme_type').show();
      $('#edit_scheme_type').show();
    }

    $("#add_new_scheme").hide();
    $("#list_scheme").hide();
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "getschemefromtype",
      type:'GET',
      data: {
        scheme_type: $("#sch_type").val()
      },
      success: function(data) {
        $("#sch").empty();
        if(data!=''){
          $("#sch").append('<option value="">Select Scheme</option>');

          for(i in data){
            $("#sch").append('<option value="'+data[i].id+'">'+data[i].scheme_name+"</option>");
          }
        }
        if($("#sch_type").val()!=""){
          $("#add_new_scheme").show();
          $("#list_scheme").show();
        }else{
          $("#edit_scheme").hide();
        }
        // $('#sch').html(data);
        //console.log(data);
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  });

  $("#add_new_scheme_type").click(function(){
    $("#add_scheme_type").attr('placeholder', 'Enter New Scheme Type');         
    $("#btn_add_new_scheme_type").html('Add <i class="fa fa-add" aria-hidden="true"></i>');
    $("#action_type").val("add");
    $("#add_scheme_type_div").show();
    $("#add_new_scheme_type").hide();
    $("#edit_scheme_type").hide();   
    $("#deactivate_scheme_type").hide(); 
  });
  
  $("#btn_add_close_scheme_type").click(function(){
    $scheme_type=$("#sch_type").val();

    $("#add_scheme_type").val('');
    $("#add_scheme_type_div").hide();
    $("#add_new_scheme_type").show();
    if($scheme_type != ""){
      $("#edit_scheme_type").show();
       //To be uncommented for active/deactive
      //$("#deactivate_scheme_type").show();
    }
  });

  $("#edit_scheme_type").click(function(){
    $("#add_scheme_type").attr('placeholder', 'Enter Updated Name');         
    $("#btn_add_new_scheme_type").html('Update <i class="fa fa-edit" aria-hidden="true"></i>');
    $("#add_scheme_type").val( $('#sch_type option:selected').text() );
    $("#action_type").val("edit");
    $("#add_scheme_type_div").show();
    $("#add_new_scheme_type").hide();
    $("#edit_scheme_type").hide(); 
    $("#deactivate_scheme_type").hide();
  });

  $("#btn_add_new_scheme_type").click(function(){
    $scheme_type_id = $("#sch_type").val();
    $new_scheme_type = $("#add_scheme_type").val();
    $action_type =   $("#action_type").val();

    if(($new_scheme_type!="")){
      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        }
      });
      $.ajax({
        url: 'addNewSchemeType',
        type:'POST',
        data: {
          scheme_type: $new_scheme_type,
          scheme_type_id: $scheme_type_id,
          action_type: $action_type
        },
        success: function(data) {
          alert('New Scheme Type '+$action_type+'ed successfully');
          $("#add_scheme_type").val('');
          $("#add_scheme_type_div").hide();
          $("#add_new_scheme_type").show();
          
          $('#sch_type').empty().append('<option value="">Select Scheme Type</option>');
          for (var  i = 0; i < data.length; i++) {
            $('#sch_type').append($('<option>', {
              value: data[i].id,
              text: data[i].scheme_type
            }),'</option>');
          }
        },
        error: function (ex) {
          alert('error url:'+ex);
        }
      });
    }else{
      alert("Please enter scheme type");
    }
  });
  
  //Scheme CRUD
  $("#add_new_scheme").click(function(){
    //alert("Add New Scheme");
    addUpdateSchemeForm(0);
  });
  $("#edit_scheme").click(function(){
    //alert("Edit Existing Scheme");
    addUpdateSchemeForm(1);
  });


});

//itemType=1 for Scheme Type, itemType=2 for Scheme 
function listView(itemType)
{
//  $('#itemlistview').DataTable().clear();
  if(listItemtable!=null && listItemtable != ''){
    $('#itemlistview').DataTable().destroy();
  }
 
  listItemtable = $('#itemlistview').DataTable( {
    dom: "Blfrtip",
    "paging": true,
    "pageLength":10,
    "lengthMenu": [[10,20, 50, 80, 120, 150, 180, 500,1000, 2000], [10,20, 50, 80, 120, 150, 180, 500,1000, 2000]],
    "serverSide": true,
    "deferRender": true,
    "processing":true,
    "bRetrieve": true,
    "ordering":false,
    "language": {
      "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
    },
    "ajax": {
      url: "{{ url('getAllItemList') }}",
      type: "POST",
      data:{
        _token: "{{csrf_token()}}",
        item_type: itemType
      }
    } ,
    "columns": [
      { "data": "id","defaultContent":""},
      { "data": "item_name","defaultContent":""},
      { "data": "is_active"},
      { "data": "action"} 
    ],
  }); 

  $('#listItemModal').modal();
  $(".submit_loader").hide();

  //getAllItemList
}

function loadWorkFlows(id)
  {
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "workflowListView",
      type:'POST',
      data: {
        scheme_id:id
      },
      success: function(data) {
        $('#workflowdiv').html(data);
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  }
  function toggleActivate(id, type)
  {
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "schemeOnboardToggleActivate",
      type:'POST',
      data: {
        level_id:id,
        action_type: type
      },
      success: function(data) {
        table.clear().draw();
        table.ajax.reload();
        loadWorkFlows($("#sch").val());
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  }

  function toggleStatus(type, id)
  {
    $scheme_type_id =  $("#sch_type").val();

    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "toggleItemStatus",
      type:'POST',
      data: {
        item_id:id,
        item_type: type
      },
      success: function(data) {
        listItemtable.clear().draw();
        listItemtable.ajax.reload();
        if(type == 1){
          location.reload();
        }else{
          $("#sch_type").val($scheme_type_id).trigger('change');
        }
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  }

  function deleteItem(type, id)
  {
    $scheme_type_id =  $("#sch_type").val();
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "deleteItem",
      type:'POST',
      data: {
        item_id:id,
        item_type: type
      },
      success: function(data){
        listItemtable.clear().draw();
        listItemtable.ajax.reload();
        alert("Item deleted successfully");
        if(type == 1){
          location.reload();
        }else{
          $("#sch_type").val($scheme_type_id).trigger('change');
        }

      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });  
  }
  
  function addUpdateSchemeForm(id){
    $(".print-error-msg").hide();
    $scheme_type_id = $("#sch_type option:selected").val();
    $scheme_type = $("#sch_type option:selected").text();
    $scheme_id = $("#sch option:selected").val();
    $scheme = $("#sch option:selected").text();

    $(".submit_loader").hide();
    $(".submit_loader").show();
    $crud_txt = '';
    
    $("#sch_scheme_type_name").text($scheme_type);
    $("#sch_scheme_type").val($scheme_type_id);
    $('#sch_description').val('');
    $('#sch_shortcode').val('');
    $("#sch_scheme_name_name").val('');
    $("#sch_scheme_name").val('');
    

    //Update Scheme
    if(id){
      $crud_txt = "Update";
      $("#sch_scheme_name_name").val($scheme);
      $("#sch_scheme_name").val($scheme_id);

      $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
      });
      $.ajax({
        type: 'POST',  
        url: 'getSchemeDetail',
        data:{
          scheme_id: $scheme_id
        },
        dataType: 'json',
        success: function (data) {
          if (!data) {
            return;
          }else{
          // Load User Role Dropdown  
            $('#sch_description').val(data['description']);
            $('#sch_shortcode').val(data['short_code']);
            if(data['is_active']){
              $('#sch_is_active_yes').prop('checked', true);
              $('#sch_is_active_no').prop('checked', false);
            }else{
              $('#sch_is_active_yes').prop('checked', false);
              $('#sch_is_active_no').prop('checked', true);
            }

          }
          $(".submit_loader").hide();
        },
        error: function (ex) {
          alert("problem loading value");
          $(".submit_loader").hide();
        }
      });
      $("#sch_id").val(1);
    } //Add Scheme
    else{
      $crud_txt = "Add";
      $('#sch_is_active_yes').prop('checked', true);
      $('#sch_is_active_no').prop('checked', false);
      $("#sch_id").val(0);

      $(".submit_loader").hide();
      
    }
    //$(".submit_loader").hide();
    $(".crud-txt").text($crud_txt);
    $("#schemeformModal").modal();

  }

  function addUpdateLevelForm(id){
    $(".print-error-msg").hide();
    $('#scheme_type').text($("#sch_type option:selected").text());
    $('#scheme_name').text($("#sch option:selected").text());
    $scheme_id = $("#sch option:selected").val();

    $(".submit_loader").hide();
    $(".submit_loader").show();
    
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      type: 'POST',  
      url: 'getAddUpdateLevelInfo',
      data:{
        id: id,
        scheme_id: $scheme_id
      },
      dataType: 'json',
      success: function (data) {
        if (!data) {
          return;
        }else{
        // Load User Role Dropdown  
          $user_role = data[0];
          $('#user_role').empty().append('<option value="">--select--</option>');
          for (var  i = 0; i < $user_role.length; i++) {
            $('#user_role').append($('<option>', {
              value: $user_role[i].name,
              text: $user_role[i].name
            }),'</option>');
          }

        // Load Parent Level Dropdown  
          $parent_level = data[1];
          if($parent_level.length > 0){
            $('#parent_role').empty().append('<option value="">--select--</option>');
            for (var  i = 0; i < $parent_level.length; i++) {
              $('#parent_role').append($('<option>', {
                value: $parent_level[i].id,
                text: $parent_level[i].name
              }),'</option>');
            }
          }
          $("#user_level option:selected").prop("selected", false)
        // For Update Preselect value  
          if(id){
            $maplevel = data[2][0];
            if(($maplevel.parent_id!=null) && ($maplevel.parent_id!=0)){ 
              $('#parent_role').val($maplevel.parent_id).trigger('change');
            }
            $('#user_role').val($maplevel.role_name).trigger('change');
            $('#user_level').val($maplevel.stack_level).trigger('change');
           
            if($maplevel.is_first){
              $('#is_first_yes').prop('checked', true);
              $('#is_first_no').prop('checked', false);
            }else{
              $('#is_first_yes').prop('checked', false);
              $('#is_first_no').prop('checked', true);
            }
          }
        // Select First Level Verifier
          

        }
        $(".submit_loader").hide();
      },
      error: function (ex) {
        alert("problem loading value");
      }
    });

    if(id){
      var crud_txt='Update Map';
    }
    else{
      var crud_txt='Add Map';
    }
    $("#id").val(id);
    $(".crud-txt").text(crud_txt);
    $("#levelformModal").modal();

  }

  $("#btn-scheme-submit").click(function(){
    $scheme_type_id = $('#sch_scheme_type').val();
    $scheme_type = $('#sch_scheme_type_name').text();
    $scheme = $('#sch_scheme_name_name').val();
    $scheme_id = $('#sch_scheme_name').val();
    $description = $('#sch_description').val();
    $shortcode = $('#sch_shortcode').val();
    $id = $('#sch_id').val();
    $is_active = $("input[name='sch_is_active']:checked").val();

    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "addUpdateScheme",
      type:'POST',
      dataType: "json",
      data: { 
        scheme_type:$scheme_type_id,
        scheme_id: $scheme_id,
        scheme_name: $scheme,
        description: $description,
        shortcode: $shortcode,
        is_active: $is_active,
        id: $id
      },
      success: function(data) {
        if(data.return_status){
          alert(data.return_msg);
          $("#sch_type").val($scheme_type_id).trigger('change');
          //$("#sch").val($scheme_id).trigger('change');
          table.ajax.reload();
          loadWorkFlows($scheme_id);
        }else{
          printErrorMsg(data.return_msg);
        }
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  });

  $("#btn-submit").click(function(){
    $scheme_id = $("#sch option:selected").val();
    $user_role = $('#user_role option:selected').val();
    $user_level = $('#user_level option:selected').val();
    $parent_role  = $('#parent_role option:selected').val();
    $is_first = $("input[name='is_first']:checked").val();
    $id = $('#id').val();
    
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "addUpdateMap",
      type:'POST',
      dataType: "json",
      data: { 
        id:$id,
        scheme_id:$scheme_id,
        user_role:$user_role,
        usr_level:$user_level,
        parent_id:$parent_role,
        is_first:$is_first
      },
      success: function(data) {
        if(data.return_status){
          if(id){
            alert('Map Level updated successfully');
          }else{
            alert('New Map Level added successfully');
          }
          table.ajax.reload();
          loadWorkFlows($("#sch").val());
        }else{
          printErrorMsg(data.return_msg);
        }
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  });
</script>
@endsection
