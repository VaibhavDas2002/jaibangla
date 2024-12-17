@extends('mapLevel-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Map Level</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('maplevel-management.store') }}">
                        {{ csrf_field() }}
                       

                        <div class="form-group">
                            <label class="col-md-4 control-label">Scheme Name</label>
                            <div class="col-md-4">
                            <select name="scheme_id" id="scheme_id" class="form-control js-scheme">
                                <option value="">--select--</option>
                                @foreach($schemes as $schemes)
                                <option value="{{$schemes->id}}">{{$schemes->scheme_name}}</option>
                                @endforeach
                            </select>
                                @if ($errors->has('scheme_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_name') }}</strong>
                                    </span>
                                 @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('designation_id_old') ? ' has-error' : '' }}">
                            <label for="designation_id_old" class="col-md-4 control-label">Designation </label>
                            <div class="col-md-4">
                                <select name="designation_id_old" id="designation_id_old" class="form-control" >
                                    <option value="">--select--</option>
                                    @foreach($designations as $designation)
                                <option value="{{$designation->id}}">{{$designation->name}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('designation_id_old'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('designation_id_old') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('final_level_approval') ? ' has-error' : '' }}">
                            <label for="final_level_approval" class="col-md-4 control-label ">Parent Level </label>
                            <div class="col-md-4">
                                <select name="final_level_approval" id="final_level_approval" class="form-control js-nextlevel" >
                                    <option value="">--select--</option>
                                    <option value="0">Final Approver</option>
                                </select>
                                @if ($errors->has('final_level_approval'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('final_level_approval') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('level') ? ' has-error' : '' }}">
                            <label for="level" class="col-md-4 control-label">Level</label>
                            <div class="col-md-4">
                                <select name="level" id="level" class="form-control" >
                                <option value="">--select--</option>
                                <option value="Gram Panchayet">Gram Panchayet</option>
                                <option value="Block">Block</option>
                                <option value="Municipality">Municipality</option>
                                <option value="Subdiv">Sub Division</option>
                                <option value="District">District</option>
                                <option value="State">State</option>
                                </select>
                                @if ($errors->has('level'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('level') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Map Level Create
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
