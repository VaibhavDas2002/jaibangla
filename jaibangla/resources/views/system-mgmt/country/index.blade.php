@extends('system-mgmt.country.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of countries</h3>
        </div>
        <div class="col-sm-4">
          {{-- <a class="btn btn-primary" href="{{ route('country.create') }}">Add new country</a> --}}
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#countryModel">
            Add New country
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
              <th  >Country Code</th>
              <th  > Country Name</th>
             
              <th  >Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th >Serial No</th>
              <th  >Country Code</th>
              <th  > Country Name</th>
             
          
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
      <form method="POST" action="{{ route('country.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Search'])
          @component('layouts.two-cols-search-row', ['items' => ['Country_Code', 'Name'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['country_code'] : '', isset($searchingVals) ? $searchingVals['name'] : '']])
          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Country Code</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Country Name</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($countries as $country)
                <tr role="row" class="odd">
                  <td>{{ $country->country_code }}</td>
                  <td>{{ $country->name }}</td>
                  <td>
                    <form class="row" method="POST" action="{{ route('country.destroy', ['id' => $country->id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('country.edit', ['id' => $country->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
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
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Country Code</th>
                <th width="20%" rowspan="1" colspan="1">Country Name</th>
                <th rowspan="1" colspan="2">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($countries)}} of {{count($countries)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $countries->links() }}
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
  <div id="countryModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <form class="form-horizontal" id="country_create" role="form" method="POST" action="#">
          <input type="hidden" id="edit_code">
  
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><span class="crud-txt" id="country_heading">Add New Country</span></h4>
          </div>
          <div class="modal-body">
            <div class="" style="display: none"><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader"
                width="50px" height="50px"></div>
            <div class="alert alert-danger print-error-msg" style="display:none">
              <ul></ul>
            </div>

            <div class="form-group{{ $errors->has('country_name') ? ' has-error' : '' }}">
              <label for="country_name" class="col-md-4 control-label required">Country Name</label>

              <div class="col-md-6">
                  <input id="country_name" type="text" class="form-control" name="country_name" value="{{ old('country_name') }}" required autofocus>

                  @if ($errors->has('country_name'))
                      <span class="help-block">
                          <strong>{{ $errors->first('country_name') }}</strong>
                      </span>
                  @endif
              </div>
          </div>
            <div class="form-group{{ $errors->has('country_code') ? ' has-error' : '' }}">
              <label for="country_code" class="col-md-4 control-label required">Country Code</label>

              <div class="col-md-6">
                  <input id="country_code" type="text" class="form-control" name="country_code" value="{{ old('country_code') }}" required>
                  @if ($errors->has('country_code'))
                      <span class="help-block">
                          <strong>{{ $errors->first('country_code') }}</strong>
                      </span>
                  @endif
              </div>
          </div>
  
  
  
  
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="btn-country-submit">
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
    country_list();
    $('#countryModel').on('hide.bs.modal', function (e) {
            $("#country_create")[0].reset();
            $('#country_create').bootstrapValidator('resetForm', true);
            $('#edit_code').val('');
        
            $('#country_heading').text('Add New Country');
                      $('#btn-country-submit').text('Add');
        });
    
    $('#country_create')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                      country_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter country name'
                                },
                                regexp: {
                                    regexp: /^[a-z\s]+$/i,
                                    message: 'Country name can consist of alphabetical characters and spaces only'
                                }

                            }
                        },
                        country_code: {
                            validators: {
                                notEmpty: {
                                    message: 'Please enter country code'
                                },
                                
                                regexp: {
                                    regexp: /^[0-9]+$/,
                                    message: 'Country code contain only integer value'
                                }

                            }
                        },
                                
                    }
                }).on('success.form.bv', function (e) {
            // Prevent form submission
            e.preventDefault();
           country_save();

        });
   
  });


function country_list(){

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
      url: "{{ route('getCountrytList') }}",
      type: "GET",
      data:{
        _token: "{{csrf_token()}}",
      
      }
    } ,
    "columns": [
      { "data": "serial_no","defaultContent":""},
      { "data": "country_code",},
      { "data": "name"},
     
      { "data": "action"} 
    ],
    "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                $("td:first", nRow).html(iDisplayIndex +1);
               return nRow;
            },
  }); 
}

  function deleteCountry(id)
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
      url: "{{route('deleteCountry')}}",
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
                content: 'Country deleted successfully',
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

  function country_save(){
    var country_name = $('#country_name').val();
    var country_code = $('#country_code').val();
    var edit_code = $('#edit_code').val();
var token = $("input[name='_token']").val();

var fd = new FormData();

        if (edit_code == '') {
        var action_url = "{{route('countrySave')}}";


        } else {

        var action_url = '{{ route("countryUpdate") }}';

        fd.append('edit_code', edit_code);
        }
          
fd.append('country_name', country_name);
fd.append('country_code', country_code);
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
      $('#countryModel').modal("hide");

    },
    error: function (jqXHR, textStatus, errorThrown) {
                
              ajax_error(jqXHR, textStatus, errorThrown); 
            }
});
  }

  function editCountry (id){
    var editId=id;

        $.ajax({
                  type: 'post',
                  url: "{{route('editCountry')}}",
                  data: {editId:editId,'_token': $('input[name="_token"]').val()},
                  dataType: 'json',
                  success: function (data) {
                    console.log(data)
                      $("#countryModel").modal('show');
                      $('#country_heading').text('Update Country');
                      $('#btn-country-submit').text('Update');
                      
                      $("#country_create")[0].reset();
                      $("#country_name").val(data.country.name);
                      $("#country_code").val(data.country.country_code);
                      $("#edit_code").val(data.country.id);
                     
                      
                  }
              });
  }

 
</script>
@stop