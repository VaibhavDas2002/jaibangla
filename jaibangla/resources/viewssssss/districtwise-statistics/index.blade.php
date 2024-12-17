@extends('districtwise-statistics.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if (($message = Session::get('msg')))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
                </div>
            @endif            
            <div class="panel panel-default">
                <div class="panel-heading">Seheme Statistics</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('linelisting-dist-block-ulb') }}" onsubmit="return validate();">
                        {{ csrf_field() }} 

                        
                        <div class="form-group{{ $errors->has('scheme_type') ? ' has-error' : '' }}" id="scheme_div">
                            <label for="scheme_type" class="col-md-4 control-label">Scheme Type</label>

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
                        <div class="form-group{{ $errors->has('level') ? ' has-error' : '' }}" id="scheme_div">
                            <label for="level" class="col-md-4 control-label">Select Level</label>

                            <div class="col-md-6">
                                <select name="level" id="level" class="form-control select2" onchange="capLevel()">
                                    <option value="0">--Select Level--</option>
                                    <!-- <option value="S">State</option> -->
                                    <option value="D">District</option>
                                    <option value="B">Block/Municipality</option>
                                </select>
                                @if ($errors->has('level'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('level') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('district') ? ' has-error' : '' }}" id="district_div">
                            <label for="district" class="col-md-4 control-label">Distict</label>

                            <div class="col-md-6">
                                <select name="district" id="district" class="form-control select2">
                                    <option value="0">--Select District--</option>
                                    @foreach($districts as $district)
                                        <option value="{{$district->district_code}}">{{$district->district_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('district'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('district') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('is_rural_urban') ? ' has-error' : '' }}" id="rural_urban_div">
                            <label for="is_rural_urban" class="col-md-4 control-label">Rural/Urban (Block/Municipality)</label>

                            <div class="col-md-6">
                                <select name="is_rural_urban" id="is_rural_urban" class="form-control select2">
                                    <option value="0">--Select Rural/Urban--</option>
                                    <option value="2">Rural (For Block Area)</option>
                                    <option value="1">Urban (For Municiplity Area)</option>
                                </select>
                                @if ($errors->has('is_rural_urban'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('is_rural_urban') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Submit
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function capLevel(){
        var cap_l = document.getElementById('level').value;
        
        if (cap_l == 'S') {
            $('#district_div').hide();
            $('#rural_urban_div').hide();
        }
        if (cap_l == 'D') {
            $('#district_div').hide();
            $('#rural_urban_div').hide();
        }
        if (cap_l == 'B') {
            $('#district_div').show();
            $('#rural_urban_div').show();
        }
    }

    function validate() {
        if (document.getElementById('scheme_type').value == 0) {
            alert('Please select scheme type');
            return false;
        }
        if (document.getElementById('level').value == 0) {
            alert('Please select level');
            return false;
        }
        return true;
    }
</script>
@endsection


