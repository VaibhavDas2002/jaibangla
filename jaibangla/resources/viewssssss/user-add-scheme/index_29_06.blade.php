@extends('user-add-scheme.base')
@section('action-content')
<section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="box-title">Scheme Management</h3>
          </div>
          <div>


              @if(session('$message'))
                  <h4 class="col-sm-6 col-sm-offset-3 alert alert-success" id="id">
                      {{session('$message')}}
                  </h4>
             @endif
          </div>
          
        </div>
      </div>

      <div class="box-body">          
        <div class="col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                     
                    <th class="sorting" tabindex="0" aria-controls="example2"  aria-label="country: activate to sort column ascending">Location</th>
                    <th  class="sorting" tabindex="0" aria-controls="example2"  aria-label="country: activate to sort column ascending">Designation</th>

                    <th class="sorting" tabindex="0" aria-controls="example2" aria-label="country: activate to sort column ascending">Username</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" aria-label="country: activate to sort column ascending">Mobile No</th> 
                    <th class="sorting" tabindex="0" aria-controls="example2" aria-label="country: activate to sort column ascending">Scheme(s)</th>                   
                    <th aria-controls="example2" aria-label="Action: activate to sort column ascending">Action</th>
                  </tr>
                </thead>
                <tbody>

               
                @foreach($results as $map)
                  
                   
                    <tr role="row" class="odd">
                      <td >{{ $map->duty_loc }}</td>
                      <td id="con_set_user">{{$map->designation_id_old}}</td>                      
                      <td id="con_set_user">{{$map->username}}</td>
                      <td>{{ $map->mobile_no }}</td>
                      <td>{{ $map->display_scheme }}</td>
                      <td id="con_set_user">
                        @if (in_array('4', explode(',', $map->assigned_scheme)))
                          <span>Already Added</span>
                        @else 
                          <select class="">
                            <option value="">--Select -- </option>
                            <option value="4">Prachesta</option>
                          </select>
                          <button class="assignScheme btn-primary" data-scheme="4" data-user_id="{{$map->user_id}}">Add</button>     
                        @endif
                                           
                      </td>
                      
                    </tr>
                   
                @endforeach
               
               
                   
                  

               
                </tbody>
                
            </table>
          </div> 
      </div>



      
      </div>
  </div>
</section>

@endsection

