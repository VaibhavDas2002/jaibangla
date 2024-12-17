@extends('update-ben-details.base1')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            @if (($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }} with Application ID: {{$id}}</strong>
                </div>
            @endif
            @if (($message = Session::get('message')))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
                </div>
            @endif            
            <div class="panel panel-default">
                <div class="panel-heading"><b>Update Bank Details</b></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('update-bank-details/'.$ben_detail->id) }}" name="editForm" onsubmit="return validate() && confirm('Are you sure?');">
                        {{ csrf_field() }}
                        
                        
                        <div class="form-group{{ $errors->has('bank_name') ? ' has-error' : '' }}">
                            <label for="bank_name" class="col-md-2 control-label">Bank Name</label>
                            <div class="col-md-4">
                                <input type="text" name="old_bank_name" id="old_bank_name" class="form-control" value="{{$ben_detail->bank_name}}" readonly>
                            </div>
                            <div class="col-md-1 text-primary">
                                <input type="checkbox" name="check_bank_name" id="check_bank_name" value="1" onchange="return fun_name();"><b>Same</b>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="bank_name" id="bank_name" required class="form-control" maxlength="200" onKeyUP="this.value = this.value.toUpperCase();" placeholder="Enter new bank name" tabindex="1">
                                @if ($errors->has('bank_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('bank_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('branch_name') ? ' has-error' : '' }}">
                            <label for="branch_name" class="col-md-2 control-label">Branch Name</label>

                            <div class="col-md-4">
                                <input type="text" name="old_branch_name" id="old_branch_name" class="form-control" value="{{$ben_detail->branch_name}}" readonly>
                            </div>
                            <div class="col-md-1 text-primary">
                                <input type="checkbox" name="check_branch_name" id="check_branch_name" value="1" onchange="return fun_branch();"><b>Same</b>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="branch_name" id="branch_name" required class="form-control" placeholder="Enter new branch name" maxlength="200" onKeyUP="this.value = this.value.toUpperCase();" tabindex="1">
                                @if ($errors->has('branch_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('branch_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('bank_ifsc') ? ' has-error' : '' }}">
                            <label for="bank_ifsc" class="col-md-2 control-label">IFSC Code</label>

                            <div class="col-md-4">
                                <input type="text" name="old_bank_ifsc" id="old_bank_ifsc" class="form-control" value="<?php print trim($ben_detail->bank_ifsc); ?>" readonly>
                            </div>
                            <div class="col-md-1 text-primary">
                                <input type="checkbox" name="check_bank_ifsc" id="check_bank_ifsc" value="1" onchange="return fun_ifsc();"><b>Same</b>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="bank_ifsc" id="bank_ifsc" required class="form-control" maxlength="11" placeholder="Enter new IFSC code" onKeyUP="this.value = this.value.toUpperCase();" tabindex="1" autocomplete="off">
                                @if ($errors->has('bank_ifsc'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('bank_ifsc') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('bank_code') ? ' has-error' : '' }}">
                            <label for="bank_code" class="col-md-2 control-label">Account No</label>

                            <div class="col-md-4">
                                <input type="text" name="old_bank_code" id="old_bank_code" class="form-control" value="<?php print trim($ben_detail->bank_code); ?>" readonly>
                            </div>
                            <div class="col-md-1 text-primary">
                                <input type="checkbox" name="check_bank_code" id="check_bank_code" value="1" onchange="return fun_code();"><b>Same</b>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="bank_code" id="bank_code" required class="form-control"  maxlength="20" placeholder="Enter new account number" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;" tabindex="1" autocomplete="off">
                                @if ($errors->has('bank_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('bank_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('remarks') ? ' has-error' : '' }}">
                            <label for="remarks" class="col-md-2 control-label">Remarks (max 100 characters)</label>

                            <div class="col-md-10">
                                <input type="text" name="remarks" id="remarks" required class="form-control"  maxlength="100" placeholder="Add remarks" tabindex="1">
                                @if ($errors->has('remarks'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('remarks') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-5">
                                <button type="submit" class="btn btn-primary" tabindex="1">
                                    Update
                                </button>
                            </div>
                        </div>
                        
                    </form>
                    <div class="text-success"><b>NOTE: The shaded boxes are old bank details of the beneficiary.</b></div>
                    <div class="text-primary"><b>NOTE: If you want to same as old details check the same checkbox.</b></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function validate(){
        if (document.editForm.bank_name.value == '') {
            alert('Please Enter the Bank Name');
            return false;
        }
        if (document.editForm.branch_name.value == '') {
            alert('Please Enter the Branch Name');
            return false;
        }
        if (document.editForm.bank_ifsc.value == '') {
            alert('Please Enter the IFSC Code');
            return false;
        }
        var ifsc = document.editForm.bank_ifsc.value;
        if (ifsc.length != 11) {
            alert('Please Provide Correct IFSC code(exactly 11 digits!)');
            return false;
        }
        if (document.editForm.bank_code.value == '') {
            alert('Please Enter the Account Number');
            return false;
        }
        if (document.editForm.remarks.value == '') {
            alert('Please add remarks');
            return false;
        }
        return true;
    }

    function fun_name() {
        if (document.getElementById('check_bank_name').checked) {
            document.getElementById('bank_name').value = document.getElementById('old_bank_name').value;
        }
        if (document.getElementById('check_bank_name').checked == false) {
            document.getElementById('bank_name').value = '';
        }
    }
    function fun_branch() {
        if (document.getElementById('check_branch_name').checked) {
            document.getElementById('branch_name').value = document.getElementById('old_branch_name').value;
        }
        if (document.getElementById('check_branch_name').checked == false) {
            document.getElementById('branch_name').value = '';
        }
    }
    function fun_ifsc() {
        if (document.getElementById('check_bank_ifsc').checked) {
            document.getElementById('bank_ifsc').value = document.getElementById('old_bank_ifsc').value;
        }
        if (document.getElementById('check_bank_ifsc').checked == false) {
            document.getElementById('bank_ifsc').value = '';
        }
    }
    function fun_code() {
        if (document.getElementById('check_bank_code').checked) {
            document.getElementById('bank_code').value = document.getElementById('old_bank_code').value;
        }
        if (document.getElementById('check_bank_code').checked == false) {
            document.getElementById('bank_code').value = '';
        }
    }
    

</script>
@endsection


