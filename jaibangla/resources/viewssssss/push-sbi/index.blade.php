@extends('append-lot.base')
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
                <div class="panel-heading">Repeat Lot</div>
                <div class="panel-body">
                       <form class="form-horizontal" role="form" method="POST" action="{{ route('push-sbi-lot-listing') }}" onsubmit="return validate();">
                        {{ csrf_field() }}
                        
                        <div class="form-group{{ $errors->has('select_scheme') ? ' has-error' : '' }}">
                                 <label for="select_scheme" class="col-md-4 control-label">Select Scheme</label>
                                   <div class="col-md-6">     
                                   <select name="select_scheme" id="select_scheme" required class="form-control select2">
                                        <option value="0" selected>---Select Scheme---</option>
                                        @foreach($reports as $report)
                                        <option value="{{$report->id}}">{{$report->scheme_name}}</option>
                                    @endforeach 
                                    </select>
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
                    <div class="text-primary"><b></b></div>
                </div>
            </div>
           
        </div>
    </div>
</div>
<script>
    function validate() {
        if (document.getElementById('select_month').value == 0) {
            alert('Please select month');
            return false;
        }
        return true;
    }
</script>
@endsection


