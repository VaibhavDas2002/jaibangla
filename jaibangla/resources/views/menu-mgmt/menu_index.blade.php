@extends('menu-mgmt.base')
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
              <h4 class="panel-title">
                <!-- <a data-toggle="collapse" href="#menuItems">Menu Items</a> -->
                <button class="btn btn-link" data-toggle="collapse" data-target="#menuItems" aria-expanded="true" aria-controls="collapseOne">
                  Menu Items (Add / Edit/ Delete)
                </button>
              </h4>
            </div>
            <div id="menuItems" class="panel-collapse collapse">
              <div class="panel-body">
                <div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
                <div class="col-md-12 text-right">
                  <a class="btn btn-primary" href="javascript:void(0)" onClick="CreatemenuForm(0)">Add new Menu</a>
                </div>
                  <div class="col-md-12 text-center" id="loaderdiv" hidden>
                    <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px"/>
                  </div>  
              
                  <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
                    <table id="example" class="display" cellspacing="0" width="100%">
                      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                      <thead>
                          <tr role="row">
                            <th width="6%" class="text-left">Menu Id</th>
                            <th width="26%" class="text-left">Menu Name</th>
                            <th width="30%" class="text-left">Menu URL</th>     
                            <th width="8%" class="text-left">URL TYPE</th>
                            <th width="6%" class="text-left">Parent Id</th>
                            <th width="9%" >Active</th>
                            <th width="15%" class="text-left">Action</th>
                          </tr>
                      </thead>
                      <tfoot>
                        <tr>
                            <th width="6%" class="text-left">Menu Id</th>
                            <th width="26%" class="text-left">Menu Name</th>
                            <th width="30%" class="text-left">Menu URL</th>     
                            <th width="8%" class="text-left">URL TYPE</th>
                            <th width="6%" class="text-left">Parent Id</th>
                            <th width="9%" class="text-left">Active</th>
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
                  User Role > Menu Mapping
                </button>
              </h4>
            </div>
            <div id="menuRoleMapping" class="panel-collapse collapse show">
              <div class="panel-body">
                  <div class="row">
                      <div class="col-md-6 col-md-offset-3"> 
                      <h4>
                          <span>Select User Role:</span>
                          <select id="user_roles">
                            <option value="">Please select User Role</option>
                            @foreach($roles as $role)
                              <option value="{{$role['name']}}">{{$role['name']}}</option>
                            @endforeach  
                          </select>
                      </h4> 
                      </div> 
                  </div>
                  <hr/>
                  <div class="row" id="menu_role_mapping_panel" hidden>
                      <span style="color:red">
                      <div class="col-md-12 text-center">
                        <cite>
                          *) While Adding a <b>Menu Item</b> in <b>User Role</b>, respective <b>parent menu</a> also needs to be <b>added</b> to reflect in Menu items
                        </cite>
                      </div>
                      <div class="col-md-12 text-center">
                        <cite>(E.g. <button type="button" class="btn btn-warning">P</button> - Parent Menu, 
                        <button type="button" class="btn btn-primary">C - <span class="badge">12</span></button> - Child Menu with Parent Menu id=12)</cite>
                      </div>
                      </span>
                      <div class="col-md-12"><hr/></div>
                      <!--Left Side Panel-->
                      <div class="dual-list list-left col-md-4">
                          <div class="col-md-12 text-left"><h4><cite><u>Selected Menu List</u></cite></h4></div>
                          <div class="well text-right">
                              <div class="row">
                                  <div class="col-md-2">
                                      <div class="btn-group">
                                          <a class="btn btn-default selector" title="select all"><i class="glyphicon glyphicon-unchecked"></i></a>
                                      </div>
                                  </div>
                                  <div class="col-md-10">
                                      <div class="input-group">
                                          <span class="input-group-addon glyphicon glyphicon-search"></span>
                                          <input type="text" name="SearchDualList" class="form-control" placeholder="search" />
                                      </div>
                                  </div>
                              </div>
                              <ul class="list-group" id="selected_menu">
                                  <!-- List to be loaded using AJAX-->
                              </ul>
                          </div>
                      </div>
              
                      <div class="list-arrows col-md-1 text-center">
                          <button class="btn btn-default btn-sm move-left">
                              <span class="glyphicon glyphicon-chevron-left"></span>
                          </button>
              
                          <button class="btn btn-default btn-sm move-right">
                              <span class="glyphicon glyphicon-chevron-right"></span>
                          </button>
                      </div>
              
                      <div class="dual-list list-right col-md-4">
                          <div class="col-md-12 text-left"><h4><cite><u>Remaining Menu List</u></cite></h4></div>
                          <div class="well">
                              <div class="row">
                                  <div class="col-md-2">
                                      <div class="btn-group">
                                          <a class="btn btn-default selector" title="select all"><i class="glyphicon glyphicon-unchecked"></i></a>
                                      </div>
                                  </div>
                                  <div class="col-md-10">
                                      <div class="input-group">
                                          <input type="text" name="SearchDualList" class="form-control" placeholder="search" />
                                          <span class="input-group-addon glyphicon glyphicon-search"></span>
                                      </div>
                                  </div>
                              </div>
                              <ul class="list-group" id="not_selected_menu">
                                  <!-- List to be loaded using AJAX-->
                              </ul>
                          </div>
                      </div>
                      <!--Right Side Preview Panel-->
                      <div class="col-md-3" style="color:darkgreen;">
                          <h4><u><cite>Menu Preview Panel </cite></u><button class="btn btn-danger" style="float:right;" id="rearrange"><i class="fa fa-edit"></i></button></h4> 
                          <!-- <i class="fa fa-sort" style="float:right; color:red">Ranking</i> -->
                          
                      <ul id="tree1">
                             <!--Menu Tree View Loaded through AJAX Call-->    
                      </ul> 
                      </div>
                  </div>
              </div>
              <!-- <div class="panel-footer">Panel Footer</div> -->
            </div>
          </div>
        </div>        
      </div>
    </div>
  </section>
	
<!--Add Update Modal -->
<div id="menuformModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><span class="crud-txt">Add</span> Menu</h4>
      </div>
      <div class="modal-body">
        <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader" width="50px" height="50px"  style="display:none;"></div>
        <div class="alert alert-danger print-error-msg" style="display:none"><ul></ul></div>
        <form id="menuform" class="form-horizontal">
            {{ csrf_field() }}                      
            <input type="hidden" name="id" id="id" value="">

            <div class="form-group">
                <label for="menu_name" class="col-md-4 control-label">Menu Name <span class="requied">*</span></label>

                <div class="col-md-6">
                    <input id="menu_name" type="text" class="form-control" name="menu_name" value=""   maxlength="50">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Parent Menu <span class="requied">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" name="parent_id" id="parent_id">
                    <option value="">--Please Select--</option> 
                    
                    </select>
                </div>
            </div>  
            <div class="form-group">
                <label for="menu_icon" class="col-md-4 control-label">ICON <span class="requied">*</span></label>

                <div class="col-md-6">
                    <input id="menu_icon" type="text" class="form-control" name="menu_icon" value=""  maxlength="50">

                </div>
            </div>
            <div class="form-group">
                <label for="link_url" class="col-md-4 control-label">Link URL <span class="requied">*</span></label>

                <div class="col-md-6">
                    <input id="link_url" type="text" class="form-control" name="link_url" value="" maxlength="100">

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">URL Type <span class="requied">*</span></label>
                <div class="col-md-6">
                    <select class="form-control" id="url_type"  name="url_type" required>
                    <option value="1"  >URL</option> 
                    <option value="2" >ROUTE</option> 
                    </select>
                    
                </div>
            </div>  
            <div class="form-group">
                <label for="menu_class" class="col-md-4 control-label">Menu Class</label>

                <div class="col-md-6">
                    <input id="menu_class" type="text" class="form-control" name="menu_class" value="" maxlength="50">

                </div>
            </div>
              <div class="form-group">
                <label for="menu_slug" class="col-md-4 control-label">Menu Slug(Used for Highlight)</label>

                <div class="col-md-6">
                    <input id="menu_slug" type="text" class="form-control" name="menu_slug" value="" maxlength="50">

                </div>
            </div>
            <div class="form-group">
                <label for="rank" class="col-md-4 control-label">Rank</label>

                <div class="col-md-6">
                    <input id="rank" type="number" class="form-control" name="rank" value="" >

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Designation <span class="requied">*</span></label>
                <div class="col-md-6">
                    <select class="form-control select2" id="designation_id_old" name="designation_id_old[]"  multiple>
                    <option value="">--Please Select--</option> 
                    
                    </select>
                  
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
<!--End Add Update Modal -->

<!--List Item Modal -->
<div id="listItemModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
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
                <th width="30%" class="text-left">Menu Name</th>
                <th width="30%" class="text-left">Menu URL</th>
                <th width="12%" class="text-left">Parent Id</th>
                <th width="8%">Rank</th>
                <th width="10%">Is Active</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th width="10%">Id</th>
                <th width="30%" class="text-left">Menu Name</th>
                <th width="30%" class="text-left">Menu URL</th>
                <th width="12%" class="text-left">Parent Id</th>
                <th width="8%" >Rank</th>
                <th width="10%" >Is Active</th>
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
<!--List Item Modal -->

<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset ("js/treeview.js") }}" type="text/javascript"></script>
<script src="{{ asset ("js/duellist.js") }}" type="text/javascript"></script>
<link href="{{ asset ("css/treeview.css") }}" rel="stylesheet">
<link href="{{ asset ("css/duellist.css") }}" rel="stylesheet">

<script > 
var table=""; 
var listItemtable="";
$(document).ready(function(){ 

  $('#user_roles').on('change',function(){
    $role = $('#user_roles').val();
    //alert($role);
    if($role != ""){
      $('#menu_role_mapping_panel').hide();
      $(".submit_loader").hide();
      $(".submit_loader").show();
      $.ajax({
        type: 'GET',
        url: 'getMenuUsingRole/'+$role,
        
        success: function (data) {
          //alert(data[0]);
          $('#selected_menu').html(data[0]);
          $('#not_selected_menu').html(data[1]);
          $('#tree1').html(data[2]);
          $('#menu_role_mapping_panel').show();
          $(".submit_loader").hide();
        },
        error: function (ex) {
          $(".submit_loader").hide();
          alert('error url:'+ex);
        }
      });
    }
  });
	 
   $(".dataTables_scrollHeadInner").css({"width":"100%"});
 
   $(".table ").css({"width":"100%"}); 
   table=$('#example').DataTable( {
    dom: "Blfrtip",
    "paging": true,
    "pageLength":10,
    "lengthMenu": [[10,20, 50, 80, 120, 150, 180, 500,1000, 2000], [20, 50, 80, 120, 150, 180, 500,1000, 2000]],
		"serverSide": true,
		"deferRender": true,
    "processing":true,
    "bRetrieve": true,
    "ordering":false,
    "language": {
      "processing": '<img src="{{ asset('images/ZKZg.gif') }}" width="150px" height="150px"/>'
    },
    "ajax": {
			url: "{{ url('getMenuList') }}",
			type: "POST",
      data:{
				_token: "{{csrf_token()}}"
			}
		} ,
    "columns": [
      { "data": "id","defaultContent":""},
      { "data": "menu_name","defaultContent":""},
      { "data": "link_url","defaultContent":"" },
      { "data": "url_type","defaultContent":"" },
      { "data": "parent_id" },
      { "data": "is_active"
        // "render": function (data, type, row) {
        //                   return (data == true) ? '<span class="glyphicon glyphicon-ok"></span>' 
        //                   : '<span class="glyphicon glyphicon-remove"></span>';}
      },
      { "data": "action"} 
    ],
   }); 


   $("#rearrange").click(function(){
    $role = $('#user_roles').val();

    if(listItemtable!=null && listItemtable != ''){
      $('#itemlistview').DataTable().destroy();
    }
    listItemtable = $('#itemlistview').DataTable( {
      dom: "Blfrtip",
      "paging": true,
      "pageLength":120,
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
        url: "{{ url('menu-management/getMenuItemFromRole') }}",
        type: "POST",
        data:{
          _token: "{{csrf_token()}}",
          role: $role
        }
      } ,
      "columns": [
        { "data": "menu_id","defaultContent":""},
        { "data": "menu_name","defaultContent":""},
        { "data": "link_url","defaultContent":""},
        { "data": "parent_id","defaultContent":""},
        { "data": "rank"}, 
        { "data": "is_active"},
      ],
    }); 
    $("#listItemModal").modal();
   });

});

function changeMapItemRank(menuid, rank)
{
  $role = $('#user_roles').val();
//    alert("New Rank: "+rank+', Menu ID: '+menuid );  
  $(".submit_loader").hide();
  $(".submit_loader").show();

  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': "{{csrf_token()}}"
    }
  });
  $.ajax({
    url: "updateRoleBasedMenuRank",
    type:'POST',
    data: {
      rank:rank,
      menu_id:menuid,
      role: $role
    },
    success: function(data){
      $('#user_roles').val($role).trigger('change');
      $(".submit_loader").hide();
    },
    error: function (ex) {
      alert('error url:'+ex);
    }
  });
}

function CreatemenuForm(id){
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").hide();
    if(id){
      var id=id;
      var crud_txt='Update';
      var add_edit_status=1;
      $(".submit_loader").hide();
      $(".submit_loader").show();
      $.ajax({
        type: 'GET',
        url: 'getdeMenuDetails/'+id,
        
        success: function (data) {
          $("#menu_name").val(data[0]['menu_name']);
          $("#menu_icon").val(data[0]['icon']);
          $("#link_url").val(data[0]['link_url']);
          $("#url_type").val(data[0]['url_type']);
          $("#menu_class").val(data[0]['menu_class']);
          $("#menu_slug").val(data[0]['menu_slug']);
          $("#rank").val(data[0]['rank']);
          
          var designationList = data[2];
          $('#designation_id_old').html(designationList);

          var parent_menu = data[3];
          $('#parent_id').html(parent_menu);

          var designation=data[1];
          var Values = new Array();
          for (var  i = 0; i < designation.length; i++) {
          Values.push(designation[i].designation_id_old);
          }
          $('.select2').val(Values).trigger('change');
          $("#parent_id").val(data[0]['parent_id']).change();

          $(".submit_loader").hide();
          //$(".select2").select2("val", ['Admin','Operator']);

        },
        error: function (ex) {
          $(".submit_loader").hide();
          alert('error url:'+ex);
        }
      });
    }
    else{
      var crud_txt='Add';
      $("#menu_name").val('');
      $("#parent_id").val('');
      $("#menu_icon").val('');
      $("#link_url").val('');
      $("#url_type").val(1);
      $("#menu_class").val('');
      $("#menu_slug").val('');
      $("#rank").val('');
      var add_edit_status=0;
      var id='';

      $(".submit_loader").hide();
      $(".submit_loader").show();
      $.ajax({
        type: 'GET',
        url: 'loadMenuItemFormMaster',  
        
        success: function (data) {         
          var designationList = data[0];
          $('#designation_id_old').html(designationList);

          var parent_menu = data[1];
          $('#parent_id').html(parent_menu);
          $(".submit_loader").hide();

        },
        error: function (ex) {
          $(".submit_loader").hide();
          alert('error url:'+ex);
        }
      });
      
    }
    $("#id").val(id);
    $("#add_edit_status").val(add_edit_status);
    $(".crud-txt").text(crud_txt);
    $('.select2').val('Admin').trigger('change');
    $("#menuformModal").modal();

  }
  $("#btn-submit").click(function(){
    var _token = $("input[name='_token']").val();
    var menu_name = $("input[name='menu_name']").val();
    var parent_id=$("#parent_id").val();
    var menu_icon = $("input[name='menu_icon']").val();
    var link_url = $("input[name='link_url']").val();
    var url_type=$("#url_type").val();
    var menu_class = $("input[name='menu_class']").val();
    var menu_slug = $("input[name='menu_slug']").val();
    var rank = $("input[name='rank']").val();
    var designation_id_old=$(".select2").val();
    var id=$("#id").val();
    // alert(id);
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': _token
      }
    });
    $.ajax({
      url: "store",
      type:'POST',
      dataType: "json",
      data: {id:id,menu_name:menu_name,parent_id:parent_id,menu_icon:menu_icon,link_url:link_url,
        url_type:url_type,menu_class:menu_class,menu_slug:menu_slug,rank:rank,designation_id_old:designation_id_old},
      success: function(data) {
        if(data.return_status){
          if(id){
            alert('Menu Item updated successfully');
          }else{
            alert('New Menu Item added successfully');
          }
          table.ajax.reload();
        }else{
          printErrorMsg(data.return_msg);
        }
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  });
  
  function printErrorMsg (msg) {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','block');
    $.each( msg, function( key, value ) {
        $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
    });
  }

  function addRemoveMenuinRole(type, id){
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "addRemoveMenuItemUserRole",
      type:'POST',
      data: {
        menu_id:id,
        user_role:$('#user_roles').val(),
        request_type:type
      },
      success: function(data) {
        $('#tree1').html(data);
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  } 
  function toggleActivate(type, id)
  {
    $role = $('#user_roles').val();
    if(type == 2){
      $(".submit_loader").hide();
      $(".submit_loader").show();

    }
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "menuItemToggleActivate",
      type:'POST',
      data: {
        type:type,
        menu_id:id,
        role: $role 
      },
      success: function(data){
        if(type == 1){
          table.clear().draw();
          table.ajax.reload();
        }else if(type == 2){
          listItemtable.clear().draw();
          listItemtable.ajax.reload();
          $('#user_roles').val($role).trigger('change');
          $(".submit_loader").hide();
        }
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  }
</script>
@endsection
