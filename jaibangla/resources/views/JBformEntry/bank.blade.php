<div class="tab-pane fade" id="bank_details">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><b>Bank Account Details</b></h4>
        </div>
        <div class="panel-body">

            <div class="form-group col-md-6">
                <label class="required-field">IFS Code</label>
                <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control special-char"
                    placeholder="IFSC Code" onkeyup="this.value = this.value.toUpperCase();"
                    value="{{$type == $op_type ? $row->bank_ifsc : old('bank_ifsc_code') }}" maxlength='11' tabindex="4"
                    @if(in_array('first_name', $readonly)) readonly @endif />
                <span id="error_bank_ifsc_code" class="text-danger"></span>
            </div>

            <div class="form-group col-md-6">
                <label class="required-field">Bank Name</label>
                <input type="text" name="name_of_bank" id="name_of_bank" class="form-control special-char"
                    placeholder="Bank Name" value="{{$type == $op_type ? $row->bank_name : old('name_of_bank') }}"
                    maxlength="200" tabindex="1" readonly @if(in_array('first_name', $readonly)) readonly @endif />
                <span id="error_name_of_bank" class="text-danger"></span>
            </div>



            <div class="form-group col-md-6">
                <label class="required-field">Bank Branch Name</label>
                <input type="text" name="bank_branch" id="bank_branch" class="form-control"
                    placeholder="Bank Branch Name"
                    value="{{$type == $op_type ? $row->branch_name : old('bank_branch') }}" maxlength="300" tabindex="2"
                    readonly @if(in_array('first_name', $readonly)) readonly @endif />
                <span id="error_bank_branch" class="text-danger"></span>
            </div>

            <div class="form-group col-md-6">
                <label class="required-field">Bank Account Number</label>
                <input type="text" name="bank_account_number" id="bank_account_number" class="form-control NumOnly"
                    placeholder="Bank Account No"
                    value="{{$type == $op_type ? $row->bank_code : old('bank_account_number') }}" maxlength='16'
                    tabindex="3" @if(in_array('first_name', $readonly)) readonly @endif />
                <span id="error_bank_account_number" class="text-danger"></span>

            </div>

            <div class="form-group col-md-6">
                <label class="required-field">Confirm Bank Account Number</label>
                <input type="text" name="confirm_bank_account_number" id="confirm_bank_account_number"
                    class="form-control NumOnly" placeholder="Confirm Bank Account No"
                    value="{{$type == $op_type ? $row->bank_code : old('bank_account_number_confirm') }}" maxlength='16'
                    tabindex="3" @if(in_array('first_name', $readonly)) readonly @endif />
                <span id="error_confirm_bank_account_number" class="text-danger"></span>
            </div>


            <br />

            <div class="col-md-12" align="center">
                <button type="button" name="previous_btn_bank_details" id="previous_btn_bank_details"
                    class="btn btn-info btn-lg">Previous</button>
                <button type="button" name="btn_bank_details" id="btn_bank_details"
                    class="btn btn-success btn-lg">Next</button>
            </div>
            <br />
        </div>
    </div>
</div>


<script src="{{ asset('js/FormEntry/bank.js') }}"></script>