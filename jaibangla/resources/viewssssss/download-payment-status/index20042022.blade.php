@extends('download-payment-status.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if (($message = Session::get('success')))
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
            <div class="panel panel-default">
                <div class="panel-heading">Payment Status</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('payment-excel-generate') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="rural_urban" id="rural_urban" value="{{$rural_urban}}">
                        <input type="hidden" name="is_urban" id="is_urban" value="{{$is_urban}}"> 

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-4 control-label">Scheme Name</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="scheme"  id="scheme">
                                    <option value="0">--Select Scheme--</option>
                                    @foreach($schemes as $scheme)
                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('scheme'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme') }}</strong>
                                    </span>
                                @endif
                                <!-- <span id="error_construction" class="text-danger"></span> -->
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('fin_year') ? ' has-error' : '' }}">
                            <label for="fin_year" class="col-md-4 control-label">Financial Year</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="fin_year" id="fin_year">
                                   <option value="0">--Select Option--</option>
                                    @foreach(Config::get('constants.fin_year') as $key=>$val)
                                   <option value="{{$key}}">{{$val}}</option>
                                   @endforeach
                                </select>
                                @if ($errors->has('fin_year'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fin_year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label for="month" class="col-md-4 control-label">Month</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="month" id="month">
                                    <option value="0">--Select Option--</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option> 
                                    <option value="April">April</option>
                                    <option value="May">May</option>   
                                    <option value="June">June</option>   
                                    <option value="July">July</option>   
                                    <option value="August">August</option>      
                                    <option value="September">September</option>   
                                    <option value="October">October</option>   
                                    <option value="November">November</option>   
                                    <option value="December">December</option>
                                </select>
                                @if ($errors->has('month'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('month') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Generate Excel
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


