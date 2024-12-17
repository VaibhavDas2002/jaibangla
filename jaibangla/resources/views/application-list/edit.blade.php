@extends('users-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add SMS</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('smsTemplate.update', ['id' => $smss->id]) }}">
                        {{ csrf_field() }}
                       <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group">
                            <label class="col-md-4 control-label">Message Body</label>
                            <div class="col-md-6">
                             
                             <textarea class="form-control" rows="5" name="sms_message" id="sms_message" required>{{$smss->message}}</textarea>

                                @if ($errors->has('sms_message'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sms_message') }}</strong>
                                    </span>
                                 @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('sms_reason') ? ' has-error' : '' }}">
                            <label for="sms_reason" class="col-md-4 control-label">Reason </label>

                            <div class="col-md-6">
                                <input id="sms_reason" type="text" class="form-control" name="sms_reason" value="{{$smss->reason}}" required autofocus>

                                @if ($errors->has('sms_reason'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sms_reason') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Update
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
