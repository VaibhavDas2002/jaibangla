@extends('system-mgmt.division.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of divisions</h3>
        </div>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#divisionModel">
          Add New Division
        </button>
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

              <th >Serial No</th>
              <th  >Division Name</th>
          
              <th  >Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th >Serial No</th>
              <th >Division Name</th>
          
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
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
  <!--Add  Division Modal -->
<div id="divisionModel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <form class="form-horizontal" id="division_create" role="form" method="POST" action="#">
        <input type="hidden" id="edit_code">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><span class="crud-txt" id="division_heading">Add New Division</span></h4>
        </div>
        <div class="modal-body">
          <div class="" style="display: none"><img src="{{ asset('images/ZKZg.gif')}}" class="submit_loader"
              width="50px" height="50px"></div>
          <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
          </div>
          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-4 control-label required">Division Name</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

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
<!--Add Update Division Modal -->
@endsection

@section('script')
<script>
  $(function(){
    division_list();
    $('#divisionModel').on('hide.bs.modal', function (e) {
            $("#division_create")[0].reset();
            $('#division_create').bootstrapValidator('resetForm', true);
            $('#edit_code').val('');
        
            $('#division_heading').text('Add New Division');
                      $('#btn-scheme-submit').text('Add');
        });
    
    $('#division_create')
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
                                    message: 'Please enter division name'
                                }
                            }
                        }

                    }
                }).on('success.form.bv', function (e) {
            // Prevent form submission
            e.preventDefault();
           division_save();

        });
   
  });


function division_list(){

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
      url: "{{ route('getDivisionList') }}",
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

  function deleteDivision( id)
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
      url: "{{route('deleteDivision')}}",
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
                content: 'Division deleted successfully',
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
    var name = $('#name').val();
    var edit_code = $('#edit_code').val();
var token = $("input[name='_token']").val();

var fd = new FormData();

        if (edit_code == '') {
        var action_url = "{{route('divisionSave')}}";


        } else {

        var action_url = '{{ route("divisionUpdate") }}';

        fd.append('edit_code', edit_code);
        }
          
fd.append('name', name);

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
      $('#divisionModel').modal("hide");

    },
    error: function (jqXHR, textStatus, errorThrown) {
                
              ajax_error(jqXHR, textStatus, errorThrown); 
            }
});
  }

  function UpdateDivision (id){
    var editId=id;

        $.ajax({
                  type: 'post',
                  url: "{{route('editDivision')}}",
                  data: {editId:editId,'_token': $('input[name="_token"]').val()},
                  dataType: 'json',
                  success: function (data) {
                    console.log(data)
                      $("#divisionModel").modal('show');
                      $('#division_heading').text('Update Division');
                      $('#btn-scheme-submit').text('Update');
                      
                      $("#division_create")[0].reset();
                      $("#name").val(data.division.name);
                    
                      $("#edit_code").val(data.division.id);
                     
                      
                  }
              });
  }

 
</script>

@stop