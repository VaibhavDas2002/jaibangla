@extends('Lokkhibhandar60.base')
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
               <h2 class="modal-title " style="text-align: center;">View Application Form</h2>
               <a href="{{ route('workflow-lb60-Ben-List', ['scheme_id'=>$row->scheme_id])}}">
                <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a>
            </div>
            <div class="modal-body">
              <div class='row'>
            <div>
             @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }} with Beneficiary ID: {{$id}}</strong>
                  

                </div>
                @endif
            @if ( ($error = Session::get('error')))
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $error }}</strong>
                  

                </div>
      @endif
           @if(count($errors) > 0)
      <div class="alert alert-danger alert-block">
        <ul>
          @foreach($errors as $error)
          <li><strong> {{ $error }}</strong></li>
          @endforeach
        </ul>
      </div>
      @endif
            </div>
                

                <!-- We display the details entered by the user here -->
                <div class="section1">
                  <div class="row">
                  <div class="col-md-12">
                    <h3 style="text-align: center; color:red;">LB Application ID:{{$row->lb_application_id}}
                      
                      </h3>
                  </div>


                  </div>
                       
                <div class="row color1">
                  <div class="col-md-12"><h3>Personal Details</h3></div>
                </div>
                <div class="row">
                    <div class="col-md-6" >
                      <div ><strong>Name :</strong> {{$row->ben_full_name}}</div>
                    </div>

                    

                     
                    
               
                        <div class="col-md-6">
                            <div ><strong>Gender:</strong> {{ ($row->gender=='Other') ? "Transgender" : $row->gender }} </div>
                            
                        </div>
                        
                        @if(!is_null($row->dob))
                        <div class="col-md-6">
                          <div ><strong>Date of Birth (DD-MM-YYYY):</strong> {{date('d/m/Y', strtotime($row->dob)) }}</div>
                         
                        </div>
                        @endif

                        

                    


                    
                    <div class="col-md-6" >
                      <div ><strong>Father's Name :</strong> {{$row->father_fname}} {{$row->father_mname}} {{$row->father_lname}}</div>
                    </div>

                    <div class="col-md-6" >
                      <div ><strong>Mother's Name :</strong> {{$row->mother_fname}} {{$row->mother_mname}} {{$row->mother_lname}}</div>
                    </div>

                     
                    
                      
                        
                       
                      

                        <div class="col-md-6">
                          <div><strong>Caste:</strong> {{$row->caste}}</div>
                        </div> 
                       
                       
                        <div class="col-md-6">
                          <div ><strong>Marital Status:</strong> {{$row->marital_status}}</div>
                        </div>

                         <div class="col-md-6" >
                         <div ><strong>Spouse Name :</strong> {{$row->spouse_fname}} {{$row->spouse_mname}} {{$row->spouse_lname}}</div>
                         </div>

                        <div class="col-md-6">
                          <div ><strong>Monthly Family Income(Rs.):</strong> {{$row->mothly_income}}</div>
                        </div>                      
                     

                   
                     
                    
                      </div>
                    

                      



                      <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Bank Details</h3></div>
                      </div>

                       <div class="col-md-6">
                         <div ><strong>Bank Name:</strong>  {{$row->bank_name}}</div>
                       
                        </div>




                         <div class="col-md-6">
                         <div ><strong>Bank Branch Name:</strong> {{$row->branch_name}}</div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>Bank Account No.:</strong> {{$row->bank_code}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>IFS Code:</strong>{{$row->bank_ifsc}}</div>
                       
                        </div>
                       
                     @if(count($docs)>0)
                     @foreach($docs as $doc_item)
                        <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>{{$doc_item['type_des']}}</h3></div>
                        </div>
                        <div class="row">
                          
                          <?php 
                          $data = $doc_item['content'];
                          $ext = $doc_item['extension'];
                          ?> 
                           @if(strtolower($ext)=='pdf')
                            <div class="col-md-12" style="border:1px solid #dcdfdf">
                          
                            <a id="link" href="#" target="_blank" width="">Download PDF Document</a>
                          </div>
                        
                          @else

                          <div class="col-md-12" style="border:1px solid #dcdfdf">
                          <a class="example-image-link" href="data:image/{{$doc_item['extension']}};base64,{{$doc_item['content']}}" data-lightbox="example-1">
                          <img class="example-image" src="data:image/{{$doc_item['extension']}};base64,{{$doc_item['content']}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                         
                         @endif           
                         
                        </div>
                        @endforeach
                        @endif
                     
                      <br/> <br/> <br/> <br/>
                       <!-- <center> 
                       <button type="button" id="submit" value="Submit"
                          class="btn btn-success success btn-lg modal-submit">
                          @if($designation_id=='Verifier' && $row->next_level_role_id==1) Import & Verify @endif @if($designation_id=='Approver' && $row->next_level_role_id==2) Approve @endif</button>
                       
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button type="button" id="reject" value="reject"
                          class="btn btn-danger btn-lg modal-submit">
                          @if($row->next_level_role_id==1 || $row->next_level_role_id==2) Reject @endif</button>
                       
                        </center> -->
                      

                      </div>
                  

                      
                <div class="row">
                   
               </div>

                
  </div>

  
                         







                

                     
                   </div>


                       </div>
                 
                      


            </div>


          </div>
          
           
        </div>
</section>
@endsection
<div id="modalReject" class="modal fade">
  <form method="POST" action="{{url('lbapplicationVerify')}}"  name="verifyReject" id="verifyReject">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="designation_id" id="designation_id" value="{{$designation_id}}"/>
  <input type="hidden" name="action_type" id="action_type" />
  <input type="hidden" name="scheme_id" id="scheme_id" value="{{$row->scheme_id}}"/>
  <input type="hidden" name="id" id="id" value="{{$row->lb_application_id}}"/>
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
				<h4 class="modal-title w-100">Do you really want to <span class="verify_reject">Reject</span>  the application(<span id="application_text_approve">{{$row->lb_application_id}}</span>)?</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p></p>
       
         
			</div>
			<div class="modal-footer justify-content-center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-info modal-submitapprove" ><span class="verify_reject">Reject</span></button>
         <button type="button" id="submittingapprove" value="Submit" class="btn btn-success success btn-lg"
                          disabled>Submitting please wait</button>
			</div>
		</div>
	</div>
  </form>
</div>
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>

<script type="text/javascript">
$(document).ready(function(){
  $("#submittingapprove").hide();
  $('#submit').click(function(){
      
      var designation_id=$("#designation_id").val();
      $("#action_type").val('');
      if(designation_id=='Verifier'){
        $("#action_type").val(2);
        $('.verify_reject').text('Import & Verify');
      }
      if(designation_id=='Approver'){
        $("#action_type").val(3);
        $('.verify_reject').text('Approve');
      }
      $('#modalReject').modal();
    
       
    });
    $('#reject').click(function(){
      $('.verify_reject').text('Reject');
      $("#action_type").val(4);
      $('#modalReject').modal();
    
       
    });
    $('.modal-submitapprove').on('click',function(){
        $(".modal-submitapprove").hide();
        $("#submittingapprove").show();
        $("#verifyReject").submit();
        
       
      });
});

</script>
