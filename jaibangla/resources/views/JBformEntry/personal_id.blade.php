<div class="tab-pane fade" id="id_details">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><b>Personal Identification Number(S)</b></h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="form-group col-md-4">
                    <label
                        class="{{ $scheme_id == 1 || $scheme_id == 13 || $scheme_id == 17 || $scheme_id == 8 || $scheme_id == 9 ? 'required-field' : '' }}">
                        Digital Ration Card Number
                    </label>
                    <div class="row">
                        <div class="col-md-5">
                            <select class="form-control" name="ration_card_cat" id="ration_card_cat"
                                style="margin-left:-15px;">
                                @if ($type == $op_type)
                                    @foreach(Config::get('constants.ration_cat') as $key => $val)
                                        <option value="{{$key}}" @if($row->ration_card_cat == $key) selected @endif>{{$val}}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Category</option>
                                    @foreach(Config::get('constants.ration_cat') as $key => $val)
                                        <option value="{{ $key }}" @if(old('ration_card_cat') == $key) selected @endif>
                                            {{ $val }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="ration_card_no" id="ration_card_no" class="form-control NumOnly"
                                placeholder="Card Number" maxlength="10"
                                value="{{$type == $op_type ? $row->ration_card_no : old('ration_card_no') }}"
                                style="margin-left:-15px; margin-right:-15px;" />
                        </div>
                    </div>
                    <span id="error_ration_card_cat" class="text-danger"></span><br />
                    <span id="error_ration_card_no" class="text-danger"></span>

                </div>
                @if ($scheme_id != 2)
                    <div class="form-group col-md-4">
                        <label class="required-field">Aadhaar Number</label>
                        <input type="text" name="aadhar_no" id="aadhar_no" class="form-control NumOnly"
                            placeholder="Aadhar No." maxlength="12" value="{{$type == $op_type ? $row->aadhar_no : old('aadhar_no') }}" />
                        <span id="error_aadhar_no" class="text-danger"></span>
                    </div>
                @endif
                <!-- PAN Field -->
                @if (in_array($scheme_id, [1, 2, 3, 5, 6, 7, 10, 11, 13, 17, 19]))
                    <div class="form-group col-md-4">
                        <label class="">PAN</label>
                        <input type="text" name="pan_no" id="pan_no" class="form-control special-char" placeholder="PAN"
                            maxlength="10" value="{{$type == $op_type ? $row->pan_no : old('pan_no') }}" onkeyup="this.value = this.value.toUpperCase();" />
                        <span id="error_pan_no" class="text-danger"></span>
                    </div>
                @endif
            </div>
            <div class="row">
                <!-- EPIC/Voter ID -->
                @if($scheme_id != 2)
                    <div class="form-group col-md-4">
                        <label
                            class="{{ $scheme_id == 1 || $scheme_id == 3 || $scheme_id == 5 || $scheme_id == 10 || $scheme_id == 11 || $scheme_id == 13 || $scheme_id == 8 || $scheme_id == 9 ? 'required-field' : '' }}">EPIC/Voter
                            Id number</label>
                        <input type="text" name="epic_voter_id" id="epic_voter_id" class="form-control"
                            placeholder="EPIC/Voter Id.No." maxlength="20" value="{{$type == $op_type ? $row->epic_voter_id : old('epic_voter_id') }}" />
                        <span id="error_epic_voter_id" class="text-danger"></span>
                    </div>
                @endif

                @include('JBformEntry.personal_id_additional')
            </div>

        </div>
    </div>
</div>

<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>
<script src="{{ asset('js/FormEntry/personal_id.js') }}"></script>