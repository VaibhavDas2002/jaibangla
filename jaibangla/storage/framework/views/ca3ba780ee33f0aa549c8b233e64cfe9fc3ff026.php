<?php $__env->startSection('action-content'); ?>
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
.required-field::after {
      content: "*";
      color: red;
}
@media  print {
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
             
            </div>
            <div class="modal-body">
            <a href="markdslist?type=<?php echo e($type); ?>&ds_mark_phase=<?php echo e($ds_mark_phase); ?>&scheme_id=<?php echo e($scheme_id); ?>"> 
                <img width="50px;" style="pull-right" src="<?php echo e(asset("images/back.png")); ?>" alt="Back" /></a>
                <div class="section1">
                  <div class="row">
                  <div class="col-md-12">
                    <h3 style="text-align: center; color:red;">Beneficiary ID:<?php echo e($row->id); ?>

                      
                      </h3>
                  </div>


                  </div>
                 
                       
                <div class="row color1">
                  <div class="col-md-12"><h3>Personal Details</h3></div>
                </div>
                <div class="row">
                    <div class="col-md-6" >
                      <div ><strong>Name :</strong> <?php echo e($row->ben_fname); ?> <?php echo e($row->ben_mname); ?> <?php echo e($row->ben_lname); ?></div>
                    </div>

                    

                      <!-- <img id="blah" src="<?php echo e(asset($row->passport_image)); ?>" alt=""  width="200px" height="200px" />

                       <img src="<?php echo e(url('storage/'.$row->passport_image)); ?>" alt="" title="" /> -->

                       <!--  <img src="<?php echo e(asset('upload/'.$row->passport_image)); ?>" alt="" width="200px" height="200px" /> -->
                    
               
                        <div class="col-md-6">
                            <div ><strong>Gender:</strong> <?php echo e(($row->gender=='Other') ? "Transgender" : $row->gender); ?> </div>
                            
                        </div>
                        
                        <?php if(!is_null($row->dob)): ?>
                        <div class="col-md-6">
                          <div ><strong>Date of Birth (DD-MM-YYYY):</strong> <?php echo e(date('d/m/Y', strtotime($row->dob))); ?></div>
                         
                        </div>
                        <?php endif; ?>

                      

                    


                    
                    <div class="col-md-6" >
                      <div ><strong>Father's Name :</strong> <?php echo e($row->father_fname); ?> <?php echo e($row->father_mname); ?> <?php echo e($row->father_lname); ?></div>
                    </div>

                    <div class="col-md-6" >
                      <div ><strong>Mother's Name :</strong> <?php echo e($row->mother_fname); ?> <?php echo e($row->mother_mname); ?> <?php echo e($row->mother_lname); ?></div>
                    </div>

                     
                    
                      
                        
                       
                      

                        <div class="col-md-6">
                          <div><strong>Caste:</strong> <?php echo e($row->caste); ?></div>
                        </div> 
                       
                       
                        <div class="col-md-6">
                          <div ><strong>Marital Status:</strong> <?php echo e($row->marital_status); ?></div>
                        </div>

                         <div class="col-md-6" >
                         <div ><strong>Spouse Name :</strong> <?php echo e($row->spouse_fname); ?> <?php echo e($row->spouse_mname); ?> <?php echo e($row->spouse_lname); ?></div>
                         </div>

                        <div class="col-md-6">
                          <div ><strong>Monthly Family Income(Rs.):</strong> <?php echo e($row->mothly_income); ?></div>
                        </div>                      
                     

                   
                     
                    
                      </div>
                    

                      <div class="row color1"  style="margin:10px 0px" >
                          <div class="col-md-12"><h3>Personal Identification Number(S)</h3></div>
                      </div>

                      <div class="col-md-6">
                        <div ><strong>Digital Ration Card No.:</strong> <?php echo e($row->ration_card_no); ?> </div>
                      </div>

                      <div class="col-md-6">
                        <div ><strong>AHL TIN: </strong><?php echo e($row->ahl_tin); ?></div>
                        </div>

                        <div class="col-md-6">
                        <div ><strong>Aadhaar No., if available:</strong> <?php echo e($row->aadhar_no); ?></div>
                        </div>

                        <div class="col-md-6">
                        <div ><strong>EPIC/Voter Id.No.: </strong> <?php echo e($row->epic_voter_id); ?></div>
                        
                        </div>

                        <div class="col-md-6">
                         <div ><strong>PAN, if available:</strong> <?php echo e($row->pan_no); ?></div>
                       
                        </div>

                        <div class="col-md-6">
                         <div ><strong>BPL Seq No., if avaiable:</strong>  <?php echo e($row->bpl_seq_no); ?></div>
                       
                        </div>

                        <div class="col-md-6">
                         <div ><strong>BPL Id No., if avaiable:</strong> <?php echo e($row->bpl_id_no); ?></div>
                       
                        </div>

                        <div class="col-md-6">
                         <div ><strong>BPL Total Score, if avaiable:</strong> <?php echo e($row->bpl_total_score); ?></div>
                       
                        </div>

                      <div class="row ">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Contact Details</h3></div>
                      </div>

                       <div class="col-md-6">
                         <div ><strong>State:</strong> West Bengal</div>
                       
                        </div>




                         <div class="col-md-6">
                         <div ><strong>Assembly Constitution:</strong>  <?php echo e($row->assembly_name); ?></div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>District:</strong>  <?php echo e($district_name); ?></div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Block/Municipality/Corp:</strong><?php echo e($row->block_name); ?></div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>GP/Ward No.:</strong><?php echo e($gp_name); ?></div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Village/Town/City:</strong> <?php echo e($row->village_town_city); ?></div>
                       
                        </div>



                         <div class="col-md-6">
                         <div ><strong>House/Premise No.:</strong>  <?php echo e($row->house_premise_no); ?></div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Post Office:</strong>  <?php echo e($row->post_office); ?></div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Pin Code:</strong>  <?php echo e($row->pincode); ?></div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>Police Station:</strong><?php echo e($row->police_station); ?></div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Mobile Number:</strong><?php echo e($row->mobile_no); ?></div>
                       
                        </div> 
                        <div class="col-md-6">
                         <div ><strong>Email Id., if available:</strong> <?php echo e($row->email); ?>

                            
                            
                           </div>

                        </div>



                         <div class="row ">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Bank Details</h3></div>
                      </div>

                       <div class="col-md-6">
                         <div ><strong>Bank Name:</strong>  <?php echo e($row->bank_name); ?></div>
                       
                        </div>




                         <div class="col-md-6">
                         <div ><strong>Bank Branch Name:</strong> <?php echo e($row->branch_name); ?></div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>Bank Account No.:</strong> <?php echo e($row->bank_code); ?></div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>IFS Code:</strong><?php echo e($row->bank_ifsc); ?></div>
                       
                        </div>

                      </div>

               
                <?php if(count($docs)>0): ?>
                <div class="row color1">
                  <div class="col-md-12"><h3>Enclosure List(Self Attested)</h3></div>
                </div>
                <div class="row">
                <?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if($doc->attched_document !=""): ?>
                  <div class="col-md-4"  >
                    <strong><?php echo e($doc->doc_type_name); ?> :</strong> 
                  </div>
                  <div class="col-md-8" style="padding-bottom: 30px; ">
                      <?php 
                        $document_mime_type = $doc->document_mime_type;
                        if($document_mime_type=='image/jpeg'){
                          $image_extension='jpg';
                        }else if($document_mime_type=='image/png'){
                           $image_extension='png';
                        }else if($document_mime_type=='application/pdf'){
                          $image_extension='pdf';
                        }
                        $row_image = "data:image/".$image_extension.";base64,".$doc->attched_document;
                       ?> 
                       <?php if(strtolower($image_extension)=='jpg' || strtolower($image_extension)=='png'): ?>
                       <div class="col-md-12" style="border:1px solid #dcdfdf">
                        <a class="example-image-link" href="<?php echo e($row_image); ?>" data-lightbox="example-1">
                        <img class="example-image" src="<?php echo e($row_image); ?>" alt="image-1" width="200" height="180" /></a>
                        </div>
                       
                        <?php elseif(strtolower($image_extension)=='pdf'): ?>
                        <div class="col-md-12" style="border:1px solid #dcdfdf">
                        <a id="link"  href="<?php echo e(route('jbDownload', ['scheme_id' => $doc->scheme_id,'created_by_dist_code' => $doc->created_by_dist_code,'beneficiary_id' => $doc->beneficiary_id,'document_type' => $doc->document_type])); ?>" target="_blank" style="color: #4324ef" width="">Download PDF Document</a>
                        </div>
                        <?php else: ?>
                        <div class="col-md-12" style="border:1px solid #dcdfdf">
                        <p>No File Found</p>
                        </div>
                        <?php endif; ?>     
                        

                  </div>
                  <?php endif; ?>         
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
               </div>
               <?php endif; ?>
               <?php 
             
                $visible=1;
                $btntext='Mark as CMO ENTRY';
              
             
               ?>
              <?php if($already_mark==0 && (intval($row->is_approved)==0)): ?>
                <div class="row">
                <form method="POST" action="<?php echo e(route('DsmarkPost')); ?>"  name="formReject" id="formReject">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" id="scheme_id" name="scheme_id" value="<?php echo e($row->scheme_id); ?>">
                 <input type="hidden" id="beneficiary_id" name="beneficiary_id" value="<?php echo e($row->id); ?>"/>
                 <input type="hidden" id="ds_mark_phase" name="ds_mark_phase" value="<?php echo e($ds_mark_phase); ?>"/>
                 <input type="hidden" id="type" name="type" value="<?php echo e($type); ?>"/>
                
                 <div class="row">
                  <div class="form-group col-md-12" >
                    <label class="">Camp Registration No.</label>
                    <input type="text" name="ds_registration_no"  autocomplete="off" id="ds_registration_no" class="form-control" placeholder="Registration No." maxlength="25"     />
                    <span id="error_ds_registration_no" class="text-danger"></span>
                   
                  
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12" >
                    <label class="">Camp Date</label>
                    <input type="date" name="ds_date" id="ds_date" class="form-control"
                    max="<?php echo date("Y-m-d"); ?>" value="" />
                   
                  
                  </div>
                </div>
                <button type="button"  class="btn btn-success success btn-lg" id="modal-submit" style="margin-top:20px;"><?php echo e($btntext); ?></button>
                        <button type="button" id="submitting" value="Submit" class="btn btn-danger btn-lg"
                          disabled style="display:none;">Submitting please wait</button>
                </div>
                
                
                </form>
                     
                   </div>
                  <?php endif; ?>

                       </div>
                 
                      


            </div>


          </div>
          
           
        </div>
</section>
<?php $__env->stopSection(); ?>
<script src="<?php echo e(asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
<script>
$(document).ready(function(){
  $(".NumOnly").keyup(function(event) {
              
              $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
    }); 
    
  $('#modal-submit').on('click',function(e){
    var ds_mark_phase=$('#ds_mark_phase').val();
           var pass_ds_registration_no=0;
           var pass_ds_date=0;
        var ds_registration_no=$('#ds_registration_no').val();
        if(ds_registration_no==''){
          alert('Please Enter Camp Registration No.');
          $("#ds_registration_no").focus();
          return false;
         }
         else{
          
          if($.trim($('#ds_registration_no').val()).length < 24)
          {
            alert('Please Enter Valid Camp Registration No.');
            $("#ds_registration_no").focus();
            return false;
          }
          else{
          var pass_ds_registration_no=1;
          }
         
      }
      var ds_date=$('#ds_date').val();
         if(ds_date==''){
          alert('Please Enter Camp Date.');
          $("#ds_date").focus();
          return false;
         }
         else{
          
          var pass_ds_date=1;
          
         }
      
   
   if(pass_ds_registration_no==1 && pass_ds_date==1){
    $("#modal-submit").hide();
    $("#submitting").show();
    $("#submit_loader").show();
    $("#formReject").submit();
   }
       
    //$("#register_form").submit();
});
});
</script>
<!-- <script>
function printfunction() {
  // var content=document.getElementById('divToPrint');
  // window.document.write('<html><head><style>.row{ margin-right: 0px!important; margin-left: 0px!important; margin-top: 1%!important;}.section1{border:1.5pxsolid#9187878c;margin:2%;padding:2%;}.color1{margin:0%!important;background-color: #5f9ea061;}.modal_field_name{ float:left;font-weight: 700;margin-right:1%;padding-top:1%;margin-top:1%;}.modal_field_value{margin-right:1%;padding-top:1%;margin-top:1%;}</style></head><body>'+content.innerHTML+'</body></html>');
  window.print();
}
</script> -->

<?php echo $__env->make('markds.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>