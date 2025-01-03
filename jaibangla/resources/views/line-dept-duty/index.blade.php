@extends('emp-user-duty.base')
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
          <a href="{{ route('line-dept-duty.create') }}" class="col-md-3 col-md-push-9 btn btn-primary"> Add User and Assign Role</a>
        <div class="col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                     <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Mapping Level</th>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="4" aria-label="country: activate to sort column ascending">Location</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Designation</th>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Username</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Mobile No</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Email</th>
                    <th width="20%"tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                  </tr>
                </thead>
                <tbody>

               
                @foreach($results as $map)
                  
                   
                    <tr role="row" class="odd">
                      <td>{{ $map->mapping_level }}</td>
                      
                      <td colspan="4">
                        @if($map->mapping_level == "State")
                          State
                          Scheme:{{$map->Scheme->scheme_name}}
                        @elseif($map->mapping_level == "District")
                          District :{{$map->district->district_name }},
                          Scheme:{{$map->Scheme->scheme_name}}
                        @elseif($map->mapping_level == "Department")
                          District :{{$map->Department->name }},
                          Scheme:{{$map->Scheme->scheme_name}}
                        @elseif($map->mapping_level == "Block")
                          @if($map->is_urban == 1)
                            District :{{$map->district->district_name }} , MC: {{$map->urban->urban_body_name}},
                            Scheme:{{$map->Scheme->scheme_name}}
                          @else
                            District :{{$map->district->district_name }} , Block: {{$map->taluka->block_name}},
                            Scheme:{{$map->Scheme->scheme_name}}
                          @endif
                        @elseif($map->mapping_level == "Subdiv")
                          @if($map->is_urban == 1)
                            District :{{$map->district->district_name }} , Sub Div: {{$map->subdiv->sub_district_name}},
                            Scheme:{{$map->Scheme->scheme_name}}                          
                          @endif
                        @endif
                                                  
                      </td>

                      <td id="con_set_user">
                        {{$map->user->designation_id}}
                       
                        

                      </td>
                      
                      <td id="con_set_user">
                        {{$map->user->username}}
                       
                        

                      </td>
                      <td>
                        {{ $map->user->mobile_no }}
                      </td>
                      <td>
                        {{ $map->user->email }}
                      </td>
                      <td id="con_set_user">
                        
                        
                        @if($map->is_active==1)
                        <a href="{{ route('enabledisable-config-duty', ['id' => $map->id]) }}" class="btn btn-danger col-md-5 btn-margin"> 
                        Disable
                        </a> 
                        @else
                        <a href="{{ route('enabledisable-config-duty', ['id' => $map->id]) }}" class="btn btn-success col-md-5 btn-margin"> 
                         Enable
                        </a> 
                        @endif
                        
                        
                   
                      </td>
                      
                    </tr>
                   
                @endforeach
               
               
                   
                  

               
                </tbody>
                <tfoot>
                  <tr>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="country: activate to sort column ascending">Mapping Level</th>
                    <th width="20%" rowspan="1" colspan="4">Location</th>
                    <th width="10%" rowspan="1" colspan="1">Designation</th>                    
                    <th width="20%" rowspan="1" colspan="1">Username</th>
                    <th width="10%" rowspan="1" colspan="1">Mobile No</th>
                    <th width="10%" rowspan="1" colspan="1">Email</th>
                    <th width="20%"rowspan="1" colspan="1">Action</th>
                  </tr>
                </tfoot>
            </table>
          </div> 
      </div>



      
      </div>
  </div>
</section>

@endsection

