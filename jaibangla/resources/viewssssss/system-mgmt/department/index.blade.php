@extends('system-mgmt.department.base')
@section('action-content')
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of departments</h3>
        </div>
        <div class="col-sm-4">

          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#departmentModel">
            Add New Department
          </button>
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

        <div class="col-md-12 text-center" id="loaderdiv" hidden>
          <img src="{{ asset('images/ZKZg.gif') }}" width="100px" height="100px" />
        </div>

        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
          <table id="example" class="display" cellspacing="0" width="100%">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <thead>
              <tr role="row">

                <th>Serial No</th>
                <th>Department Name</th>

                <th>Action</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>Serial No</th>
                <th>Department Name</th>

                <th>Action</th>
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
    {{-- <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('department.search') }}">
    {{ csrf_field() }}
    @component('layouts.search', ['title' => 'Search'])
    @component('layouts.two-cols-search-row', ['items' => ['Name'],
    'oldVals' => [isset($searchingVals) ? $searchingVals['name'] : '']])
    @endcomponent
    @endcomponent
    </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
            aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                  aria-label="Department: activate to sort column ascending">Department Name</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2"
                  aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($departments as $department)
              <tr role="row" class="odd">
                <td>{{ $department->name }}</td>
                <td>
                  <form class="row" method="POST" action="{{ route('department.destroy', ['id' => $department->id]) }}"
                    onsubmit="return confirm('Are you sure?')">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('department.edit', ['id' => $department->id]) }}"
                      class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                      Update
                    </a>
                    <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th width="20%" rowspan="1" colspan="1">Department Name</th>
                <th rowspan="1" colspan="2">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to
            {{count($departments)}} of {{count($departments)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $departments->links() }}
          </div>
        </div>
      </div>
    </div>
  </div> --}}
  <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->
</div>
<!--Add  Department Modal -->
<div id="departmentModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form class="form-horizontal" id="department_create" role="form" method="POST" action="#">
        <input type="hidden" id="edit_code">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><span class="crud-txt" id="department_heading">Add New Department</span></h4>
        </div>
        <div class="modal-body">
          <div class="" style="display: none"><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader"
              width="50px" height="50px"></div>
          <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
          </div>
          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-4 control-label required">Department Name</label>

            <div class="col-md-6">
              <input id="department_name" type="text" class="form-control" name="name" value="{{ old('name') }}"
                required autofocus>

              @if ($errors->has('name'))
              <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
              </span>
              @endif
            </div>
          </div>




        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btn-scheme-submit">
            Create
          </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
    </form>
  </div>
</div>
<!--Add Update Department Modal -->
@endsection

@section('script')
<script>
  $(function(){
    department_list();
    $('#departmentModel').on('hide.bs.modal', function (e) {
            $("#department_create")[0].reset();
            $('#department_create').bootstrapValidator('resetForm', true);
            $('#edit_code').val('');
        
            $('#department_heading').text('Add New Department');
                      $('#btn-scheme-submit').text('Add');
        });
    
    $('#department_create')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                      name: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter department name'
                                },
                                regexp: {
                                    regexp: /^[a-z\s]+$/i,
                                    message: 'Department Name can consist of alphabetical characters and spaces only'
                                }

                            }
                        },
                                
                    }
                }).on('success.form.bv', function (e) {
            // Prevent form submission
            e.preventDefault();
           division_save();

        });
   
  });


function department_list(){

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
      url: "{{ route('getDepartmentList') }}",
      type: "GET",
      data:{
        _token: "{{csrf_token()}}",
      
      }
    } ,
    "columns": [
      { "data": "serial_no","defaultContent":""},
      { "data": "name",},

      { "data": "action"} 
    ],
    "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                $("td:first", nRow).html(iDisplayIndex +1);
               return nRow;
            },
  }); 
}

  function deleteDepartment(id)
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
      url: "{{route('deleteDepartment')}}",
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
                content: 'Department deleted successfully',
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

  function division_save(){
    var department_name = $('#department_name').val();
    var edit_code = $('#edit_code').val();
var token = $("input[name='_token']").val();

var fd = new FormData();

        if (edit_code == '') {
        var action_url = "{{route('departmentSave')}}";


        } else {

        var action_url = '{{ route("departmentUpdate") }}';

        fd.append('edit_code', edit_code);
        }
          
fd.append('department_name', department_name);

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
      $('#departmentModel').modal("hide");

    },
    error: function (jqXHR, textStatus, errorThrown) {
                
              ajax_error(jqXHR, textStatus, errorThrown); 
            }
});
  }

  function editDepartment (id){
    var editId=id;

        $.ajax({
                  type: 'post',
                  url: "{{route('editDepartment')}}",
                  data: {editId:editId,'_token': $('input[name="_token"]').val()},
                  dataType: 'json',
                  success: function (data) {
                    console.log(data)
                      $("#departmentModel").modal('show');
                      $('#department_heading').text('Update Department');
                      $('#btn-scheme-submit').text('Update');
                      
                      $("#department_create")[0].reset();
                      $("#department_name").val(data.department.name);
                    
                      $("#edit_code").val(data.department.id);
                     
                      
                  }
              });
  }

 
</script>

@stop