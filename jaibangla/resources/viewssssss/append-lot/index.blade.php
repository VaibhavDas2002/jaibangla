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
                       <form class="form-horizontal" role="form" method="POST" action="{{ route('store-append-lot') }}" onsubmit="return validate();">
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
                         
                                <div class="form-group{{ $errors->has('select_year') ? ' has-error' : '' }}">
                                 <label for="select_year" class="col-md-4 control-label">Select Year</label>
                                   <div class="col-md-6">     
                                   <select name="select_year" id="select_year" required class="form-control select2">
                                        <option value="0" selected>---Select Year---</option>
                                             <option value="2020-2021">2020-2021</option>
                                    </select>
                                    </div>
                                </div>
                        <div class="form-group{{ $errors->has('select_month') ? ' has-error' : '' }}">

                            <label for="select_month" class="col-md-4 control-label">Select Month</label>
                            
                            <div class="col-md-6">
                               

                                <select name="select_month" id="select_month" required class="form-control select2">
                                    <option value="0" selected>---Select Month---</option>
				    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                </select>
                                @if ($errors->has('select_month'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('select_month') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('select_pmt_mode') ? ' has-error' : '' }}">
                                 <label for="select_pmt_mode" class="col-md-4 control-label">Select Previous Lots Payment Mode</label>
                                   <div class="col-md-6">     
                                   <select name="select_pmt_mode" id="select_pmt_mode" required class="form-control select2">
                                        <option value="0" >---Select Payment Mode---</option>
                                             <option value="SBI" selected>SBI</option>
                                             <option value="IFMS">IFMS</option>
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
            @php if($new_lot_no !='')
          { @endphp
            <div class="alert alert-success">
                    <strong>New lot with Lot No:- {{$new_lot_no}} and number of Beneficiary : {{$ben_count}} has been created.</strong>
                </div>
      @php } @endphp
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


