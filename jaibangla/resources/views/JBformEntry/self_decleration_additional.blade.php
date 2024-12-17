@if (in_array($scheme_id, [2, 10, 11, 13, 17]))
    <div class="row">
        <label>In case the applicant is receiving pension from other sources</label>
        <br />
        <label>1.</label>
        <input type="text" name="receiving_pension_other_source_1" id="receiving_pension_other_source_1"
            class="form-control" placeholder=""
            value="{{ $type == $op_type ? $row->receiving_pension_other_source_1 : old('receiving_pension_other_source_1') }}"
            maxlength='300' tabindex="3" />
        <label>2.</label>
        <input type="text" name="receiving_pension_other_source_2" id="receiving_pension_other_source_2"
            class="form-control" placeholder=""
            value="{{$type == $op_type ? $row->receiving_pension_other_source_2 : old('receiving_pension_other_source_2') }}"
            maxlength='300' tabindex="3" />
    </div>
@endif
@if($scheme_id != 2 || $scheme_id != 11)
    <div class="row">
        <div class="col-md-12">
            <div class="modal_field_name">In the event of my death, I hereby nominate (Please mention Name, Address &
                Relationship)
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-4">
            <label class="">Name</label>
            <input type="text" name="nominate_name" id="nominate_name" class="form-control txtOnly" placeholder="Name"
                value="{{$type == $op_type ? $row->nominate_name : old('nominate_name') }}" maxlength='200' />
            <span id="error_nominate_name" class="text-danger"></span>
        </div>

        <div class="form-group col-md-4">
            <label class="">Address</label>
            <input type="text" name="nominate_address" id="nominate_address" class="form-control special-char"
                placeholder="Address" value="{{$type == $op_type ? $row->nominate_address : old('nominate_address') }}"
                maxlength='200' />
            <span id="error_nominate_address" class="text-danger"></span>
        </div>

        <div class="form-group col-md-4">
            <label class="">Relationship</label>
            <input type="text" name="nominate_relationship" id="nominate_relationship" class="form-control txtOnly"
                placeholder="Relationship"
                value="{{$type == $op_type ? $row->nominate_relationship : old('nominate_relationship') }}" maxlength='200' />
            <span id="error_nominate_relationship" class="text-danger"></span>
        </div>


    </div>
    @if ($scheme_id == 17)

        <div class="row">
            <div class="form-group col-md-12">
                <label class="">to receive the rest amount payable to me till my death</label>
            </div>
        </div>
    @endif
@endif

@if ($scheme_id == 17)
    <div class="row">
        <div class="form-group col-md-12">
            <label class="">I <select name="ssp_y_n" id="ssp_y_n">
                    @if($type == $op_type)
                        <option value="1" @if($row->ssp_y_n == 1) selected @endif> am </option>
                        <option value="0" @if($row->ssp_y_n == 0) selected @endif>am not </option>
                    @else
                        <option value="1"> am </option>
                        <option value="0">am not </option>
                    @endif

                </select> a beneficiary of any other Social Security pension scheme or a recipient of Government pension or
                pension from any other organization.</label>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            <label class="">I <select name="pucca_house_y_n" id="pucca_house_y_n">
                @if ($type == $op_type)
                <option value="1" @if($row->pucca_house_y_n == 1) selected @endif>do</option>
                <option value="0" @if($row->pucca_house_y_n == 0) selected @endif>do not</option>
                @else
                <option value="1" >do</option>
                <option value="0" >do not</option>
                @endif
                </select> have Pucca dwelling house.</label>
        </div>
    </div>
    <div class="form-group col-md-12" tabindex="4">
        <label>Presently, I am reciving following pension(s) from</label>
        <br />
        @if ($type == $op_type )
        <?php
                  $row_receive_pension = array();
                  if($row->receive_pension!=null)
                    $row_receive_pension = explode(',',$row->receive_pension);
                   
                ?>

@foreach(Config::get('constants.pension_body') as $key=>$desc)
                    <label>
                      <input type="checkbox" class="receive-pension" name="receive_pension[]" value="{{$key}}"
                        @if(in_array($key,$row_receive_pension,true)) checked @endif> {{$desc}}
                    </label>
                    <br />
                    @endforeach

        
        @else
        @foreach(Config::get('constants.pension_body') as $key => $desc)
            <label>
                <input type="checkbox" class="receive-pension" name="receive_pension[]" value="{{$key}}" @if(in_array($key, $old_receive_pension, true)) checked @endif> {{$desc}}
            </label>
            <br />
        @endforeach
        
        @endif
        
    </div>


@else
    <div class="row">
        <div class="form-group col-md-12" tabindex="5">
            <label>Presently, I am receiving the following social Security Pension/s (Please tick)</label>
            <br />
            @if ($type == $op_type)
            @foreach(Config::get('constants.social_pension_cat') as $key=>$desc)
                            <label>
                              <input type="checkbox" class="social-security-pension" name="social_security_pension[]"
                                value="{{$key}}" @if(in_array($key,$row_social_security_pension,true)) checked @endif>
                              {{$desc}}
                            </label>
                            <br />
                            @endforeach
            @else
                @foreach(Config::get('constants.social_pension_cat') as $key => $desc)
                    <label>
                        <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="{{$key}}"
                            @if(in_array($key, $old_social_security_pension, true)) checked @endif> {{$desc}}
                    </label>
                    <br />
                @endforeach
            @endif

        </div>
    </div>
    <br />
    @if ($scheme_id == 11)
        <div class="row">
            <div class="form-group col-md-12">
                <label class="">I hereby declare that i have not done remarriage</label>
            </div>
        </div>
    @endif
@endif



<br />
<div align="center" class="col-md-12">
    <button type="button" name="previous_btn_decl_details" id="previous_btn_decl_details"
        class="btn btn-info btn-lg">Previous</button>
    <input type="button" class="btn btn-success btn-lg" name="btn_submit_preview" id="btn_submit_preview"
        value="Preview and Submit" data-toggle="modal" data-target="#confirm-submit_">

</div>
<br />