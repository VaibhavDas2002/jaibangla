@extends('ben-application-status.base')
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
                <div class="panel-heading">Enter Beneficiary Details</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('list-app-status') }}" onsubmit="return validate();">
                        {{ csrf_field() }}
                         
                        
                        <div style="border: 1px solid blue; border-radius: 5px; padding: 5px; margin-bottom: 5px;">
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
                            <label for="scheme_type" class="col-md-4 control-label">Scheme Name</label>

                            <div class="col-md-6">
                                <select name="scheme_type" id="scheme_type" class="form-control select2">
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
                        <div class="form-group{{ $errors->has('dist_code') ? ' has-error' : '' }}">
                            <label for="dist_code" class="col-md-4 control-label">District</label>

                            <div class="col-md-6">
                                <select name="dist_code" id="dist_code" class="form-control select2">
                                    <option value="0">--Select District--</option>
                                    @foreach($districts as $district)
                                        <option value="{{$district->district_code}}">{{$district->district_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('dist_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dist_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('is_rural_urban') ? ' has-error' : '' }}">
                            <label for="is_rural_urban" class="col-md-4 control-label">Rural/Urban</label>

                            <div class="col-md-6">
                                <select name="is_rural_urban" id="is_rural_urban" class="form-control select2 js_rural_urban">
                                    <option value="0">--Select Rural/Urban--</option>
                                    <option value="2">Rural</option>
                                    <option value="1">Urban</option>
                                </select>
                                @if ($errors->has('is_rural_urban'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('is_rural_urban') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('block_ulb') ? ' has-error' : '' }}">
                            <label for="block_ulb" class="col-md-4 control-label">Block/Municipality</label>

                            <div class="col-md-6">
                                <select name="block_ulb" id="block_ulb" class="form-control select2 js_block_ulb">
                                    <option value="0">--Select Block/Municipality--</option>
                                    {{--  @foreach ($results as $result)
                                            <option value="{{$result->code}}">{{$result->name}}</option>
                                        @endforeach  --}}
                                </select>
                                @if ($errors->has('block_ulb'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('block_ulb') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Search
                                </button>
                            </div>
                        </div>
                        
                    </form>
                    <div class="text-primary"><b>NOTE: Search using beneficiary name or Id and others all field required for searching.</b></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
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
        if (document.getElementById('dist_code').value == 0) {
            alert('Please select sdistrict');
            return false;
        }
        if (document.getElementById('is_rural_urban').value == 0) {
            alert('Please select rural/urban');
            return false;
        }    
        if (document.getElementById('block_ulb').value == -1) {
            alert('Please select block/municipality');
            return false;
        }
        return true;
    }
</script>
@endsection


