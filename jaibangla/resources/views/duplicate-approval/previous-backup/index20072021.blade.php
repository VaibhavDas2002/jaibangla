@extends('duplicate-approval.base')
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
            <div class="panel panel-default">
                <div class="panel-heading">Select Pension Scheme</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('show-duplicate-approval') }}">
                        {{ csrf_field() }} 

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="scheme"  id="scheme">
                                    <option value="0">--Select--</option>
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
                        <div class="form-group{{ $errors->has('filter') ? ' has-error' : '' }}">
                            <label for="filter" class="col-md-4 control-label">Select Fiter Type</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="filter"  id="filter">
                                    <option value="0">--Select--</option>
                                    <option value="ration">By Ration Card</option>
                                    <option value="voter">By Voter Card</option>
                                    <!-- <option value="both">By Ration & Voter Card</option> -->
                                </select>
                                <span style="color: green; font-weight: bold;">Voter Card or Ration Card</span>
                                @if ($errors->has('filter'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('filter') }}</strong>
                                    </span>
                                @endif
                                <!-- <span id="error_construction" class="text-danger"></span> -->
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
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
@endsection


