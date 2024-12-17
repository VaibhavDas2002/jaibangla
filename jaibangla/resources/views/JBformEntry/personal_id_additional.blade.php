@if (!in_array($scheme_id, [17, 8, 9]))

    <!-- AHL TIN -->
    <div class="form-group col-md-4">
        <label class="">AHL TIN</label>
        <input type="text" name="ahl_tin" id="ahl_tin" class="form-control special-char" placeholder="AHL TIN"
            maxlength="90" value="{{$type == $op_type ? $row->ahl_tin : old('ahl_tin') }}" />
        <span id="error_ahl_tin" class="text-danger"></span>
    </div>

    <!-- BPL Seq Number -->
    <div class="form-group col-md-4">
        <label class="">BPL Seq Number (if available)</label>
        <input type="text" name="bpl_seq_no" id="bpl_seq_no" class="form-control special-char" placeholder="BPL Seq No."
            maxlength="12" value="{{$type == $op_type ? $row->bpl_seq_no : old('bpl_seq_no') }}" />
        <span id="error_bpl_seq_no" class="text-danger"></span>
    </div>


    <!-- BPL Id Number -->
    <div class="form-group col-md-4">
        <label class="">BPL Id Number (if available)</label>
        <input type="text" name="bpl_id_no" id="bpl_id_no" class="form-control special-char" placeholder="BPL Id No."
            maxlength="12" value="{{$type == $op_type ? $row->bpl_id_no : old('bpl_id_no') }}" />
        <span id="error_bpl_id_no" class="text-danger"></span>
    </div>

    <!-- BPL Total Score -->
    <div class="form-group col-md-4">
        <label class="">BPL Total Score (if available)</label>
        <input type="text" name="bpl_total_score" id="bpl_total_score" class="form-control NumOnly"
            placeholder="BPL Total Score" maxlength="6"
            value="{{$type == $op_type ? $row->bpl_total_score : old('bpl_total_score') }}" />
        <span id="bpl_total_score" class="text-danger"></span>
    </div>
    <div class="form-group col-md-4">&nbsp;</div>
@endif
<div class="row">
    <div class="col-md-12">
        <hr>
    </div>
    @if ($scheme_id == 2)
        <div class="form-group col-md-4">
            <label class="required-field">Applicant have the Aadhaar Number?</label>
            <select class="form-control" name="aadhar_exits" id="aadhar_exits">
                @php

                    if ($type == $op_type) {
                        $sel_aadhar_exits = $row->aadhar_exits;
                    } else {
                        $sel_aadhar_exits = old('aadhar_exits', 1);
                    }
                @endphp
                <option value="1" @if($sel_aadhar_exits == 1) selected @endif>Yes</option>
                <option value="0" @if($sel_aadhar_exits == 0) selected @endif>No</option>
            </select>
            <span id="error_aadhar_exits" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4" id="aadhar_div" @if($sel_aadhar_exits == 0) style="display:none" @endif>
            <label class="required-field">Aadhaar Number</label>
            <input type="text" name="aadhar_no" id="aadhar_no" class="form-control NumOnly" placeholder="Aadhaar No."
                maxlength="12" value="{{$type == $op_type ? $row->aadhar_no : old('aadhar_no') }}" tabindex="4" />
            <span id="error_aadhar_no" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4" id="withoutaadhar_div" @if($sel_aadhar_exits == 1) style="display:none" @endif>
            <label class="required-field">Reason for Which Aadhaar Cannot be Generated</label>
            <select class="form-control" name="withoutaadhar_cause" id="withoutaadhar_cause">
                @if($type == $op_type)
                    @foreach(Config::get('constants.withoutAadhaarreason') as $key => $val)
                        <option value="{{$key}}" @if($row->withoutaadhar_cause == $key) selected @endif>{{$val}}</option>
                    @endforeach
                @else
                    <option value="" selected>--Please Specify--</option>
                    @foreach(Config::get('constants.withoutAadhaarreason') as $key => $val)
                        <option value="{{$key}}" @if(old('withoutaadhar_cause') == $key) selected @endif>{{$val}}</option>
                    @endforeach
                @endif

            </select>
            <span id="error_withoutaadhar_cause" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4" id="withoutaadhar_cause_other_div" @if($sel_aadhar_exits == 1) style="display:none"
        @endif>
            <label class="required-field">Specify Other Reason</label>
            <input type="text" name="withoutaadhar_cause_other" id="withoutaadhar_cause_other" class="form-control"
                value="{{ $type == $op_type ? $row->withoutaadhar_cause : old('withoutaadhar_cause_other') }}" />
            <span id="error_withoutaadhar_cause_other" class="text-danger"></span>
        </div>
    @endif

</div>

<div class="row">
    <div class="col-md-12" align="center">
        <button type="button" name="previous_btn_id_details" id="previous_btn_id_details"
            class="btn btn-info btn-lg">Previous</button>
        <button type="button" name="btn_id_details" id="btn_id_details" class="btn btn-success btn-lg">Next</button>
    </div>
</div>