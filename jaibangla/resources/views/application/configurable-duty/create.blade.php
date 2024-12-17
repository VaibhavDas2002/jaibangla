@extends('application.base')
@section('action-content')
<section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="box-title">Configurable Duty Setting Setting</h3>
          </div>
          <div>


              <!--@if(session('message'))
                  <h4 class="col-sm-6 col-sm-offset-3 alert alert-success" id="id">
                      {{session('message')}}
                  </h4>
             @endif-->
          </div>
          
        </div>
      </div>

       <div class="box-body">
        <form action="{{url('/mapconfig-config-duty-mgmnt')}}" method="post">
           {{ csrf_field() }}
          <div class="row">
            <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }} col-sm-12">
              <label for="scheme" class="col-md-4 control-label">Scheme</label>
              <select  id="scheme" class="form-control select2" name="schemelist[]" multiple="multiple"required>
                <option value="">--Select Scheme--</option>
                @foreach ($schemes as $scheme)
                <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                @endforeach
              </select>
                @if ($errors->has('scheme'))
                    <span class="help-block">
                        <strong>{{ $errors->first('scheme') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('con_user') ? ' has-error' : '' }} col-sm-12">
              <label for="con_user" class="col-md-4 control-label">Users</label>
              <select name="con_user" id="con_user" class="form-control select2 " required>
                <option value="">--Select User--</option>
                @foreach ($users as $user)
                <option value="{{$user->id}}">{{$user->username}}</option>
                @endforeach
              </select>
                @if ($errors->has('con_user'))
                    <span class="help-block">
                        <strong>{{ $errors->first('con_user') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('maping_level') ? ' has-error' : '' }} col-sm-12">
              <label for="maping_level" class="col-md-4 control-label">Mapping Level</label>
              
              <select name="maping_level" id="maping_level" class="form-control select2 district_code" required>
                <option value="">--Select  --</option>
                @foreach ($mappings as $key=>$value)
                <option value="{{$key}}">{{$value}}</option>
                @endforeach
                 
              </select>
                @if ($errors->has('maping_level'))
                    <span class="help-block">
                        <strong>{{ $errors->first('maping_level') }}</strong>
                    </span>
                @endif
            </div>


            <div class="form-group{{ $errors->has('dist_code') ? ' has-error' : '' }} col-sm-12" id="divDistrict">
              <label for="dist_code" class="col-md-4 control-label">District</label>
              
              <select name="dist_code" id="dist_code" class="form-control select2 district_code js-district" >
                <option value="">--Select  --</option>
                 @foreach ($districts as $district)
                <option value="{{$district->district_code}}" > {{$district->district_name}}</option>
                @endforeach
              </select>
                @if ($errors->has('dist_code'))
                    <span class="help-block">
                        <strong>{{ $errors->first('dist_code') }}</strong>
                    </span>
                @endif
            </div>

            

           

            @if($flag==1)
               <div class="form-group{{ $errors->has('urban_code') ? ' has-error' : '' }} col-sm-12" id="divUrbanCode">
              <label for="urban_code" class="col-md-4 control-label">Rural/Urban</label>
              <?php //echo($flag)?>
              <select name="urban_code" id="urban_code" class="form-control select2 js-urban" >
                <option value="">--Select  --</option>
                 @foreach ($levels as $key=>$value)
                <option value="{{$key}}" > {{$value}}</option>
                @endforeach
               <!--  <option value="1">Urban</option>
                <option value="2">Rural</option> -->
                 
              </select>
                @if ($errors->has('urban_code'))
                    <span class="help-block">
                        <strong>{{ $errors->first('urban_code') }}</strong>
                    </span>
                @endif
            </div>


                <div class="form-group{{ $errors->has('body_code') ? ' has-error' : '' }} col-sm-12" id="divBodyCode">
                  <label for="body_code" class="col-md-4 control-label">Municipality/Taluka</label>
                  
                  <select name="body_code" id="body_code" class="form-control select2 js-localbody">
                    <option value="">--Select Option --</option>
                    
                     
                  </select>
                    @if ($errors->has('body_code'))
                        <span class="help-block">
                            <strong>{{ $errors->first('body_code') }}</strong>
                        </span>
                    @endif
                </div>
         
           
          @elseif($flag!=1)

           <div class="form-group{{ $errors->has('urban_code') ? ' has-error' : '' }} col-sm-12" id="divUrbanCode">
              <label for="urban_code" class="col-md-4 control-label">Rural/Urban</label>
              <?php //echo($flag)?>
              <select name="urban_code" id="urban_code" class="form-control select2" >
                <option value="">--Select  --</option>
                 @foreach ($levels as $key=>$value)
                <option value="{{$key}}" > {{$value}}</option>
                @endforeach
               <!--  <option value="1">Urban</option>
                <option value="2">Rural</option> -->
                 
              </select>
                @if ($errors->has('urban_code'))
                    <span class="help-block">
                        <strong>{{ $errors->first('urban_code') }}</strong>
                    </span>
                @endif
            </div>



           <div class="form-group{{ $errors->has('body_code') ? ' has-error' : '' }} col-sm-12" id="divBodyCode">
                  <label for="body_code" class="col-md-4 control-label">Municipality/Taluka</label>
                  
                  <select name="body_code" id="body_code" class="form-control select2">
                    <option value="">--Select Option --</option>
                      @foreach ($locations as $location)
                         <option value="{{$location->code}}" > {{$location->name}}</option>
                       @endforeach
                     
                  </select>
                    @if ($errors->has('body_code'))
                        <span class="help-block">
                            <strong>{{ $errors->first('body_code') }}</strong>
                        </span>
                    @endif
                </div>
          @endif
          </div>

          <div class="row">
           <div class="form-group col-sm-4 col-sm-offset-5">
              <button type="submit" name="submit" id="map" value="Map" class="btn btn-warning col-sm-5 col-sm-offset-2 col-xs-5 col-xs-offset-2 btn-margin " >Map</button>
           </div>
          </div>
        </form>

       
      </div>

      



      
      </div>
  </div>
</section>





@endsection