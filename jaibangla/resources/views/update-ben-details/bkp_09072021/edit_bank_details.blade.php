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
        Update Beneficiary <small>Update Bank Details with Mobile No</small>
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      <div class="box">
          <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <!-- @if (($message = Session::get('success')) && ($id =Session::get('id')))
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
                    @endif -->

                    @if(isset($lot_msg))
                        <div class="alert alert-danger alert-block">
                            <!-- <button type="button" class="close" data-dismiss="alert">×</button>  -->
                            <strong>{{ $lot_msg }}</strong>
                        </div>
                    @endif

                    @if(!isset($lot_msg))            
                    <div class="panel panel-default">
                        <div class="panel-heading"><font size="3">Update Bank Details Beneficiary Id - <b>{{$ben_detail->id}}</b></font></div>
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
                                <div class="form-group{{ $errors->has('mobile_no') ? ' has-error' : '' }}">
                                    <label for="mobile_no" class="col-md-2 control-label">Mobile No</label>

                                    <div class="col-md-4">
                                        <input type="text" name="old_mobile_no" id="old_mobile_no" class="form-control" value="<?php print trim($ben_detail->mobile_no); ?>" readonly>
                                    </div>
                                    <div class="col-md-1 text-primary">
                                        <input type="checkbox" name="check_mobile_no" id="check_mobile_no" value="1" onchange="return fun_mobile();"><b>Same</b>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="mobile_no" id="mobile_no" required class="form-control"  maxlength="10" placeholder="Enter new mobile number" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;" tabindex="1" autocomplete="off">
                                        @if ($errors->has('mobile_no'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('mobile_no') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('remarks') ? ' has-error' : '' }}">
                                    <label for="remarks" class="col-md-2 control-label">Remarks</label>

                                    <div class="col-md-10">
                                        <input type="text" name="remarks" id="remarks" required class="form-control"  maxlength="300" placeholder="Add remarks" tabindex="1">
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
                    @endif

                </div>
            </div>
          </div>
        </div>
    </section>
    <!-- /.content -->
</div>
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
  });

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
        if (document.editForm.mobile_no.value == '') {
            alert('Please Enter the mobile no');
            return false;
        }
        if (document.editForm.remarks.value == '') {
            alert('Please add remarks');
            return false;
        }
        var new_acc = document.editForm.bank_code.value;
        var old_acc = document.editForm.old_bank_code.value;
        var new_ifsc = document.editForm.bank_ifsc.value;
        var old_ifsc = document.editForm.old_bank_ifsc.value;
        if ((new_acc == old_acc) && (new_ifsc == old_ifsc)) {
            $.alert({
                title : 'Alert',
                type : 'red',
                icon : 'fa fa-warning',
                content : 'Bank A/c no same as previous one. Please enter new A/c no.'
            });
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
    function fun_mobile() {
        if (document.getElementById('check_mobile_no').checked) {
            document.getElementById('mobile_no').value = document.getElementById('old_mobile_no').value;
        }
        if (document.getElementById('check_mobile_no').checked == false) {
            document.getElementById('mobile_no').value = '';
        }
    }
    

</script>