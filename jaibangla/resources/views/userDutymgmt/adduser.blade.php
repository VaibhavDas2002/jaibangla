@extends('userDutymgmt.base')

@section('action-content')
 <style>
.required-field::after {
    content: "*";
    color: red;
}
 .imageSize{
  font-size: 9px;
  color: #333;
 }

  </style>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new User and Assign Role <a href="{{ route('userDutymanagement') }}"><img  style="float:left" width="40" height="40" src="{{ asset ("/images/back.png") }}"/></a></div>
                <div class="panel-body">
                @if ( ($message = Session::get('success')))
            <div class="row">
              <div class="alert alert-success alert-block" style="margin:10px 30px 10px 30px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>

              </div>
            </div>
            @endif
            @if ( ($error = Session::get('error')))
            <div class="row">
              <div class="alert alert-danger alert-block" style="margin:10px 30px 10px 30px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $error }}</strong>

              </div>
            </div>
            @endif
            @if(count($errors) > 0)
           <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeError('crud_msg_Crud1')">
            <span aria-hidden="true">&times;</span>
           </button>
          <div class="alert alert-danger alert-block" id="crud_msg_Crud1">
          <ul>
          @foreach($errors as $error)
          <li><strong> {{ $error }}</strong></li>
          @endforeach
          </ul>
        </div>
        @endif
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('adduserpost') }}" >
                        {{ csrf_field() }}  
                        <div class="form-group">
                            <label for="full_name" class="col-md-4 control-label required-field">Full Name</label>

                            <div class="col-md-6">
                                <input id="full_name" type="text" class="form-control" 
                                name="full_name" value="{{ old('full_name') }}"  autocomplete="off" maxlength="200">

                               
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name_as_in_aadhar" class="col-md-4 control-label required-field">Full Name as in Aadhaar</label>

                            <div class="col-md-6">
                                <input id="full_name_as_in_aadhar" type="text" class="form-control" 
                                name="full_name_as_in_aadhar" value="{{ old('full_name_as_in_aadhar') }}"  autocomplete="off" maxlength="200">

                               
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username" class="col-md-4 control-label required-field">Display Name</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" 
                                name="username" value="{{ old('username') }}"  autocomplete="off">

                               
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label required-field">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"  autocomplete="off">

                               
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mobile" class="col-md-4 control-label required-field">Mobile No.</label>

                            <div class="col-md-6">
                                <input id="mobile" type="text" class="form-control NumOnly" 
                                name="mobile" value="{{ old('mobile') }}" 
                                 maxlength="10" autocomplete="off">

                               
                            </div>
                        </div>                   
                        
                      
                        
                        @if($role_visible)
                        <div class="form-group">
                            <label class="col-md-4 control-label required-field">Role</label>
                            <div class="col-md-6">
                                <select class="form-control" name="designation_id" id="designation_id" >
                                    <option value="">--Select Role--</option>
                                    @foreach($roles as $role)
                                   <option value="{{$role->name}}" @if($selected_role==$role->name) selected @endif>{{$role->name}}</option>
                                   @endforeach  
                                </select>
                                
                            </div>
                        </div> 
                        @else
                        <div class="form-group">
                            <label class="col-md-4 control-label required-field">Role</label>
                            <div class="col-md-6">
                                <select class="form-control" name="designation_id" id="designation_id">
                                    @foreach($roles as $role)
                                    <option value="{{$role->name}}" @if($selected_role==$role->name) selected @endif>{{$role->name}}</option>
                                    @endforeach  
                                </select>
                                
                            </div>
                        </div> 
                  @endif 
                        <div class="form-group">
                            <label class="col-md-4 control-label required-field">Scheme</label>
                            <div class="col-md-6">
                                <select  id="scheme" class="form-control select2" name="schemelist[]" 
                                multiple="multiple" >
                                  <option value="">--Select Scheme--</option>
                                  @foreach ($schemes as $scheme)
                                  <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                  @endforeach
                                </select>
                                 
                            </div>
                        </div>

                        @if($district_visible)

                         <div class="form-group" id="district_div">
                            <label class="col-md-4 control-label required-field">District</label>
                            <div class="col-md-6">
                                <select name="dist_code" id="dist_code" class="form-control" >
                                    <option value="">--Select  --</option>
                                     @foreach ($districts as $district)
                                    <option value="{{$district->district_code}}" > {{$district->district_name}}</option>
                                    @endforeach
                                </select>
                               
                            </div>
                        </div>
                        @else 
                        <input type="hidden" name="dist_code" id="dist_code" value="{{$district_code}}"/>
                        @endif
                        @if($is_urban_visible)
                        <div class="form-group" id="is_urban_div">
                            <label class="col-md-4 control-label required-field">Rural/Urban</label>
                            <div class="col-md-6">
                                <select name="is_urban" id="is_urban" class="form-control" >
                                    <option value="">--Select  --</option>
                                    @foreach ($levels as $key=>$value)
                                    <option value="{{$key}}" > {{$value}}</option>
                                    @endforeach
                                </select>
                              
                            </div>
                        </div> 
                        @else 
                        <input type="hidden" name="is_urban" id="is_urban" value="{{$is_urban}}"/>
                        @endif
                        @if($block_visible)
                        <div class="form-group" id="block_code_div">
                            <label class="col-md-4 control-label required-field">Block/Sub Div</label>
                            <div class="col-md-6">
                                <select name="block_code" id="block_code" class="form-control" >
                                    <option value="">--Select  --</option>
                                    
                                </select>
                               
                            </div>
                        </div>  
                        @else 
                        <input type="hidden" name="block_code" id="block_code" value="{{$block_code}}"/>
                        @endif
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Create
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
$(document).ready(function(){
    var role_id=$('#designation_id').val();
    if(role_id=='Approver'){
        $('#block_code').html('<option value="">--All --</option>'); 
        $("#is_urban_div").hide();
        $("#block_code_div").hide();

    }
    if(role_id=='Verifier'){
        $("#is_urban_div").show();
        $("#block_code_div").show();

    }
    if(role_id=='Operator'){
        $("#is_urban_div").show();
        $("#block_code_div").show();

    }
     $(".NumOnly").keyup(function(event) {
              
        $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
    }); 
    $('#designation_id').change(function() {
        var designation_id=$(this).val();
        if(designation_id=='HOD' || designation_id=='Dashboard' || designation_id=='MisState' || designation_id=='DDO' || designation_id=='Delegated DDO' || designation_id=='PRD_DDO' || designation_id=='HED_DDO' || designation_id=='AuditOfficer'){
            $('#block_code').html('<option value="">--Please Select--</option>'); 
            $("#district_div").hide();
            $("#is_urban_div").hide();
            $("#block_code_div").hide();
        }
        else if(designation_id=='Approver' || designation_id=='Delegated Approver'  || designation_id=='StatusCheckerDistrict'){
            $('#dist_code').val(''); 
            $("#district_div").show();
            $("#is_urban_div").hide();
            $("#block_code_div").hide();
        }
        else if(designation_id=='Verifier'){
            //$('#dist_code').val(''); 
            $('#block_code').html('<option value="">--Please Select--</option>'); 
            $("#district_div").show();
            $("#is_urban_div").show();
            $("#block_code_div").show();
        }
        else if(designation_id=='Operator'){
           // $('#dist_code').val(''); 
            $('#block_code').html('<option value="">--Please Select--</option>'); 
            $("#district_div").show();
            $("#is_urban_div").show();
            $("#block_code_div").show();
        }
    });
    $('#dist_code').change(function() {
        var district=$(this).val();
        $('#is_urban').val('');
        $('#block_code').html('<option value="">--Please Select --</option>'); 
    });
   $('#is_urban').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#block_code').html('<option value="">--Please Select --</option>'); 
        }
        select_district_code= $('#dist_code').val();
       // alert(select_district_code);
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district_code").focus();
              $('#block_code').html('<option value="">--Please Select --</option>'); 
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--Please Select--</option>';
        if(select_body_type==2){
          $("#blk_sub_txt").text('Block');
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
            $('#block_code').html(htmlOption);
            $("#block_code_div").show();
        }else if(select_body_type==1){
          $("#blk_sub_txt").text('Sub Div');
            $.each(subDistricts, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
            $('#block_code').html(htmlOption);
            $("#block_code_div").show();
        } 
        else{
              $('#block_code').html('<option value="">--Please Select --</option>'); 
        }   
        
        }
    });
});
function closeError(divId){
   $('#'+divId).hide();
  }
</script>