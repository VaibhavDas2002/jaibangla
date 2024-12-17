@extends('system-mgmt.state.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of states</h3>
        </div>
        <div class="col-sm-4">
          {{-- <a class="btn btn-primary" href="{{ route('state.create') }}">Add new state</a> --}}
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#stateModel">
            Add new state
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

              <th >Serial No</th>
              <th  >Country Name</th>
              <th  > State Name</th>
              <th  > State Code</th>
              <th  >Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th >Serial No</th>
              <th  >Country Name</th>
              <th  > State Name</th>
              <th  > State Code</th>
             
          
              <th >Action</th>
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
      <form method="POST" action="{{ route('state.search') }}">
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
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="state: activate to sort column ascending">State Name</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Country Name</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($states as $state)
                <tr role="row" class="odd">
                  <td>{{ $state->name }}</td>
                  <td>{{ $state->country_name }}</td>
                  <td>
                    <form class="row" method="POST" action="{{ route('state.destroy', ['id' => $state->id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('state.edit', ['id' => $state->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
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
                <th width="20%" rowspan="1" colspan="1">State Name</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Country Name</th>
                <th rowspan="1" colspan="2">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($states)}} of {{count($states)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $states->links() }}
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
  <div id="stateModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <form class="form-horizontal" id="state_create" role="form" method="POST" action="#">
          <input type="hidden" id="edit_code">
  
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><span class="crud-txt" id="state_heading">Add New State</span></h4>
          </div>
          <div class="modal-body">
            <div class="" style="display: none"><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader"
                width="50px" height="50px"></div>
            <div class="alert alert-danger print-error-msg" style="display:none">
              <ul></ul>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label required">Country</label>
              <div class="col-md-6">
                  <select class="form-control" name="country_id" id="country_id">
                      @foreach ($countries as $country)
                          <option value="{{$country->id}}">{{$country->name}}</option>
                      @endforeach
                  </select>
              </div>
          </div>
            <div class="form-group{{ $errors->has('state_name') ? ' has-error' : '' }}">
              <label for="state_name" class="col-md-4 control-label required">State Name</label>

              <div class="col-md-6">
                  <input id="state_name" type="text" class="form-control" name="state_name" value="{{ old('state_name') }}" required autofocus>

                  @if ($errors->has('state_name'))
                      <span class="help-block">
                          <strong>{{ $errors->first('state_name') }}</strong>
                      </span>
                  @endif
              </div>
          </div>
           <div class="form-group{{ $errors->has('state_code_val') ? ' has-error' : '' }}">
              <label for="state_code_val" class="col-md-4 control-label required">State Code</label>

              <div class="col-md-6">
                  <input id="state_code_val" type="text" class="form-control" name="state_code_val" value="{{ old('state_code_val') }}" required autofocus>

                  @if ($errors->has('state_code_val'))
                      <span class="help-block">
                          <strong>{{ $errors->first('state_code_val') }}</strong>
                      </span>
                  @endif
              </div>
          </div>
    
  
  
  
  
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="btn-state-submit">
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
    state_list();
    $('#stateModel').on('hide.bs.modal', function (e) {
            $("#state_create")[0].reset();
            $('#state_create').bootstrapValidator('resetForm', true);
            $('#edit_code').val('');
        
            $('#state_heading').text('Add New State');
                      $('#btn-state-submit').text('Add');
        });
    
    $('#state_create')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                      state_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter state name'
                                },
                                regexp: {
                                    regexp: /^[a-z\s]+$/i,
                                    message: 'State name can consist of alphabetical characters and spaces only'
                                }

                            }
                        },
                        country_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Please select country'
                                }

                            }
                        },
                        state_code_val: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter state code'
                                },
                                
                                regexp: {
                                    regexp: /^[0-9]+$/,
                                    message: 'State code contain only integer value'
                                },
                                stringLength: {
                                    min: 1,
                                    max: 3,
                                    message: 'State code value  should not cross 3 characters.'
                                }

                            }
                        },

                        
                                
                    }
                }).on('success.form.bv', function (e) {
            // Prevent form submission
            e.preventDefault();
           state_save();

        });
   
  });


function state_list(){

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
      url: "{{ route('getStatetList') }}",
      type: "GET",
      data:{
        _token: "{{csrf_token()}}",
      
      }
    } ,
    "columns": [
      { "data": "serial_no","defaultContent":""},
      {data: 'country_name'},
      { "data": "name"},
      { "data": "state_code"},
     
      { "data": "action"} 
    ],
    "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                $("td:first", nRow).html(iDisplayIndex +1);
               return nRow;
            },
  }); 
}

  function deleteState(id)
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
      url: "{{route('deleteState')}}",
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
                content: 'State deleted successfully',
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

  function state_save(){
    var country_id = $('#country_id').val();
    var state_name = $('#state_name').val();
    var state_code_val = $('#state_code_val').val();
    var edit_code = $('#edit_code').val();
var token = $("input[name='_token']").val();

var fd = new FormData();

        if (edit_code == '') {
        var action_url = "{{route('stateSave')}}";


        } else {

        var action_url = '{{ route("stateUpdate") }}';

        fd.append('edit_code', edit_code);
        }
          
fd.append('country_id', country_id);
fd.append('state_name', state_name);
fd.append('state_code_val', state_code_val);
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
      $('#stateModel').modal("hide");

    },
    error: function (jqXHR, textStatus, errorThrown) {
                
              ajax_error(jqXHR, textStatus, errorThrown); 
            }
});
  }

  function editState (id){
    var editId=id;

        $.ajax({
                  type: 'post',
                  url: "{{route('editState')}}",
                  data: {editId:editId,'_token': $('input[name="_token"]').val()},
                  dataType: 'json',
                  success: function (data) {
                    console.log(data)
                      $("#stateModel").modal('show');
                      $('#state_heading').text('Update State');
                      $('#btn-state-submit').text('Update');
                      
                      $("#state_create")[0].reset();
                      $("#state_name").val(data.state.name);
                      $("#state_code_val").val(data.state.state_code);
                      $("#country_id").val(data.state.country_id);
                      $("#edit_code").val(data.state.id);
                     
                      
                  }
              });
  }

 
</script>
@stop