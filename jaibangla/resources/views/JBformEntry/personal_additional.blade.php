<div class="row">
    <div class="form-group col-md-12">
        <hr>
    </div>
    @if($scheme_id == 2)
        <div class="form-group col-md-4">
            <label class="required-field">Type of Disability</label>
            <select class="form-control" name="disablity_type" id="disablity_type">
                @if ($type == $op_type)
                    @foreach(Config::get('constants.disablity_type') as $key => $val)
                        <option value="{{ $key }}" @if($row->type_disability == $key) selected @endif>
                            {{ $val }}
                        </option>
                    @endforeach
                @else

                    <option value="">--Select--</option>
                    @foreach(Config::get('constants.disablity_type') as $key => $val)
                        <option value="{{$key}}" @if(old('disablity_type') == $key) selected @endif>{{$val}}</option>
                    @endforeach
                @endif
            </select>
            <span id="error_disablity_type" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4">
            <label class="required-field">Percentage of Disablity</label>
            <input type="text" name="disablity_type_percentage" id="disablity_type_percentage" class="form-control "
                placeholder="Percentage" maxlength="5"
                value="{{ $type == $op_type ? $row->percentage_disability : old('disablity_type_percentage') }}" />
            <span id="error_disablity_type_percentage" class="text-danger"></span>

        </div>
        <div class="form-group col-md-4">
            <label class="required-field">Authority Name</label>
            <input type="text" name="disablity_type_authority" id="disablity_type_authority" class="form-control txtOnly"
                placeholder="Certifying Authority" maxlength="200"
                value="{{$type == $op_type ? $row->certifying_auth : old('disablity_type_authority') }}" />
            <span id="error_disablity_type_authority" class="text-danger"></span>

        </div>
        <div class="form-group col-md-4">
            <label class="required-field">Authority Designation</label>
            <input type="text" name="disability_designation" id="disability_designation" class="form-control txtOnly"
                placeholder="Designation Name" maxlength="200"
                value="{{$type == $op_type ? $row->disability_designation : old('disability_designation') }}" />
            <span id="error_disability_designation" class="text-danger"></span>
        </div>
    @endif

    @if ($scheme_id == 5)
        <div class="form-group col-md-4">
            <label>Belongs to Fisherman Community</label>
            <select class="form-control" name="fisherman_comm" id="fisherman_comm" tabindex="14">
                @if ($type == $op_type)
                    <option value="YES" @if($row->fisherman_comm == "YES") selected @endif>Yes</option>
                    <option value="NO" @if($row->fisherman_comm == "NO") selected @endif>No</option>
                @else

                    <option value="">--Select--</option>
                    <option value="YES" @if(old('fisherman_comm') == $key) @endif>Yes</option>
                    <option value="NO" @if(old('fisherman_comm') == $key) @endif>No</option>
                @endif

            </select>
            <span id="error_fisherman_comm" class="text-danger"></span>
        </div>

        <div class="form-group col-md-4">
            <label class="required-field">Physically Handicapped </label>
            <select class="form-control" name="phy_hadi_status" id="phy_hadi_status" tabindex="15">
                @if ($type == $op_type)
                    <option value="No" @if($row->phy_hadi_status == "No") selected @endif>No</option>
                    <option value="Yes" @if($row->phy_hadi_status == "Yes") selected @endif>Yes</option>

                @else
                    <option value="No" @if(old('phy_hadi_status') == 'No') selected @endif>No</option>
                    <option value="Yes" @if(old('phy_hadi_status') == 'Yes') @endif>Yes</option>
                @endif
            </select>
            <span id="error_phy_hadi_status" class="text-danger"></span>
        </div>
    @endif

    @if ($scheme_id == 17)

        <div class="form-group col-md-4">
            <label class="required-field">Select Application Phase</label>
            <select class="form-control" name="app_phase" id="app_phase">
                @if ($type == $op_type)
                    @foreach(Config::get('constants.purohit_phase') as $key => $val)
                        <option value="{{ $key }}" @if($row->app_phase == $key) selected @endif>
                            {{ $val }}
                        </option>
                    @endforeach
                @else
                    <option value="">--Select--</option>
                    @foreach(Config::get('constants.purohit_phase') as $key => $val)
                        <option value="{{$key}}" @if(old('app_phase') == $key) selected @endif>{{$val}}</option>
                    @endforeach
                @endif
            </select>
            <span id="error_app_phase" class="text-danger"></span>
        </div>

        <div class="form-group col-md-4">
            <label class="required-field">Temple Type</label>
            <select class="form-control" name="temple_type" id="temple_type">
                @if ($type == $op_type)

                    <option value='Temple Purohit' @if($row->temple_type == 'Temple Purohit') selected @endif>Temple Purohit
                    </option>
                    <option value='Tribal Religious Place Purohit' @if($row->temple_type == 'Tribal Religious Place Purohit')
                    selected @endif>Tribal Religious Place Purohit</option>
                    <option value='Community Purohit' @if($row->temple_type == 'Community Purohit') selected @endif>Community
                        Purohit</option>
                @else

                    <option value="">--Select--</option>
                    <option value='Temple Purohit' @if(old('temple_type') == 'Temple Purohit') selected @endif>Temple Purohit
                    </option>
                    <option value='Tribal Religious Place Purohit' @if(old('temple_type') == 'Tribal Religious Place Purohit')
                    selected @endif>Tribal Religious Place Purohit</option>
                    <option value='Community Purohit' @if(old('temple_type') == 'Community Purohit') selected @endif>Community
                        Purohit</option>
                @endif
            </select>
            <span id="error_temple_type" class="text-danger"></span>
        </div>
    @endif
    @if ($scheme_id == 11)
        <div class="form-group col-md-12">
            <label class="">Husband's Name</label>
        </div>

        <div class="form-group col-md-4">
            <label class="required-field">First Name</label>
            <input type="text" name="husband_first_name" id="husband_first_name" class="form-control txtOnly"
                placeholder="First Name" maxlength="200" value="{{$type == $op_type ? $row->husband_fname : old('husband_first_name') }}" tabindex="4" />
            <span id="error_husband_first_name" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4">
            <label>Middle Name</label>
            <input type="text" name="husband_middle_name" id="husband_middle_name" class="form-control txtOnly"
                placeholder="Middle Name" maxlength="100" value="{{$type == $op_type ? $row->husband_mname : old('husband_middle_name') }}" tabindex="5" />
            <span id="error_husband_middle_name" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4">
            <label class="required-field">Last Name</label>
            <input type="text" name="husband_last_name" id="husband_last_name" class="form-control txtOnly"
                placeholder="Last Name" maxlength="200" value="{{$type == $op_type ? $row->husband_lname : old('husband_last_name') }}" tabindex="6" />
            <span id="error_husband_last_name" class="text-danger"></span>
        </div>
    @endif
</div>