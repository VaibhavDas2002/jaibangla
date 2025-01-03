@extends('users-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Edit Map Level</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('maplevel-management.update',['id' => $mapLavel->id]) }}">
                         <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                       

                        <div class="form-group">
                            <label class="col-md-4 control-label">Scheme Name</label>
                            <div class="col-md-4">
                            <select name="scheme_id" id="scheme_id" class="form-control">
                                <option value="">--select--</option>
                                @foreach($schemes as $schemes)
                                <option value="{{$schemes->id}}" {{$schemes->id == $mapLavel->scheme_id ? 'selected' : ''}}>{{$schemes->scheme_name}}</option>
                                @endforeach
                            </select>
                                @if ($errors->has('scheme_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_name') }}</strong>
                                    </span>
                                 @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('designation_id') ? ' has-error' : '' }}">
                            <label for="designation_id" class="col-md-4 control-label">Designation </label>
                            <div class="col-md-4">
                                <select name="designation_id" id="designation_id" class="form-control" >
                                    <option value="">--select--</option>
                                    @foreach($designations as $designation)
                                <option value="{{$designation->id}}" {{$designation->id == $mapLavel->designation_id ? 'selected' : ''}}>{{$designation->name}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('designation_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('designation_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('final_level_approval') ? ' has-error' : '' }}">
                            <label for="final_level_approval" class="col-md-4 control-label">Next Level Final Approvel </label>
                            <div class="col-md-4">
                                <select name="final_level_approval" id="final_level_approval" class="form-control" >
                                    <option value="">--select--</option>
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
                                <option value="Gram Panchayet" {{ $mapLavel->level_gp_district_mc_subd=='Gram Panchayet' ? 'selected' : ''}}>Gram Panchayet</option>
                                <option value="Block" {{ $mapLavel->level_gp_district_mc_subd=='Block' ? 'selected' : ''}}>Block</option>
                                <option value="Municipality" {{ $mapLavel->level_gp_district_mc_subd=='Municipality' ? 'selected' : ''}}>Municipality</option>
                                <option value="District" {{ $mapLavel->level_gp_district_mc_subd=='District' ? 'selected' : ''}}>District</option>
                                <option value="State" {{ $mapLavel->level_gp_district_mc_subd=='State' ? 'selected' : ''}}>State</option>
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
