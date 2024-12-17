<?php 
use Illuminate\Support\Facades\Input;
?>
@if ($scheme_id == 17)
    <div class="border">
        <div class="form-group col-md-12">
            <label class="">Current Address </label> <label>
                <input type="checkbox" class="cur_per_same" name="cur_per_same" id="cur_per_same" value="1" tabindex="12"
                    @if(old('cur_per_same') == 1) checked @endif> Same as Permanent Address
            </label>
        </div>
        <br />
        <div class="row address" id="cur_address">
            <div class="form-group col-md-4">
                <label class="">State</label>
                <input type="text" id="state_cur" name="state_cur" class="form-control" placeholder="" value="WEST BENGAL"
                    readonly="true" tabindex="13">
                <span id="error_state" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="">District</label>
                <select name="district_cur" id="district_cur" class="form-control" tabindex="14">
                    <option value="">--Select --</option>
                    @if ($type == $op_type)
                        @foreach($districts as $district)
                            <option value="{{ $district->district_code }}" @if($row->dist_code_cur == $district->district_code)
                            selected @endif>
                                {{ $district->district_name }}
                            </option>
                        @endforeach

                    @else
                            @foreach ($districts as $district)
                                    <option value="{{$district->district_code}}" @if(
                                        old('district_cur') == $district->
                                            district_code
                                    ) selected @endif> {{$district->district_name}}</option>
                            @endforeach
                    @endif

                </select>
                <span id="error_district_cur" class="text-danger"></span>

            </div>
            <div class="form-group col-md-4">
                <label class="">Assembly Constituency</label>
                <select name="asmb_cons_cur" id="asmb_cons_cur" class="form-control" tabindex="15">
                    @if ($type == $op_type)
                        @foreach ($assemly_list as $as_list)
                            <option value="{{ $as_list->ac_no }}" @if(isset($row->assembly_code_cur) && $as_list->ac_no == $row->assembly_code_cur) selected @endif>
                                {{ $as_list->ac_name }}
                            </option>
                        @endforeach
                    @else
                        <option value="">--Select--</option>
                    @endif
                </select>
                <span id="error_asmb_cons_cur" class="text-danger"></span>

            </div>
            <div class="form-group col-md-4" id="divUrbanCode">
                <label class="">Rural/ Urban</label>

                <select name="urban_code_cur" id="urban_code_cur" class="form-control" tabindex="16">
                    @if ($type == $op_type)
                        @foreach(Config::get('constants.rural_urban') as $key => $val)
                            <option value="{{ $key }}" @if(isset($row->rural_urban_id_cur) && $row->rural_urban_id_cur == $key)
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
                <span id="error_urban_code_cur" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4" id="divBodyCode">
                <label class="">Block/Municipality/Corp.</label>

                <select name="block_cur" id="block_cur" class="form-control" tabindex="17">
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
                <span id="error_block_cur" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4" id="divBodyCode">
                <label class="">GP/Ward No</label>

                <select name="gp_ward_cur" id="gp_ward_cur" class="form-control" tabindex="18">
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
                <span id="error_gp_ward_cur" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="">Village/Town/City</label>
                <input type="text" id="village_cur" name="village_cur" class="form-control special-char"
                    placeholder="Village/Town/City" maxlength="300" value="{{$type == $op_type ? $row->village_town_city_cur : old('village') }}" tabindex="19">
                <span id="error_village_cur" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="">House/Premise Number</label>
                <input type="text" id="house_cur" name="house_cur" class="form-control special-char"
                    placeholder="House/Premise No." maxlength="300" value="{{$type == $op_type ? $row->house_premise_no_cur : old('house') }}" tabindex="20">
                <span id="error_house_cur" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="">Post Office</label>
                <input type="text" id="post_office_cur" name="post_office_cur" class="form-control special-char"
                    placeholder="Post Office" maxlength="300" value="{{$type == $op_type ? $row->post_office_cur : old('post_office') }}" tabindex="21">
                <span id="error_post_office_cur" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="">Pin Code</label>
                <input type="text" id="pin_code_cur" name="pin_code_cur" class="form-control NumOnly" placeholder="Pin Code"
                    maxlength="6" value="{{$type == $op_type ? $row->pincode_cur : old('post_office') }}" tabindex="22">
                <span id="error_pin_code_cur" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="">Police Station</label>
                <input type="text" id="police_station_cur" name="police_station_cur" class="form-control special-char"
                    placeholder="Police Station" maxlength="200" value="{{$type == $op_type ? $row->police_station_cur : old('police_station') }}" tabindex="23">
                <span id="error_police_station_cur" class="text-danger"></span>
            </div>
        </div>
    </div>

    <div class="border">
        <div class="row">
            <div class="form-group col-md-4">
                <label>Number of years Dwelling in WB</label>
                <input type="text" id="residency_period" name="residency_period" class="form-control NumOnly" maxlength="3"
                    placeholder="Number of years Dwelling in WB" value="{{ $type == $op_type ? $row->residency_period :old('residency_period') }}" tabindex="24">
                <span id="error_residency_period" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="required-field">Mobile Number</label>
                <input type="text" id="mobile_no" name="mobile_no" class="form-control NumOnly" placeholder="Mobile No"
                    maxlength="10" value="{{$type == $op_type ? $row->mobile_no : old('mobile_no') }}" tabindex="25">
                <span id="error_mobile_no" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="">Email Id </label>
                <input type="text" id="email" name="email" class="form-control" placeholder="Email Id." maxlength="200"
                    value="{{$type == $op_type ? $row->email : old('email') }}" tabindex="26">
                <span id="error_email" class="text-danger"></span>
            </div>
        </div>
    </div>
@endif

<div class="col-md-12" align="center">
    <button type="button" name="previous_btn_contact_details" id="previous_btn_contact_details"
        class="btn btn-info btn-lg">Previous</button>
    <button type="button" name="btn_contact_details" id="btn_contact_details"
        class="btn btn-success btn-lg">Next</button>
</div>