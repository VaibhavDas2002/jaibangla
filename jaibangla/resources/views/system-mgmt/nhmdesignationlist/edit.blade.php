@extends('system-mgmt.nhmdesignationlist.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update Designation</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('nhmDesignationList.update', ['id' => $designationMasters->id]) }}">
                        
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Service Category</label>

                            <div class="col-md-6">
                                <select class="form-control js-service_category_designation_edit" name="service_category">
                                     <option value="{{$designationMasters->nhm_service_category->id}}">{{$designationMasters->nhm_service_category->name}}</option>
                                      @foreach ($nhm_service_categorys as $nhm_service_category)
                                        <option value="{{$nhm_service_category->id}}">{{$nhm_service_category->name}}</option>
                                      @endforeach      
                                </select>
                               <!--  <input id="name" type="text" class="form-control" name="service_category" value="{{ old('service_category') }}" required autofocus> -->

                                @if ($errors->has('service_category'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('service_category') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Major Programme Head</label>
                            <div class="col-md-6">
                                <select class="form-control js-major_programme_head_designation_edit" name="major_programme_head">
                                   
                                         <option value="{{$designationMasters->majorProgammeHeadMaster->id}}">{{$designationMasters->majorProgammeHeadMaster->name}}</option>
                                          
                                       
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Programme Head</label>
                            <div class="col-md-6">
                                <select class="form-control js-programme_head_designation_edit" name="programme_head">
                                   <option value="{{$designationMasters->programmeHeadMaster->id}}">{{$designationMasters->programmeHeadMaster->name}}</option>
                                         
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Level</label>
                            <div class="col-md-6">
                                <select class="form-control" name="level">
                                        <option value="{{$designationMasters->level}}">{{$designationMasters->level}}</option>
                                         <option value="State">State</option>
                                         <option value="District">District</option>
                                         <option value="ULB">ULB</option>
                                         <option value="Block">Block</option>
                                 
                                </select>
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="col-md-4 control-label">Name</label>
                            <div class="col-md-6">
                               <input id="designation_name" type="text" class="form-control" name="designation_name" value="{{old('name', $designationMasters->name)}}" required autofocus>
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
