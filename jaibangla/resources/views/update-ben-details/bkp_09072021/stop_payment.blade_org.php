@extends('update-ben-details.base1')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
                      
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
                                <input type="text" name="m_name" id="old_bank_name" class="form-control" readonly>
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

                        <div class="form-group{{ $errors->has('file_stop_payment') ? ' has-error' : '' }}">
                                 <label for="bank_code" class="col-md-2 control-label">Document Upload</label>
                                <div class="col-md-6 text-primary">
                                    <input type="file" name="file_stop_payment" class="form-control">
                                </div>
                                  @if ($errors->has('file_stop_payment'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('file_stop_payment') }}</strong>
                                    </span>
                                @endif
                         </div>

                         <div class="form-group{{ $errors->has('stop_payment_reason') ? ' has-error' : '' }}">
                            <label for="stop_payment_reason" class="col-md-2 control-label">Select Reason for Stop Payment</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="stop_payment_reason" id="stop_payment_reason" required class="form-control">
                                 <option value="0" selected>--Select--</option>
                                 <option value="death">Death</option>
                                 <option value="non_eligibility">Non Eligibility</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('remarks') ? ' has-error' : '' }}">
                            <label for="remarks" class="col-md-2 control-label">Remarks</label>

                            <div class="col-md-6">
                                <textarea type="text" name="remarks" id="remarks" required class="form-control"  maxlength="300" placeholder="Add remarks" tabindex="1"></textarea>
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
<script>
    function validate(){
        if (document.editForm.stop_payment_reason.value == 'death') {
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
@endsection


