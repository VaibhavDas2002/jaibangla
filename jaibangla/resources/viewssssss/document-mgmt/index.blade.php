@extends('document-mgmt.base')
@section('action-content')
<style>
.select2{
  width:300px;
}
</style>
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of Documents</h3>
        </div>
        <div class="col-sm-4">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#documentModel">
            Add New Document
          </button>
        </div>

        <br />
        <div class="clearfix"></div>
        <div class="col-sm-12">@if(session()->has('message'))
          <div class="alert alert-success">
            {{ session()->get('message') }}
          </div>
          @endif
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>

      <div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
        <div class="col-md-12 text-right" id="addButton" hidden>
          <a class="btn btn-primary" href="javascript:void(0)" onClick="addUpdateLevelForm(0)">Add Level</a>
        </div>
        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px" />
        </div>

        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
          <table id="example" class="display" cellspacing="0" width="100%">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <thead>
              <tr role="row">

                <th width="6%">Id</th>
                <th width="25%" class="text-left">Document Name</th>
                <th width="25%" class="text-left">Document Type</th>
                <th width="7%">Document Size</th>
                <th width="6%">Group</th>
                <th width="6%">Active Status</th>
                <th width="6%">Is Profile Pic</th>
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
                <th width="6%">Is Active</th>
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
      <!-- /.box-body -->
    </div>
</section>
<!-- /.content -->
</div>

<!--Add  Document Modal -->
<div id="documentModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form class="form-horizontal" id="document_create" role="form" method="POST" action="#">
        <input type="hidden" id="edit_code">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><span class="crud-txt" id="document_heading">Add New Document</span></h4>
        </div>
        <div class="modal-body">
          <div class="" style="display: none"><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader"
              width="50px" height="50px"></div>
          <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
          </div>




          <div class="form-group{{ $errors->has('doc_name') ? ' has-error' : '' }}">
            <label for="doc_name" class="col-md-4 control-label required">Document Name</label>

            <div class="col-md-6">
              <input id="doc_name" type="text" class="form-control" name="doc_name" value="{{ old('doc_name') }}"
                required autofocus>

              @if ($errors->has('doc_name'))
              <span class="help-block">
                <strong>{{ $errors->first('doc_name') }}</strong>
              </span>
              @endif
            </div>
          </div>



          <div class="form-group{{ $errors->has('doc_type') ? ' has-error' : '' }}">
            <label for="doc_type" class="col-md-4 control-label required">Document Type</label>
            <div class="col-md-6">
              <input id="doc_type" type="text" class="form-control" name="doc_type" value="{{ old('doc_type') }}"
                required autofocus>
              @if ($errors->has('doc_type'))
              <span class="help-block">
                <strong>{{ $errors->first('doc_type') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="form-group{{ $errors->has('doc_size_kb') ? ' has-error' : '' }}">
            <label for="doc_size_kb" class="col-md-4 control-label required">Max Size <b>(in KB)</b></label>
            <div class="col-md-6">
              <input id="doc_size_kb" type="text" class="form-control" name="doc_size_kb"
                value="{{ old('doc_size_kb') }}" required autofocus>
              @if ($errors->has('doc_size_kb'))
              <span class="help-block">
                <strong>{{ $errors->first('doc_size_kb') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="form-group{{ $errors->has('doucument_group') ? ' has-error' : '' }}">
            <label for="doucument_group" class="col-md-4 control-label">Document Group</label>

            <div class="col-md-6">
              <select name="doucument_group[]" id="doucument_group" class="select2" tabindex="4" multiple>
                <option value="">--NA --</option>
                @foreach(Config::get('constants.document_group') as $key=>$val)
                <option value="{{$key}}" @if( old('doucument_group')==$key) selected @endif>{{$val}}</option>
                @endforeach
              </select>

              @if ($errors->has('doucument_group'))
              <span class="help-block">
                <strong>{{ $errors->first('doucument_group') }}</strong>
              </span>
              @endif
            </div>
          </div>


          {{-- <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
              <button type="submit" class="btn btn-primary">
                Add
              </button>
            </div>
          </div> --}}

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btn-scheme-submit">
            Add
          </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
    </form>
  </div>
</div>
<!--Add Update Document Modal -->
@endsection

@section('script')
<script>
  $(function(){
    $('#documentModel').on('hide.bs.modal', function (e) {
            $("#document_create")[0].reset();
            $('#document_create').bootstrapValidator('resetForm', true);
            $('#edit_code').val('');
            $('#doucument_group').val('').change();
            $('#document_heading').text('Add New Document');
            $('#btn-scheme-submit').text('Add');
        });
    
    $('#document_create')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                      doc_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter doc name'
                                }
                            }
                        },
                        doc_type: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter document type'
                                }

                            }
                        },
                        doc_size_kb: {
                            validators: {
                              notEmpty: {
                                    message: 'Please enter document size'
                                },
                                regexp: {
                                    regexp: /^[0-9.-]+$/,
                                    message: 'Document size contain only integer value'
                                }
                               
                            }
                        },

                    }
                }).on('success.form.bv', function (e) {
            // Prevent form submission
            e.preventDefault();
           document_save();

        });
    fill_datatable();
  });


function fill_datatable(){

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
      url: "{{ route('getDocumentList') }}",
      type: "GET",
      data:{
        _token: "{{csrf_token()}}",
      
      }
    } ,
    "columns": [
      { "data": "id","defaultContent":""},
      { "data": "doc_name","doc_name":""},
      { "data": "doc_type","defaultContent":"" },
      { "data": "doc_size_kb" },
      { "data": "doucument_group"},
      { "data": "is_active"},
      { "data": "is_profile_pic"},
      { "data": "action"} 
    ],
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
      url: "{{route('documentToggleActivate')}}",
      type:'POST',
      data: {
        document_id:id,
        action_type: type
      },
      success: function(data) {
        table.clear().draw();
        table.ajax.reload();
    
      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });
  }
  function deleteDocument( id)
  {
    $.confirm({
                type: 'red',
                icon: 'fa fa-warning',
                title: 'Warning!!',
                content: 'Are you sure to delete this record?',
                buttons: {
        confirm: function () {
          $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
      }
    });
    $.ajax({
      url: "{{route('deleteDocument')}}",
      type:'POST',
      data: {
        item_id:id,
      
      },
      success: function(data){
        table.clear().draw();
        table.ajax.reload();
        $.alert({
                type: 'green',
                icon: 'fa fa-check',
                title: 'Success!!',
                content: 'Document deleted successfully',
            });
    
       

      },
      error: function (ex) {
        alert('error url:'+ex);
      }
    });  
        },
        cancel: function () {
          
        },
                }
            });
            
        
   
  }

  function document_save(){
    var doc_name = $('#doc_name').val();

var token = $("input[name='_token']").val();
var doc_type = $('#doc_type').val();
var doc_size_kb  = $('#doc_size_kb ').val();
var edit_code = $("#edit_code").val();
var doucument_group  = $('#doucument_group').val();
console.log(doucument_group);
var fd = new FormData();

        if (edit_code == '') {
        var action_url = "{{route('documentSave')}}";


        } else {

        var action_url = '{{ route("documentUpdate") }}';

        fd.append('edit_code', edit_code);
        }
          
fd.append('doc_name', doc_name);
fd.append('doc_type', doc_type);
fd.append('doc_size_kb', doc_size_kb);
fd.append('doucument_group', doucument_group);
fd.append('_token', token);

$.ajax({
    type: 'post',
    url: action_url,
    data: fd,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function (response) {
   
      $.alert({
                type: 'green',
                icon: 'fa fa-check',
                title: 'Success!!',
                content: response.msg
            });
            table.clear().draw();
        table.ajax.reload();
      $('#documentModel').modal("hide");

    },
    error: function (jqXHR, textStatus, errorThrown) {
                
              ajax_error(jqXHR, textStatus, errorThrown); 
            }
});
  }

  function UpdateDocument (id){
    var editId=id;
   $('#doucument_group').val('').change();
        $.ajax({
                  type: 'post',
                  url: "{{route('editDocument')}}",
                  data: {editId:editId,'_token': $('input[name="_token"]').val()},
                  dataType: 'json',
                  success: function (data) {
                    //console.log(data)
                      $("#documentModel").modal('show');
                      $('#document_heading').text('Update Document');
                      $('#btn-scheme-submit').text('Update');
                      
                      $("#document_create")[0].reset();
                      $("#doc_name").val(data.docs.doc_name);
                      $("#doc_type").val(data.docs.doc_type);
                      $("#doc_size_kb").val(data.docs.doc_size_kb);
                      $("#edit_code").val(data.docs.id);
                      if(data.docs.doucument_group!=''){
                        var RemoveFirstCharSub = data.docs.doucument_group.slice(1,-1);
                        var doucument_group = RemoveFirstCharSub.split(",");
                      }
                     else{
                        var doucument_group = array();
                     }
                     //console.log(doucument_group);
                     $('#doucument_group').val(doucument_group).change();
                     //$("#doucument_group").select2("val", [1,2]);
                     // $("#doucument_group").val(doucument_group);
                      
                  }
              });
  }

 
</script>

@stop