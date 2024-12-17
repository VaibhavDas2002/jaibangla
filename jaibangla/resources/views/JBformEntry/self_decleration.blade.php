<?php
$old_receive_pension = array();
if (old('receive_pension') != null)
    $old_receive_pension = old('receive_pension');
$old_social_security_pension = array();
if (old('social_security_pension') != null)
    $old_social_security_pension = old('social_security_pension');
?>

<?php
if ($type == $op_type)
{
    $row_receive_pension = array();
    if ($row->receive_pension != null) $row_receive_pension = explode(',', $row->receive_pension);
    //;
    $row_social_security_pension = array();
    if ($row->social_security_pension != null) $row_social_security_pension = explode(',', $row->social_security_pension);
    //explode(',',);
    
}
?>
<div class="tab-pane fade" id="decl_details">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><b>Self Declaration</b></h4>
        </div>
        <div class="panel-body">
        @if($scheme_id != 2)
            <div class="row">
                    <div class="form-group col-md-12 aadhar-text">
                        <label class="">I <select name="av_status" id="av_status">
                                <option value="1"> give </option>
                                <option value="0">do not give </option>
                            </select> consent to the use of the Aadhaar No.for authenticating my identity for social
                            security pension (In case Aadhaar no. provided by the applicant)</label>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="form-group col-md-12" tabindex="4">
                    <label>Presently, I am reciving following pension(s) from</label>
                    <br />
                    @foreach(Config::get('constants.pension_body') as $key => $desc)
                    <label>
                        @if ($type == $op_type)
                        <input type="checkbox" class="receive-pension" name="receive_pension[]" value="{{$key}}"
                                @if(in_array($key,$row_receive_pension,true)) checked @endif> {{$desc}}
                        @else
                        <input type="checkbox" class="receive-pension" name="receive_pension[]" value="{{$key}}"
                        @if(in_array($key, $old_receive_pension, true)) checked @endif> {{$desc}}
                        @endif
                       
                    </label>
                    <br />
                    @endforeach
                </div>
            </div>


          
            @include('JBformEntry.self_decleration_additional')
        </div>
    </div>
</div>



<script src="{{ asset('js/FormEntry/self_dec.js') }}"></script>