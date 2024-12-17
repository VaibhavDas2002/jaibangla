@extends('portal.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12" style="margin: 0 auto; text-align: center">
                            <img src="{{ asset("/images/maintainance_icon.png") }}" class="maintainance-image" alt="Under Maintainance">
                        </div>
                        <div class="col-sm-12" style="padding-top:100px; margin:0 auto;">
                            <h1 style="text-align: center; color: #0D0">Activity is temporary down for maintainance</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


