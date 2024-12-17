@extends('application.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-sm-12">
              <h3 class="box-title"> Application Setting</h3>
            </div>
            <div>


                  @if(session('message'))
                        <h4 class="col-sm-6 col-sm-offset-3 alert alert-success" id="id">
                            {{session('message')}}
                        </h4>
                   @endif
            </div>
            
          </div>
        </div>
<div class="box-body">
          <form action="{{url('/mapconfig')}}" method="post">
             {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-sm-6">
                    <label>Police Station</label>
                    
                    <select name="con_ps" id="con_ps" class="form-control select2" required>
                        <option value="">--Select Police Station--</option>
                         @foreach ($policestations as $policestation)
                        <option value="{{$policestation->id}}" > {{$policestation->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label>Users</label>
                    <select name="con_user" id="con_user" class="form-control select2">
                        <option value="">--Select User--</option>
                        @foreach ($users as $user)
                        <option value="{{$user->id}}">{{$user->username}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
               <div class="form-group col-sm-4 col-sm-offset-5">
                    <button type="submit" name="map" id="map" value="Map" class="btn btn-warning col-sm-5 col-sm-offset-2 col-xs-5 col-xs-offset-2 btn-margin " >Map</button>
               </div>
            </div>

            <div class="col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr role="row">
                        <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Police Station</th>
                        <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Assigned To</th>
                        <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
                      </tr>
                    </thead>
                    <tbody>

                   
                    @foreach($results as $result)
                        
                         
                        <tr role="row" class="odd">
                          <td id="con_set_ps">{{$result->name}}</td>
                          <td id="con_set_user">{{$result->username}}</td>
                          <td>
                                <a id="editID" href="{{url('/mapconfigEdit/'.$result->duty_id)}}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                                    
                                Edit
                                </a>
                                <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                                  Deactivate
                                </button>
                           
                          </td>
                        </tr>
                       
                    @endforeach
                   
                         
                      

                   
                    </tbody>
                    <tfoot>
                      <tr>
                        <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Police Station</th>
                        <th width="20%" rowspan="1" colspan="1">Assigned To</th>
                        <th rowspan="1" colspan="2">Action</th>
                      </tr>
                    </tfoot>
                </table>
            </div> 
          </form>
</div>



        
        </div>
    </div>
</section>

@endsection


             