@extends('LifeCertificate.base')
@section('action-content')
<style>
  *{
    font-size: 15px;
  }

.field-name{
  float:left;
  font-weight:600;
  font-size:17px;
  margin-right:3%;
  padding-top:1%;
}
.field-value{
  
  
  font-size:17px;
  padding-top:1%;
  
}
.required-field::after {
      content: "*";
      color: red;
}
.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
}
.section1{
    border: 1.5px solid #9187878c;
    overflow: hidden;
    padding-bottom: 10px;
   
   
}
.color1{
  
  background-color: #dcdfdf;
}
.color1 h3{
margin: 10px 0px 10px 0px !important;
}

.setPos{
  padding: 0px 0px 10px 0px;
  margin: 10px 0px 10px 0px;
  border:1px solid #dcdfdf;
  overflow: hidden;
}
.modal_field_name{
  float:left;
  font-weight: 700;
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}

.modal_field_value{
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}

.modal-header{
  background-color: #7fffd4;
}

@media print {
  .example-screen {
       display: none;
    }

    *{
    font-size: 15px;
  }

.field-name{
  float:left;
  font-weight:600;
  font-size:17px;
  margin-right:3%;
  padding-top:1%;
}
.field-value{
  
  
  font-size:17px;
  padding-top:1%;
  
}

.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
}
.section1{
    border: 1.5px solid #9187878c;
    overflow: hidden;
    padding-bottom: 10px;
   
   
}
.color1{
  
  background-color: #dcdfdf;

}
.color1 h3{
 margin: 10px 0px 10px 0px !important;
}

.setPos{
  padding: 0px 0px 10px 0px;
  margin: 10px 0px 10px 0px;
  border:1px solid #dcdfdf;
  overflow: hidden;
}
.modal_field_name{
  float:left;
  font-weight: 700;
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}

.modal_field_value{
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}

.modal-header{
  background-color: #7fffd4;
}

  /*.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
}
.section1{
    border: 1.5px solid #9187878c!important;
    margin: 0.25cm!important;
    padding: 0.25cm!important;
    page-break-inside : avoid;
}
.color1{
  margin: 0%!important;
  background-color: #5f9ea061!important;
  -webkit-print-color-adjust: exact; 
}
.modal_field_name{
  float:left!important;
  font-weight: 700!important;
  margin-right:0.5cm!important;

}

.modal_field_value{
  padding-top:0.30cm!important;

}
.color1{
  margin: 0%!important;
  background-color: #7fffd4!important;
 -webkit-print-color-adjust: exact; 
}

.modal-header{
  background-color: #7fffd4!important;
 -webkit-print-color-adjust: exact; 
}
#divToPrint{
}*/
}


</style>
<section >
<div class="modal-fade" tabindex="-1" role="document">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="example-screen">
               <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                <!-- <span aria-hidden="true">&times;</span> -->
               <!-- </button> -->
               <h2 class="modal-title " style="text-align: center;"><a href="{{ route('lifeCertificte', ['scheme_id'=>$row->scheme_id])}}">
                <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a> View Application Form</h2>
               
            </div>
            <div class="modal-body">
              <!--   <h4 class="example-screen" style="text-align: center;">Please Verify or Reject Employee's application with Comments</h4> -->
                
              @if(count($errors) > 0)
      <div class="alert alert-danger alert-block">
        <ul>
          @foreach($errors as $error)
          <li><strong> {{ $error }}</strong></li>
          @endforeach
        </ul>
      </div>
      @endif
                <!-- We display the details entered by the user here -->
                <div class="section1">
                  <div class="row">
                  <div class="col-md-12">
                    <h3 style="text-align: center; color:red;">Beneficiary ID:{{$row->id}}
                      
                      </h3>
                  </div>


                  </div>
                  @include('pension-details-view.personal_details')
                  @include('pension-details-view.personal_identification')
                  @include('pension-details-view.contact_details')
                  @include('pension-details-view.bank_details')
                  @include('pension-details-view.enclosure_list')
                  @include('pension-details-view.additional_details')
              
                        @if($is_operator && is_null($row->next_level_role_id_edit))
                        <form method="post" id="register_form" action="{{url('editLifeCertificatePost')}}" enctype="multipart/form-data"
                    class="submit-once" onsubmit="return client_validation()">
                    <input type="hidden" name="scheme_id" id="scheme_id" value="{{$row->scheme_id}}"/>
                    <input type="hidden" name="id" id="id" value="{{$row->id}}"/>
                     {{ csrf_field() }}
                        <div class="row">
                          
                        
                          <div class="form-group col-md-6">
                                    <label   class="required-field" >{{ $doc_certificate['doc_name'] }}</label>
                                    <input type="file" name="doc_{{ $doc_certificate['id']}}" id="doc_{{ $doc_certificate['id'] }}" class="form-control" tabindex="1" />
                                    <div class="imageSize">(Image type must be {{ $doc_certificate['doc_type'] }} and image size max {{ $doc_certificate['doc_size_kb'] }}KB)</div>
                                    <span id="error_doc_{{ $doc_certificate['id'] }}" class="text-danger"></span>
                          </div>
                         
                        </div>
                        
                        <div class="row">
                       
                          <div class="form-group col-md-4">
                            <label  class="required-field">As on Date</label>
                            <input type="date" name="life_certificate_ason_date" id="life_certificate_ason_date" 
                            class="form-control"  tabindex="5" value="" max="<?php echo date("Y-m-d"); ?>"/>
                            <span id="error_life_certificate_ason_date" class="text-danger"></span>
                          </div>
                         
                         
                       </div>
                       <center> <button type="submit" id="submit" value="Submit"
                          class="btn btn-success success btn-lg modal-submit">Submit </button>
                        <button type="button" id="submitting" value="Submit" class="btn btn-success success btn-lg"
                          disabled>Submitting please wait</button></center>
                      

                      </div>
                    </form>
                    @endif
                    @if($is_verifier && $row->next_level_role_id_edit==1)
                       
                    
                   
                     {{ csrf_field() }}
                        
                        
                        <div class="row">
                       
                          <div class="form-group col-md-4">
                            <label  class="required-field">As on Date</label>
                            
                            <span id="error_life_certificate_ason_date" class="text-info">@if($row->life_certificate_ason_date!='') {{date('d/m/Y', strtotime($row->life_certificate_ason_date)) }} @endif</span>
                          </div>
                         
                         
                       </div>
                       <center> 
                       <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="Back to Operator" id="nsap_marked" class="btn btn-info btn-lg btn-action" >
                        </div>
                        
                        <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="Verify" id="Verifysubmit" class="btn btn-success btn-lg btn-action" >
                        </div>

                       


                      
                      

                      </div>
                    
                    @endif

                      
               

                
  </div>

  
                         







                

                     
                   </div>


                       </div>
                 
                      


            </div>


          </div>
          
           
        </div>
</section>
@endsection
<form method="post" name="apprved_form" id="apprved_form" action="{{ route('SingleApproveLifeCertificate')}}" >
{{ csrf_field() }}
                      <input type="hidden" name="action_type" id="action_type" value="">
                      <input type="hidden" name="id" id="ben_id" value="{{$row->id}}">
                      <input type="hidden" name="scheme_id" id="scheme_id" value="{{$row->scheme_id}}">
<div class="modal" tabindex="-1" role="dialog" id="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     
      <div class="modal-body">
      <p>Are You Sure want to <b><span id="action_txt"></span></b>  the Beneficiary with ID <span id="id_txt" class="text-info"></span></p>
     
        
         
          
      </div>
      <div class="row">
                        <div class="col-md-12">
                        <input style="width:100%; padding: 2%; margin:1%;" type="text" name="comments" id="comments" class="form-control" placeholder="Comments" /> 
                        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="modal-submit">OK</button>
        <button type="button"  id="submitting1" value="Submit" class="btn btn-success success btn-lg" disabled >Submitting please wait</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</form>
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>

<script>
$(document).ready(function(){
  $("#submitting").hide();
  $("#submitting1").hide();
  $("#action_type").val('');
  $("#action_txt").text('');
  $("#id_txt").text('');
  $('.btn-action').click(function(){  
    $("#action_type").val('');
    $("#action_type").val($(this).val());
    //alert($("#action_type").val());
    $("#action_txt").text($(this).val());
    $("#id_txt").text($("#ben_id").val());
   
    $('#myModal').modal('show');
});
$('#modal-submit').on('click',function(){
 var action_type= $("#action_type").val();
   $("#modal-submit").hide();
   $("#submitting1").show();
   $("#apprved_form").submit();

});
});
function client_validation(){
  //alert('ok');
 
}
</script>
