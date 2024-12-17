@extends('sitrep-configuration.ditrictsitrep.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Configure Sitrep</div>
                <div>
                 @if ($message = Session::get('success'))
                  <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                          <strong>{{ $message }}</strong>
                  </div>
                  @endif
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('configuresitrep.store') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('report_to') ? ' has-error' : '' }}">
                            <label for="report_to" class="col-md-4 control-label">Send To</label>

                            <div class="col-md-6">
                                <input id="report_to" type="text" class="form-control" name="report_to" value="{{ old('report_to') }}" required autofocus>

                                @if ($errors->has('report_to'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('report_to') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('report_from') ? ' has-error' : '' }}">
                            <label for="report_from" class="col-md-4 control-label">Send From</label>

                            <div class="col-md-6">
                                <input id="report_from" type="text" class="form-control" name="report_from" value="{{ old('report_from') }}" required autofocus>

                                @if ($errors->has('report_from'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('report_from') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('org_no') ? ' has-error' : '' }}">
                            <label for="org_no" class="col-md-4 control-label">Org No</label>

                            <div class="col-md-6">
                                <input id="org_no" type="text" class="form-control" name="org_no" value="{{ old('org_no') }}" required autofocus>

                                @if ($errors->has('org_no'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('org_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('ref') ? ' has-error' : '' }}">
                            <label for="ref" class="col-md-4 control-label">Ref</label>

                            <div class="col-md-6">
                                <input id="ref" type="text" class="form-control" name="ref" value="{{ old('ref') }}" required autofocus>

                                @if ($errors->has('ref'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ref') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('ref_org_no') ? ' has-error' : '' }}">
                            <label for="ref_org_no" class="col-md-4 control-label">Ref Org No</label>

                            <div class="col-md-6">
                                <input id="ref_org_no" type="text" class="form-control" name="ref_org_no" value="{{ old('ref_org_no') }}" required autofocus>

                                @if ($errors->has('ref_org_no'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ref_org_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Configure
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
