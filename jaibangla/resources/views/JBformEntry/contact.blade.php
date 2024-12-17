<div class="tab-pane fade" id="contact_details">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><b>Contact Details</b></h4>
        </div>
        <div class="panel-body">
            <div class="border">
                <div class="row">
                    <div class="form-group col-md-12 ajax_loader" style="display:none;">
                        <img src="{{asset('images/ZKZg.gif')}}" />
                    </div>
                    <div class="form-group col-md-4">
                        <label class="required-field">State</label>
                        <input type="text" id="state" name="state" class="form-control" placeholder=""
                            value="WEST BENGAL" readonly="true">
                        <span id="error_state" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="required-field">District</label>
                        <select name="district" id="district" class="form-control client-js-district">
                            @if ($type == $op_type)
                                @foreach($districts as $district)
                                    <option value="{{ $district->district_code }}"
                                        @if($row->dist_code == $district->district_code) selected @endif>
                                        {{ $district->district_name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">--Select--</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->district_code }}"
                                        @if(old('district') == $district->district_code) selected @endif>
                                        {{ $district->district_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <span id="error_district" class="text-danger"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label class="required-field">Assembly Constituency</label>
                        <select name="asmb_cons" id="asmb_cons" class="form-control  client-js-assembly">
                            @if ($type == $op_type)
                                @foreach ($assemly_list as $as_list)
                                    <option value="{{ $as_list->ac_no }}" @if(isset($row->assembly_code) && $as_list->ac_no == $row->assembly_code) selected @endif>
                                        {{ $as_list->ac_name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">--Select--</option>
                            @endif
                        </select>
                        <span id="error_asmb_cons" class="text-danger"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4" id="divUrbanCode">
                        <label class="required-field">Rural/ Urban</label>
                        <select name="urban_code" id="urban_code" class="form-control client-js-urban">
                            @if ($type == $op_type)
                                @foreach(Config::get('constants.rural_urban') as $key => $val)
                                    <option value="{{ $key }}" @if(isset($row->rural_urban_id) && $row->rural_urban_id == $key)
                                    selected @endif>
                                        {{ $val }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">--Select--</option>
                                @foreach(Config::get('constants.rural_urban') as $key => $val)
                                    <option value="{{ $key }}" @if(old('urban_code') == $key) selected @endif>
                                        {{ $val }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <span id="error_urban_code" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-4" id="divBodyCode">
                        <label class="required-field">Block/Municipality/Corp.</label>
                        <select name="block" id="block" class="form-control  client-js-localbody">
                            @if ($type == $op_type)
                                @foreach ($block_munc_list as $blk_list)
                                    <option value="{{ $blk_list->code }}" @if(isset($row->block_ulb_code) && $blk_list->code == $row->block_ulb_code) selected @endif>
                                        {{ $blk_list->val }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">--Select--</option>
                            @endif
                        </select>
                        <span id="error_block" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-4" id="divBodyCode">
                        <label class="required-field">GP/Ward No</label>
                        <select name="gp_ward" id="gp_ward" class="form-control  client-js-gpward">
                            @if ($type == $op_type)
                                @foreach ($gp_ward_list as $gp_list)
                                    <option value="{{ $gp_list->code }}" @if(isset($row->gp_ward_code) && $gp_list->code == $row->gp_ward_code) selected @endif>
                                        {{ $gp_list->val }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">--Select--</option>

                            @endif
                        </select>
                        <span id="error_gp_ward" class="text-danger"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="required-field">Village/Town/City</label>
                        <input type="text" id="village" name="village" class="form-control special-char"
                                placeholder="Village/Town/City" maxlength="300" value="{{$type == $op_type ? $row->village_town_city : old('village') }}">
                        <span id="error_village" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="">House/Premise Number</label>
                        <input type="text" id="house" name="house" class="form-control special-char"
                        placeholder="House/Premise No." maxlength="300" value="{{$type == $op_type ? $row->house_premise_no : old('house') }}">
                        <span id="error_house" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="required-field">Post Office</label>
                        <input type="text" id="post_office" name="post_office" class="form-control special-char"
                                placeholder="Post Office" maxlength="300" value="{{$type == $op_type ? $row->post_office : old('post_office') }}">
                        <span id="error_post_office" class="text-danger"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="required-field">Pin Code</label>
                        <input type="text" id="pin_code" name="pin_code" class="form-control NumOnly"
                        placeholder="Pin Code" maxlength="6" value="{{$type == $op_type ? $row->pincode : old('pin_code') }}">
                        <span id="error_pin_code" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="required-field">Police Station</label>
                        <input type="text" id="police_station" name="police_station"
                                class="form-control special-char" placeholder="Police Station" maxlength="200"
                                value="{{$type == $op_type ? $row->police_station : old('police_station') }}">
                        <span id="error_police_station" class="text-danger"></span>
                    </div>
                    @if($scheme_id != 17)
                        <div class="form-group col-md-4">
                            <label class="required-field">Number of years Dwelling in WB</label>
                            <input type="text" id="residency_period" name="residency_period"
                                    class="form-control NumOnly" maxlength="3" placeholder="Number of years Dwelling in WB"
                                    value="{{ $type == $op_type ? $row->residency_period :old('residency_period') }}">
                            <span id="error_residency_period" class="text-danger"></span>
                        </div>
                    @endif
                </div>
                <div class="row">
                    @if($scheme_id != 17)
                        <div class="form-group col-md-4">
                            <label class="required-field">Mobile Number</label>
                            <input type="text" id="mobile_no" name="mobile_no" class="form-control NumOnly"
                                    placeholder="Mobile No" maxlength="10" value="{{$type == $op_type ? $row->mobile_no : old('mobile_no') }}">
                            <span id="error_mobile_no" class="text-danger"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="">Email Id </label>
                            <input type="text" id="email" name="email" class="form-control" placeholder="Email Id."
                            maxlength="200" value="{{$type == $op_type ? $row->email : old('email') }}">
                            <span id="error_email" class="text-danger"></span>
                        </div>
                    @endif
                </div>
                <hr>
                <br />
                <br /> <br />
            </div>
            @include('JBformEntry.contact_additional')

        </div>
    </div>
</div>

<script src="{{ asset('js/FormEntry/contact.js') }}"></script>