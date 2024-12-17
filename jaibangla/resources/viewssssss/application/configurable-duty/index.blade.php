@extends('application.base')
@section('action-content')
<section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="box-title">Configurable Duty Setting</h3>
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
          <a href="{{url('/mapsetting-config-duty-mgmnt')}}" class="col-md-3 col-md-push-9 btn btn-primary">New Duty Assignment </a>
        <div class="col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                     <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Mapping Level</th>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="4" aria-label="country: activate to sort column ascending">Location</th>
                    
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Username</th>
                    <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                  </tr>
                </thead>
                <tbody>

               
                @foreach($results as $map)
                  
                   
                    <tr role="row" class="odd">
                      <td>{{ $map->mapping_level }}</td>
                      
                      <td colspan="4">
                        @if($map->mapping_level == "State HQ")
                          State HQ
                        @elseif($map->mapping_level == "District HQ")
                          District :{{$map->district->district_name }},
                          Scheme:{{$map->Scheme->scheme_name}}
                        @elseif($map->mapping_level == "Block")
                          @if($map->is_urban == 1)
                            District :{{$map->district->district_name }} , MC: {{$map->urban->urban_body_name}},
                            Scheme:{{$map->Scheme->scheme_name}}
                          @else
                            District :{{$map->district->district_name }} , Block: {{$map->taluka->block_name}},
                            Scheme:{{$map->Scheme->scheme_name}}
                          @endif
                        @endif
                                                  
                      </td>
                      
                      <td id="con_set_user">
                        {{$map->user->username}}
                       
                        

                      </td>
                      <td id="con_set_user">
                        
                        
                        @if($map->is_active==1)
                        <a href="{{ route('enabledisable-config-duty', ['id' => $map->id]) }}" class="btn btn-danger col-md-3 btn-margin"> 
                        Disable
                        </a> 
                        @else
                        <a href="{{ route('enabledisable-config-duty', ['id' => $map->id]) }}" class="btn btn-success col-md-3 btn-margin"> 
                         Enable
                        </a> 
                        @endif
                        
                        
                   
                      </td>
                      
                    </tr>
                   
                @endforeach
               
               
                   
                  

               
                </tbody>
                <tfoot>
                  <tr>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Mapping Level</th>
                    <th width="20%" rowspan="1" colspan="4">Location</th>                    
                    <th width="20%" rowspan="1" colspan="1">Username</th>
                    <th rowspan="1" colspan="1">Action</th>
                  </tr>
                </tfoot>
            </table>
          </div> 
      </div>



      
      </div>
  </div>
</section>

@endsection

