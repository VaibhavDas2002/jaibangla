@extends('ben-payment-status.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if (($message = Session::get('success')) && ($id =Session::get('id')))
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
            @endif
            @if (($message = Session::get('msg1')))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
                </div>
            @endif            
            <div class="panel panel-default">
                <div class="panel-heading">Enter Beneficiary Name</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('search-by-name-pmt') }}" onsubmit="return validate();">
                        {{ csrf_field() }}
                        <input type="hidden" name="dist_code" id="dist_code" value="@php if(isset($dist_code)){print $dist_code;} @endphp">
                        <input type="hidden" name="is_rural_urban" id="is_rural_urban" value="@php if(isset($is_urban)){print $is_urban;} @endphp">
                        <input type="hidden" name="block_ulb" id="block_ulb" value="@php if(isset($block_ulb_code)){print $block_ulb_code;} @endphp">
                        <input type="hidden" name="is_department" id="is_department" value="@php if(isset($is_dept)){print $is_dept;} @endphp"> 
                        
                        <div id="style_div" style="border: 1px solid blue; border-radius: 5px; padding: 5px; margin-bottom: 5px;">
                            <div id="for_verifier_level">
                                <div class="form-group{{ $errors->has('ben_fname') ? ' has-error' : '' }}">
                                    <label for="ben_fname" class="col-md-4 control-label">First Name</label>

                                    <div class="col-md-6">
                                        <input type="text" name="ben_fname" id="ben_fname" class="form-control">
                                        @if ($errors->has('ben_fname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('ben_fname') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('ben_mname') ? ' has-error' : '' }}">
                                    <label for="ben_mname" class="col-md-4 control-label">Middle Name</label>

                                    <div class="col-md-6">
                                        <input type="text" name="ben_mname" id="ben_mname" class="form-control">
                                        @if ($errors->has('ben_mname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('ben_mname') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('ben_lname') ? ' has-error' : '' }}">
                                    <label for="ben_lname" class="col-md-4 control-label">Last Name</label>

                                    <div class="col-md-6">
                                        <input type="text" name="ben_lname" id="ben_lname" class="form-control">
                                        @if ($errors->has('ben_lname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('ben_lname') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <!-- ID search -->
                                <div align="center" class="form-group text-primary"><font size="4"><b>or</b></font></div>
                            </div>
                        <div class="form-group{{ $errors->has('ben_id') ? ' has-error' : '' }}">
                            <label for="ben_id" class="col-md-4 control-label">Beneficiary Id</label>

                            <div class="col-md-6">
                                <input type="text" name="ben_id" id="ben_id" class="form-control" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;">
                                @if ($errors->has('ben_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ben_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        </div>
                        <div class="form-group{{ $errors->has('scheme_type') ? ' has-error' : '' }}">
                            <label for="scheme_type" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <select name="scheme_type" id="scheme_type" class="form-control select2" required>
                                    <option value="0">--Select Scheme Type--</option>
                                    @foreach($schemes as $scheme)
                                        <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('scheme_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn-block" disabled id="submit_btn">
                                    Search
                                </button>
                            </div>
                        </div>
                        
                    </form>
                    <div class="text-primary" id="note_div"><b>NOTE: Search using beneficiary name or Id and scheme type also required for searching.</b></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
    $(document).ready(function(){
        document.getElementById('submit_btn').removeAttribute("disabled");
        var dept = document.getElementById('is_department').value;
        var dist_code = document.getElementById('dist_code').value;
        var block_ulb = document.getElementById('block_ulb').value;
        if (dept == 1) {
            $('#for_verifier_level').hide();
            document.getElementById('ben_id').setAttribute("required","required");
            document.getElementById('style_div').style = '';
            document.getElementById('note_div').innerHTML = '';
        }
        if (dept == "" && dist_code != "" && block_ulb == "") {
            $('#for_verifier_level').hide();
            document.getElementById('ben_id').setAttribute("required","required");
            document.getElementById('style_div').style = '';
            document.getElementById('note_div').innerHTML = '';
        }
        if (dept == "" && block_ulb != "" && block_ulb != "") {
            $('#for_verifier_level').show();
            document.getElementById('ben_id').removeAttribute("required");
            document.getElementById('style_div').style = 'border: 1px solid blue; border-radius: 5px; padding: 5px; margin-bottom: 5px;';
            document.getElementById('note_div').innerHTML = '<b>NOTE: Search using beneficiary name or Id and scheme type also required for searching.</b>';
        }
    });

    function validate() {
        var id = document.getElementById('ben_id').value;
        var first = document.getElementById('ben_fname').value;
        if (id == '' && first == '') {
            alert('For searching enter beneficiary id or first name');
            return false;
        }
        if (document.getElementById('scheme_type').value == 0) {
            alert('Please select scheme type');
            return false;
        }
        return true;
    }
</script>

