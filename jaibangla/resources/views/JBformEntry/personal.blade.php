<div class="tab-pane active" id="personal_details">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><b>Personal Details</b></h4>
        </div>
        <div class="panel-body">
            <div class="form-group col-md-12">
                <label class="required-field"><b>Application Type: </b></label>
            </div>
            <div class="form-group col-md-4 ">
                <select class="form-control" name="entry_type" id="entry_type" @if(in_array('entry_type',   $readonly)) readonly @endif>
                    @if ($type == $op_type)

                        <option value="Normal" @if($type == $op_type && isset($row->entry_type) && $row->entry_type == "Normal")
                        selected @endif>
                            Normal Entry
                        </option>
                        <option value="Form through Duare Sarkar camp" @if($type == $op_type && isset($row->entry_type) && $row->entry_type == "Form through Duare Sarkar camp") selected @endif>
                            Form through Duare Sarkar camp
                        </option>
                    @else
                        @if($ds_allow && $normal_entry)
                            <option value="Normal">Normal Entry</option>
                            <option value="Form through Duare Sarkar camp" selected>Form through Duare Sarkar
                                camp</option>
                        @elseif($ds_allow && !$normal_entry)
                            <option value="Form through Duare Sarkar camp" selected>Form through Duare Sarkar
                                camp</option>
                        @else
                            <option value="Normal" selected>Normal Entry</option>
                        @endif
                    @endif

                </select>
            </div>
            <div class="form-group">
                <h3 class=""> For <b>Duare Sarkar</b> entry please select from dropdown <i><b>"Form
                            through
                            Duare Sarkar camp"</b></i></h3>
            </div>
            <div class="row duareSarkar" style="display:none;">
                <div class="form-group col-md-4">
                    <label class="required-field">Duare Sarkar Registration No.</label>
                    <input type="text" name="ds_registration_no" id="ds_registration_no" class="form-control"
                        placeholder="Duare Sarkar Registration No." maxlength="25"
                        value="{{ $type == $op_type ? $row->ds_registration_no : old('ds_registration_no') }}" @if(in_array('ds_registration_no',   $readonly)) readonly @endif/>
                    <span id="error_ds_registration_no" class="text-danger"></span>

                </div>
                <div class="form-group col-md-4">
                    <label class="required-field">Duare Sarkar Date</label>
                    <input type="date" name="ds_date" id="ds_date" class="form-control"
                        max="<?php echo date("Y-m-d"); ?>" value="{{$type == $op_type ? $row->ds_date : old('ds_date')}}" @if(in_array('ds_date',   $readonly)) readonly @endif/>
                    <span id="error_ds_date" class="text-danger"></span>

                </div>
            </div>

            <div class="form-group col-md-12">
                <label class="">Beneficiary Name</label>
            </div>
            <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}">
            <div class="form-group col-md-4">
                <label class="required-field">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control txtOnly"
                    placeholder="First Name" maxlength="200"
                    value="{{ $type == $op_type ? $row->ben_fname : old('first_name') }}" @if(in_array('first_name', $readonly)) readonly @endif />
                <span id="error_first_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label>Middle Name</label>
                <input type="text" name="middle_name" id="middle_name" class="form-control txtOnly"
                    placeholder="Middle Name" maxlength="100"
                    value="{{$type == $op_type ? $row->ben_mname : old('middle_name') }}" @if(in_array('middle_name',   $readonly)) readonly @endif/>
                <span id="error_middle_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control txtOnly" placeholder="Last Name"
                    maxlength="200" value="{{ $type == $op_type ? $row->ben_lname : old('last_name') }}"  @if(in_array('last_name',   $readonly)) readonly @endif/>
                <span id="error_last_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Gender</label>
                <select class="form-control" name="gender" id="gender"  @if(in_array('gender',   $readonly)) readonly @endif>
                    @if($type == $op_type)
                        @foreach(Config::get('constants.gender') as $key => $val)
                            <option value="{{ $key }}" @if($row->gender == $key) selected @endif>
                                {{ $val }}
                            </option>
                        @endforeach
                    @else
                        <option value="">--Select--</option>
                        @foreach(Config::get('constants.gender') as $key => $val)
                            <option value="{{ $key }}" @if(old('gender') == $key) selected @endif>
                                {{ $val }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <span id="error_gender" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="">Date of Birth</label>
                <input type="date" name="dob" id="dob" class="form-control"
                    value="{{$type == $op_type ? $row->dob : old('dob')}}"  @if(in_array('dob',   $readonly)) readonly @endif />
                <span id="error_dob" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Age<span> (as on 01/01/2020)</span></label>
                <input type="hidden" name="hidden_age" id="hidden_age"
                    value="{{$type == $op_type ? $row->ben_age : old('txt_age') }}">
                <input type="text" name="txt_age" id="txt_age" class="form-control NumOnly" placeholder="Age"
                    value="{{$type == $op_type ? $row->ben_age : old('txt_age') }}" maxlength="3" @if(in_array('txt_age',   $readonly)) readonly @endif/>
                <span id="error_txt_age" class="text-danger"></span>
            </div>

            <div class="form-group col-md-12">
                <label class="">Father's Name</label>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">First Name</label>
                <input type="text" name="father_first_name" id="father_first_name" class="form-control txtOnly"
                    placeholder="First Name" maxlength="200"
                    value="{{$type == $op_type ? $row->father_fname : old('father_first_name') }}" @if(in_array('father_first_name',   $readonly)) readonly @endif />
                <span id="error_father_first_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label>Middle Name</label>
                <input type="text" name="father_middle_name" id="father_middle_name" class="form-control txtOnly"
                    placeholder="Middle Name" maxlength="100"
                    value="{{$type == $op_type ? $row->father_mname : old('father_middle_name') }}" @if(in_array('father_middle_name',   $readonly)) readonly @endif/>
                <span id="error_father_middle_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Last Name</label>
                <input type="text" name="father_last_name" id="father_last_name" class="form-control txtOnly"
                    placeholder="Last Name" maxlength="200"
                    value="{{$type == $op_type ? $row->father_lname : old('father_last_name') }}" @if(in_array('father_last_name',   $readonly)) readonly @endif/>
                <span id="error_father_last_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-12">
                <label class="">Mother's Name</label>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">First Name</label>
                <input type="text" name="mother_first_name" id="mother_first_name" class="form-control txtOnly"
                    placeholder="First Name" maxlength="200"
                    value="{{$type == $op_type ? $row->mother_fname : old('mother_first_name') }}" @if(in_array('mother_first_name',   $readonly)) readonly @endif/>
                <span id="error_mother_first_name" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label>Middle Name</label>
                <input type="text" name="mother_middle_name" id="mother_middle_name" class="form-control txtOnly"
                    placeholder="Middle Name" maxlength="100"
                    value="{{$type == $op_type ? $row->mother_mname : old('mother_middle_name') }}" @if(in_array('mother_middle_name',   $readonly)) readonly @endif/>
                <span id="error_mother_middle_name" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="required-field">Last Name</label>
                <input type="text" name="mother_last_name" id="mother_last_name" class="form-control txtOnly"
                    placeholder="Last Name" maxlength="200"
                    value="{{$type == $op_type ? $row->mother_lname : old('mother_last_name') }}" @if(in_array('mother_last_name',   $readonly)) readonly @endif/>
                <span id="error_mother_last_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Caste</label>
                <select class="form-control" name="caste_category" id="caste_category" @if(in_array('caste_category',   $readonly)) readonly @endif>
                    @if ($type == $op_type)
                        @if($scheme_id == 3)
                            <option value="SC">SC</option>
                        @elseif ($scheme_id == 1)
                            <option value="ST">ST</option>
                        @else
                            @foreach(Config::get('constants.caste') as $key => $val)
                                <option value="{{$key}}" @if($row->gender == $key) selected @endif>{{$val}}
                                </option>
                            @endforeach
                        @endif
                    @else
                        @if($scheme_id == 3)
                            <option value="SC">SC</option>
                        @elseif ($scheme_id == 1)
                            <option value="ST">ST</option>
                        @else
                            @foreach(Config::get('constants.caste') as $key => $val)
                                <option value="{{$key}}" @if(old('caste_category') == $key) selected @endif>{{$val}}
                                </option>
                            @endforeach
                        @endif
                    @endif

                </select>
                <span id="error_caste_category" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4" id="caste_certificate_no_section">
                <label class="required-field">Caste Certificate No.</label>
                <input type="text" name="caste_certificate_no" id="caste_certificate_no" class="form-control"
                    placeholder="Caste Certificate No." maxlength="200"
                    value="{{$type == $op_type ? $row->caste_certificate_no : old('caste_certificate_no')}}" @if(in_array('caste_certificate_no',   $readonly)) readonly @endif/>
                <span id="error_caste_certificate_no" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Marital Status</label>
                <select class="form-control" name="marital_status" id="marital_status" @if(in_array('marital_status',   $readonly)) readonly @endif>
                    @if($type ==  $op_type)
                        @foreach(Config::get('constants.marital_status') as $key => $val)
                            <option value="{{ $key }}" @if($row->marital_status == $key) selected
                            @endif>
                                {{ $val }}
                            </option>
                        @endforeach
                    @else
                        <option value="">--Select--</option>
                        @foreach(Config::get('constants.marital_status') as $key => $val)
                            <option value="{{ $key }}" @if(old('marital_status') == $key) selected @endif>
                                {{ $val }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <span id="error_marital_status" class="text-danger"></span>
            </div>

            <div class="row" id="spouse_section">
                <div class="form-group col-md-4">
                    &nbsp;
                </div>
                <div class="form-group col-md-12">
                    <label class="">Spouse Name (if applicable)</label>
                </div>
                <div class="form-group col-md-4">
                    <label class="">First Name</label>
                    <input type="text" name="spouse_first_name" id="spouse_first_name" class="form-control txtOnly"
                        placeholder="First Name" maxlength="200"
                        value="{{ $type == $op_type ? $row->spouse_fname : old('spouse_first_name') }}" @if(in_array('spouse_first_name',   $readonly)) readonly @endif />
                    <span id="error_spouse_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                    <label>Middle Name</label>
                    <input type="text" name="spouse_middle_name" id="spouse_middle_name" class="form-control txtOnly"
                        placeholder="Middle Name" maxlength="100"
                        value="{{ $type == $op_type ? $row->spouse_mname : old('spouse_middle_name') }}" @if(in_array('spouse_middle_name',   $readonly)) readonly @endif/>
                    <span id="error_spouse_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                    <label class="">Last Name</label>
                    <input type="text" name="spouse_last_name" id="spouse_last_name" class="form-control txtOnly"
                        placeholder="Last Name" maxlength="200"
                        value="{{$type == $op_type ? $row->spouse_lname : old('spouse_last_name') }}" @if(in_array('spouse_last_name',   $readonly)) readonly @endif/>
                    <span id="error_spouse_last_name" class="text-danger"></span>
                </div>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Monthly Family Income (In Rs)</label>
                <input type="text" name="monthly_income" id="monthly_income" class="form-control price-field"
                    placeholder="Monthly Family Income(Rs.)" maxlength="9"
                    value="{{$type == $op_type ? $row->mothly_income : old('monthly_income') }}" @if(in_array('monthly_income',   $readonly)) readonly @endif>
                <span id="error_monthly_income" class="text-danger"></span>
            </div>
            @if ($scheme_id == 2 || $scheme_id == 5 || $scheme_id == 17 || $scheme_id == 11)
                <div class="additional_details">
                    <hr>
                    @include('JBformEntry.personal_additional')
                </div>
            @endif
            <div class="col-md-12" align="center">
                <button type="button" name="btn_personal_details" id="btn_personal_details"
                    class="btn btn-success btn-lg">Next</button>
            </div>
            </br>
            </br>
        </div>
    </div>
</div>

<script src="{{ asset("js/FormEntry/personal_details.js") }}"></script>