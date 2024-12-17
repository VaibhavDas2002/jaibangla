<style type="text/css">
    .has-error
    {
      border-color:#cc0000;
      background-color:#ffff99;
    }
    .preloader1{
      position: fixed;
      top:40%;
      left: 52%;
      z-index: 999;
    }
    .preloader1 {
      background: transparent !important;
    }
    #loadingDiv {
      position:absolute;
      top:0px;
      right:0px;
      width:100%;
      height:100%;
      background-color:#fff;
      background-image:url('../images/ajaxgif.gif');
      background-repeat:no-repeat;
      background-position:center;
      z-index:10000000;
      opacity: 0.4;
      filter: alpha(opacity=40); /* For IE8 and earlier */
    }
    
    .loadingDivModal{
      position:absolute;
      top:0px;
      right:0px;
      width:100%;
      height:100%;
      background-color:#fff;
      background-image:url('../images/ajaxgif.gif');
      background-repeat:no-repeat;
      background-position:center;
      z-index:10000000;
      opacity: 0.4;
      filter: alpha(opacity=40); /* For IE8 and earlier */
    }
    #updateDiv {
      border: 1px solid #d9d9d9;
      padding: 8px;  
      box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }
</style>
  @extends('layouts.app-template-datatable_new')
  @section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Change Request Management
            </h1>
            <ol class="breadcrumb">
                <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                     {{-- <div id="loadingDiv"></div>  --}}
                    <div class="panel panel-default">
                        <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span id="panel-icon">Enter Filter Criteria</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (($message = Session::get('success')) )
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }} </strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('message')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('msg1')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12 pull-right" id="addButton">
                                            <a class="btn btn-primary" id="add_new">Add New Requirement</a>
                                        </div>
                                        <br>
                                        <br>
                                        <br>
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="col-md-3">
                                              <label class=" control-label">Category Type<span class="text-danger">*</span></label>
                                              <select class="form-control select2" name="category" id='category' required>
                                                <option value="">--Select Category--</option>
                                                @foreach ($change_request as $category)
                                                <option value="{{$category->code}}">{{$category->name}}</option>
                                                @endforeach
                                              </select>
                                              <span class="text-danger" id="error_category"></span>
                                            </div>
                                            <div class="col-md-3" id="comun_div">
                                              <label class=" control-label">Comunication Type</label>
                                              <select class="form-control " name="comun_type" id='comun_type' required>
                                                <option value="">--Select Comunication--</option>
                                                @foreach ($comunnication_type as $coum_type)
                                                <option value="{{$coum_type->code}}">{{$coum_type->name}}</option>
                                                @endforeach
                                              </select>
                                              <span class="text-danger" id="error_comunication"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <label class=" control-label">From Date</label>
                                                <input type=date name="from_date" id="from_date" class="form-control" max="{{ date('Y-m-d') }}">
                                                <span class="text-danger" id="error_from_date"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <label class=" control-label">To Date</label>
                                                <input type=date name="to_date" id="to_date" class="form-control" max="{{ date('Y-m-d') }}">
                                                <span class="text-danger" id="error_to_date"></span>
                                            </div>
                                            <div class="text-center" style="margin-top: 100px;" >
                                                <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button" ><i class="fa fa-search"></i> Search</button>&nbsp;
                                                {{-- <button class="btn btn-default" name="reset_btn" id="reset_btn" type="button" disabled><i class="fa fa-refresh"></i> Reset</button> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="res_div" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table display" cellspacing="0" width="100%"> 
                                        <thead style="font-size: 12px;">
                                            <th width="5%">ID</th>
                                            <th width="10%">Subject</th>
                                            <th width="10%">Description</th>
                                            <th width="10%">Implemented Date</th>
                                             <th width="5%">Download</th> 
                                        </thead>
                                        <tbody style="font-size: 14px;"></tbody>   
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="modalUpdatebank" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Upload Details</h4>
                    </div>
                    <div class="modal-body">
                        <form id="vb" class="form-horizontal">
                       
                            <div class="form-group">
                                <label class="col-md-4 control-label">Category Type<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select class="form-control " name="category_type" id='category_type' required>
                                        <option value="">--Select Category--</option>
                                        @foreach ($change_request as $category)
                                        <option value="{{$category->code}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                      <span class="text-danger" id="error_category_type"></span>
                                </div>
                            </div>  
                            <div class="form-group" id="coum_type_id">
                                <label class="col-md-4 control-label">Comunication Type</label>
                                <div class="col-md-6">
                                    <select class="form-control " name="comun_type_id" id='comun_type_id' required>
                                        <option value="">--Select Comunication--</option>
                                        @foreach ($comunnication_type as $coum_type)
                                        <option value="{{$coum_type->code}}">{{$coum_type->name}}</option>
                                        @endforeach
                                    </select>
                                      <span class="text-danger" id="error_comunication_type"></span>
                                </div>
                            </div>  
                            <div class="form-group">
                                <label for="menu_icon" class="col-md-4 control-label">Implemented Date <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type=date name="implement_date" id="implement_date" class="form-control" max="{{ date('Y-m-d') }}">
                                    <span class="text-danger" id="error_upload_date"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Scheme<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select  id="scheme" class="form-control select2" name="schemelist[]" 
                                  multiple="multiple" style="width: 100%">
                                    <option value="">--Select Scheme--</option>
                                    @foreach ($schemes as $scheme)
                                    <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                    @endforeach
                                  </select>
                                      <span class="text-danger" id="error_scheme"></span>
                                </div>
                            </div>  
                            <div class="form-group">
                              <label for="firstname" class="col-md-4 control-label ">Documents<span class="text-danger">*</span></label>
                              <div class="col-md-6">
                                <button type="button" name="add" id="add" class="btn btn-success btn-sm" title="Click to add files"><i class="glyphicon glyphicon-paperclip"></i> <b>Attachments</b></button>
                                <table id="dynamic_field" class="table table-condensed" style="font-size: 14px;">  
                                    <span class="text-success" id="no_of_file" style="font-weight: bold;">0</span> files size of
                                    <span class="text-primary" id="total_file_size" style="font-weight: bold; display: none;">0</span>
                                    <span class="text-success" id="file_size_mb" style="font-weight: bold;">0.00 MB</span>
                                    <span class="text-danger" id="file_accross_size" style="font-weight: bold; float: right; padding-right: 30px;"></span>
                                </table>
                              </div>
                                
                            </div>
                            <div class="form-group">
                                <label for="firstname" class="col-md-4 control-label">Subject<span class="text-danger">*</span></label>
                                <textarea name="subject" id="subject"  rows="2" cols="65" placeholder="Enter Subject..."></textarea>
                                <span id="error_remarks" class="text-danger"></span>
                            </div>
                            <div class="form-group">
                                <label for="firstname" class="col-md-4 control-label">Description<span class="text-danger">*</span></label>
                                <textarea name="description" id="description"  rows="3" cols="65" placeholder="Enter Description..."></textarea>
                                <span id="error_remarks" class="text-danger"></span>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="upload" data-dismiss="modal">
                        <span class="crud-txt">Add</span>
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
  @endsection
  <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
  <script>
   $(document).ready(function(){
    var interval = setInterval(function () {
        var momentNow = moment();
        $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
        $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);
    $('#loadingDiv').hide();
    $('#comun_div').hide();
    $("#coum_type_id").hide();
    $("#category_type").on('change', function(){
        var category_type_id =  $("#category_type").val();
        if(category_type_id == 2)
        {
          $("#coum_type_id").show();
        } 
        else
        {
          $("#coum_type_id").hide();
        }
    });
    $("#category").on('change', function(){
        var category_type =  $("#category").val();
        if(category_type == 2)
        {
          $("#comun_div").show();
        } 
        else
        {
          $("#comun_div").hide();
        }
    });

    $('#submit_btn').click(function(){
        if($.trim($('#category').val()).length == 0){
            error_category = 'Category is required';
        $('#error_category').text(error_category);
      }
      else{
        error_category = '';
        $('#error_category').text(error_category);
      }
      

      if( error_category != '' ){
        return false;
      }
      else{
        $('#loadingDiv').show();
        $('#res_div').show();
        var msg = 'Requirement Details';
        $('#panel_head').text(msg);
        if ( $.fn.DataTable.isDataTable('#example') ) {
          $('#example').DataTable().destroy();
        }
        $('#example tbody').empty();
        var table=$('#example').DataTable( {
          dom: 'Blfrtip',
          "scrollX": true,
          "paging": true,
          "searchable": true,
          "ordering":false,
          "bFilter": true,
          "bInfo": true,
          "pageLength":25,
          'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
          },
          "ajax": 
          {
            url: "{{ route('post-daily-upload') }}",
            type: "post",
            data:function(d){
                d.category = $('#category').val(), 
                d.comun_type= $('#comun_type').val(),
                d.from_date = $('#from_date').val(),
                d.to_date = $('#to_date').val(),
                d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDiv').hide();
              $('.preloader1').hide();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            $('#loadingDiv').hide();
            //console.log('Data rendered successfully');
          },
          "columns": [
            { "data": "id" },
            { "data": "subject" },
            { "data": "description" },
            { "data": "implemented_date"},
            { "data": "download"},
          ],
      
          "buttons": [
            {
               extend: 'pdf',
               footer: true,
               pageSize:'A4',
               //orientation: 'landscape',
               pageMargins: [ 40, 60, 40, 60 ],
               exportOptions: {
                    columns: [0,1,2,3,4,5,6],

                }
               },
               {
                   extend: 'excel',
                   footer: true,
                   pageSize:'A4',
                   //orientation: 'landscape',
                   pageMargins: [ 40, 60, 40, 60 ],
                   exportOptions: {
                        columns: [0,1,2,3,4,5,6],
                        stripHtml: false,
                    }
                },
            //'pdf','excel','print'
          ],
        });
      }
    });
    $(document).on('click', '#add_new', function(){ 
        $('#modalUpdatebank').modal('show');
    });
    $(document).on('click', '#add_new', function(){ 
        $('#modalUpdatebank').modal('show');

    });
    var i=1; 
    $('#add').click(function(){  
      i++;
      var files_size = document.getElementById('total_file_size').innerHTML;
        if (files_size > 1024000) {
           var mb = (((files_size)/1024)/1024).toFixed(3);
           alert('Total size- '+mb+' MB. You can send upto 1 MB! Please remove files.');
           document.getElementById('file_accross_size').innerHTML = '(You can send upto 2 MB.)';
        }
      
      // //if (i > 10) {alert('Above 10');}
      else{
        //$('#dynamic_field').append('<tr id="row'+i+'"><td><input type="file" name="raise_file[]" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove btn-xs"><b>x</b></button></td></tr>');  
        //$('#dynamic_field').append('<tr id="row'+i+'"><td><input type="file" id='+i+' name="raise_file[]" onchange="validateImage('+i+')" /></td><td><span style="font-weight:bold;" id="size'+i+'"></span> <button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove btn-xs"><b>x</b></button></td></tr>');  
        $('#dynamic_field').append('<tr id="row'+i+'" style="display: none;"><td width="80%"><span style="font-weight:bold;" id="name'+i+'"></span> <input type="file" id='+i+' name="raise_file[]" onchange="validateImage('+i+')" style="display:none;" /></td><td width="20%"><span style="font-weight:bold;" id="size'+i+'"></span> <button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove btn-xs"><b>x</b></button></td></tr>');
        $('#'+i).trigger('click');
      }
  });
  $(document).on('click', '.btn_remove', function(){
  //alert(id);  
      var button_id = $(this).attr("id");

      var file = document.getElementById(button_id).files[0];
      if (file != undefined) { 
        total_file_size = document.getElementById('total_file_size').innerHTML;
        var total_size = Number(total_file_size) - file.size;
        document.getElementById('total_file_size').innerHTML = total_size;
        var total_kb = total_size/1024;
        document.getElementById('file_size_mb').innerHTML = (total_kb/1024).toFixed(2)+' MB';
        file_no = document.getElementById('no_of_file').innerHTML;
        var file_no = Number(file_no) - 1;
        document.getElementById('no_of_file').innerHTML = file_no;
      }
      $('#row'+button_id+'').remove();  
  });


    $(document).on('click', '#upload', function(e) {
        e.preventDefault();
        
        var category_type = $('#category_type').val();
        var comun_type_id = $('#comun_type_id').val();
        var scheme = $('#scheme').val();
        var implement_date = $('#implement_date').val();
        var subject = $('#subject').val();
        var description = $('#description').val();
        var formData = new FormData();
        formData.append('category_type', category_type);
        formData.append('comun_type_id', comun_type_id);
        formData.append('scheme', scheme);
        formData.append('implement_date', implement_date);
        formData.append('subject', subject);
        formData.append('description', description);
        formData.append('_token', '{{ csrf_token() }}');
        $('input[name="raise_file[]"]').each(function() {
            var files = $(this)[0].files;
            for (var i = 0; i < files.length; i++) {
                formData.append('raise_file[]', files[i]); // Append each file to formData
            }
        });
        $.confirm({
          
            title: 'Confirm!',
            type: 'orange',
            icon: 'fa fa-warning',
            content: '<strong>Are you want to Add New Requirement ?</strong>',
            buttons: {
                confirm:  {
                    text: 'Confirm',
                    btnClass: 'btn-blue',
                    keys: ['enter', 'shift'],
                    action: function(){
                        mobileUpdate(formData);
                    }
                },
                cancel: function () {
                },
            }
        });
    });

    function mobileUpdate(formData){
        $.ajax({
            type: 'POST',
            url: "{{ route('post-upload-details') }}",
            data: formData,
            processData: false, // prevent jQuery from automatically transforming the data into a query string
            contentType: false, // set content type to false for form-data
            cache: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status == 1) {
                    $.alert({
                        title: response.title,
                        type: response.type,
                        icon: response.icon,
                        content: response.msg
                    });
                    // Reset form or perform other success actions
                }
                else {
                    $.alert({
                        title: response.title,
                        type: response.type,
                        icon: response.icon,
                        content: response.msg
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Handle errors here
            }
        });
    }
});
function validateImage(id) {
        //var formData = new FormData();
     
        var file = document.getElementById(id).files[0];

        //formData.append("Filedata", file);
        var t = file.type.split('/').pop().toLowerCase();
        // console.log(file);
        if (t != "jpeg" && t != "pdf" && t != "png") {
            alert('Please select jpeg, jpg, png and pdf file only.');
            document.getElementById(id).value = '';
            return false;
        }

        //$('#row'+id).attr('display','none');
        total_file_size = document.getElementById('total_file_size').innerHTML;
        var total_size = file.size + Number(total_file_size);
        document.getElementById('total_file_size').innerHTML = total_size;
        var total_kb = total_size/1024;
        document.getElementById('file_size_mb').innerHTML = (total_kb/1024).toFixed(2)+' MB';
        var size_KB = file.size / 1024;
        //document.getElementById('size'+id).innerHTML = (size_KB/1024).toFixed(2)+' MB';
        document.getElementById('size'+id).innerHTML = size_KB.toFixed(0)+' KB';
        document.getElementById('name'+id).innerHTML = file.name;
        file_no = document.getElementById('no_of_file').innerHTML;
        var file_no = Number(file_no) + 1;
        document.getElementById('no_of_file').innerHTML = file_no;
        //console.log(total_size);
        
        if (file != undefined) {
            document.getElementById("row"+id).style.display = "";
        }
        return true;
    }

 </script>