@extends('duare-sarkar-report.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            @if (($message = Session::get('msg1')))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
                </div>
            @endif            
            <div class="panel panel-default">
                <div class="panel-heading">Select Scheme</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="#" onsubmit="return validate();">
                        {{ csrf_field() }}
                        
                        <div class="form-group{{ $errors->has('scheme_type') ? ' has-error' : '' }}">
                            <label for="scheme_type" class="col-md-4 control-label">Select Scheme</label>

                            <div class="col-md-6">
                                <select name="scheme_type" id="scheme_type" class="form-control select2" required>
                                    <option value="">--Select Scheme--</option>
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
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
    function validate() {
        if (document.getElementById('scheme_type').value == 0) {
            alert('Please select scheme');
            return false;
        }
        return true;
    }
</script>

