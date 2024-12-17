@extends('application.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-sm-12">
              <h3 class="box-title"> Application Setting</h3>
            </div>
           
            
          </div>
        </div>
        <div class="box-body">
            @foreach($editResults as $eResult)
                    
            @endforeach

            @foreach($editUsers as $euser)
                
            @endforeach

             @foreach($duty_assignements as $duty_assignement)
               
            @endforeach
           

            <form action="{{url('/updateConfig/'.$duty_assignement->id)}}" method="post">
             {{ csrf_field() }}


              <div>
                  @if(session('message'))
                        <h4 class="col-sm-6 col-sm-offset-3 alert alert-success" id="id">
                            {{session('message')}}
                        </h4>
                   @endif
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label>Police Station</label>
                    
                    <select name="con_ps" id="con_ps" class="form-control select2" required>
                        <option value="">--Select Police Station--</option>
                         @foreach ($policestations as $policestation)
                        <option value="{{$policestation->id}}" {{ ( $policestation->id == $eResult->id ) ? 'selected' : '' }}  > {{$policestation->name}}</option>
                        @endforeach
                    </select>

                </div>
                <div class="col-sm-6">
                    <label>Users</label>
                    <select name="con_user" id="con_user" class="form-control select2">
                        <option value="">--Select User--</option>
                        @foreach ($users as $user)
                        <option value="{{$user->id}}" {{ ( $user->id == $euser->id ) ? 'selected' : '' }} >{{$user->username}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
               <div class="form-group col-sm-4 col-sm-offset-5">
                    <button type="submit" name="update" id="update" value="update" class="btn btn-warning col-sm-5 col-sm-offset-2 col-xs-5 col-xs-offset-2 btn-margin " >Update</button>
               </div>
            </div>
        </form>
            
        </div>
    </div>
</section>

@endsection

