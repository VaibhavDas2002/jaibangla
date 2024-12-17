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
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Update Beneficiary <small>Stop Payment</small>
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      <div class="box box-default">
        <div class="box-body">
          <div class="panel panel-default">
                <div class="panel-heading"><b>Stop Payment Details</b></div>
                <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('stop-payment/'.$ben_detail->id) }}" name="editForm" onsubmit="return validate() && confirm('Are you sure?');" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        
                        <div class="form-group{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-2 control-label">ID</label>
                            <div class="col-md-6" >
                                <type="text" name="id" id="id" class="form-control" readonly>
                {{$ben_detail->id}}
                            </div>   
                        </div>
              
                        <div class="form-group{{ $errors->has('f_name') ? ' has-error' : '' }}">
                            <label for="f_name" class="col-md-2 control-label">First Name</label>
                            <div class="col-md-6" >
                                <type="text" name="f_name" id="f_name" class="form-control" readonly>
                {{$ben_detail->ben_fname}}
                            </div>
                            
                        </div>

                        <div class="form-group{{ $errors->has('m_name') ? ' has-error' : '' }}">
                            <label for="m_name" class="col-md-2 control-label">Middle Name</label>
                            <div class="col-md-6">
                                <type="text" name="m_name" id="old_bank_name" class="form-control" readonly>
                {{$ben_detail->ben_mname}}
                            </div>
                            
                        </div>

                        <div class="form-group{{ $errors->has('l_name') ? ' has-error' : '' }}">
                            <label for="l_name" class="col-md-2 control-label">Last Name</label>
                            <div class="col-md-6">
                                <type="text" name="l_name" id="l_name" class="form-control" readonly>
                {{$ben_detail->ben_lname}}
                            </div>
                           
                        </div>
                        
                        <div class="form-group{{ $errors->has('bank_name') ? ' has-error' : '' }}">
                            <label for="bank_name" class="col-md-2 control-label">Bank Name</label>
                            <div class="col-md-6">
                                <type="text" name="old_bank_name" id="old_bank_name" class="form-control" readonly>
                {{$ben_detail->bank_name}}
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('mobile_no') ? ' has-error' : '' }}">
                            <label for="bank_name" class="col-md-2 control-label">Mobile No</label>
                            <div class="col-md-6">
                                <type="text" name="mobile_no" id="mobile_no" class="form-control" readonly>
                {{$ben_detail->mobile_no}}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('branch_name') ? ' has-error' : '' }}">
                            <label for="branch_name" class="col-md-2 control-label">Branch Name</label>

                            <div class="col-md-6">
                                <type="text" name="old_branch_name" id="old_branch_name" class="form-control" readonly>
                {{$ben_detail->branch_name}}
                            </div>
                            
                            
                        </div>
                        <div class="form-group{{ $errors->has('bank_ifsc') ? ' has-error' : '' }}">
                            <label for="bank_ifsc" class="col-md-2 control-label">IFSC Code</label>

                            <div class="col-md-6">
                                <type="text" name="old_bank_ifsc" id="old_bank_ifsc" class="form-control" readonly>
                <?php print trim($ben_detail->bank_ifsc); ?>
                            </div>
                            
                           
                        </div>
                        <div class="form-group{{ $errors->has('bank_code') ? ' has-error' : '' }}">
                            <label for="bank_code" class="col-md-2 control-label">Account No</label>

                            <div class="col-md-6">
                                <type="text" name="old_bank_code" id="old_bank_code" class="form-control" readonly>
                <?php print trim($ben_detail->bank_code); ?>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('payment_count') ? ' has-error' : '' }}">
                            <label for="payment_count" class="col-md-2 control-label">Total Payment made</label>

                            <div class="col-md-6">
                                <type="text" name="payment_count" id="payment_count" class="form-control" readonly>
                <?php print trim($ben_detail->payment_count); ?>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('file_stop_payment') ? ' has-error' : '' }}">
                                 <label for="bank_code" class="col-md-2 control-label">Document Upload</br>(max size: 1024 KB)</label>
                                <div class="col-md-6 text-primary">
                                    <input type="file" name="file_stop_payment" class="form-control"  id="file_stop_payment">
            <br>
                                    <span id="umsg1"></span>
                                </div>
                                  @if ($errors->has('file_stop_payment'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('file_stop_payment') }}</strong>
                                    </span>
                                @endif
                         </div>

                         <div class="form-group{{ $errors->has('stop_payment_reason') ? ' has-error' : '' }}">
                            <label for="stop_payment_reason" class="col-md-2 control-label">Select Reason/Documents for Stop Payment</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="stop_payment_reason" id="stop_payment_reason" required class="form-control">
                                 <option value="0" selected>--Select--</option>
                                 @foreach($doc_type as $type)
                                    <option value="{{$type->id}}">{{$type->doc_name}}</option>
                                 @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('remarks') ? ' has-error' : '' }}">
                            <label for="remarks" class="col-md-2 control-label">Remarks</br>(max character: 100)</label>

                            <div class="col-md-6">
                                <textarea type="text" name="remarks" id="remarks" required class="form-control"  maxlength="100" placeholder="Add remarks" tabindex="1"></textarea>
                                @if ($errors->has('remarks'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('remarks') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-2">
                                <button type="submit" class="btn btn-primary" tabindex="1">
                                    Update
                                </button>
                            </div>
                        </div>
                        
                    </form>
                    <div class="text-success"><b>NOTE: Death Certificate is mandatory in case of Stop Payment due to Death.</b></div>
                    <div class="text-primary"><b>NOTE: Once Stop Payment is done, it can not be rolled back.</b></div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
  $(document).ready(function(){
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#file_stop_payment').change(function(){
        var card_file=document.getElementById("file_stop_payment");
        if(card_file.value!="")
      {
        var attachment;
        attachment = card_file.files[0];
            console.log(attachment.size)
        if(attachment.size>1048576)
        {
          document.getElementById("umsg1").innerHTML="<div class='alert-error'>Unaccepted document file size. Max size 1024 KB. Please try again</div>";
          $('#file_stop_payment').val('');
                return false;
        }
            else{
                document.getElementById("umsg1").innerHTML="";
            }
      }
    });
  });

  function validate(){
      if (document.editForm.stop_payment_reason.value == '100') {
        if (document.editForm.file_stop_payment.value == '') {
          alert('Please upload File for Stop Payment');
          return false;
        }
      }
      if (document.editForm.stop_payment_reason.value == 0) {
          alert('Please select Reason for Stop Payment');
          return false;
      }
      if (document.editForm.remarks.value == '') {
          alert('Please add Remarks');
          return false;
      }
      return true;
  }
</script>