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
           <br/><br/>
             @if ($message = Session::get('message') )
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
               
               
              </div>
              @endif
             
          </div>
           <div>
          
             @if ($error = Session::get('error') )
              <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $error }}</strong>
               
               
              </div>
              @endif
             
          </div>
        </div>
      </div>

      <div class="box-body">
          <a href="{{ route('emp-user-duty.create') }}" class="col-md-3 col-md-push-9 btn btn-primary"> Add User and Assign Role</a>
          <br/> <br/> <br/>
        <div class="col-md-12">
        <table id="example2" class="table table-bordered table-hover"  >
                <thead>
                  <tr role="row">
                     <th>Mapping Level</th>
                    <th width="50%">Location</th>
                    <th>Designation</th>
                    <th>Username</th>
                    <th>Mobile No</th>
                    <th>Email</th>
                    <th>Current Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

               
                @foreach($results as $map)
                  
                   
                    <tr role="row" class="odd">
                      <td>{{ $map->mapping_level }}</td>
                      
                      <td width="30%">
                        @if($map->mapping_level == "State")
                          State
                          Scheme:{{$map->Scheme->scheme_name}}
                        @elseif($map->mapping_level == "District")
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
                       <td>
                          {{$map->is_active==1?'Enabled':'Disabled'}}

                      </td>
                      <td id="con_set_user">
                        
                        
                        @if($map->is_active==1)
                        <a href="{{ route('enabledisable-config-duty-emp', ['id' => $map->id]) }}" class="btn btn-danger" onclick="return confirm('Are you sure, you want to Disable it?')"> 
                        Click to Disable
                        </a> 
                        @else
                        <a href="{{ route('enabledisable-config-duty-emp', ['id' => $map->id]) }}" class="btn btn-success" onclick="return confirm('Are you sure, you want to Enable it?')"> 
                         Click to Enable
                        </a> 
                        @endif
                        
                        
                   
                      </td>
                      
                    </tr>
                   
                @endforeach
               
               
                   
                  

               
                </tbody>
                <tfoot>
                  <tr>
                    <th>Mapping Level</th>
                    <th width="30%">Location</th>
                    <th>Designation</th>                    
                    <th>Username</th>
                    <th>Mobile No.</th>
                    <th>Email</th>
                    <th>Current Status</th>
                    <th>Action</th>
                  </tr>
                </tfoot>
            </table>
          </div> 
      </div>



      
      </div>
  </div>
</section>
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}" type="text/javascript" ></script>
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script>
$(document).ready(function() {
    $('#example2').DataTable( {
        "scrollX": true,
        "ordering": false
    } );
} );
</script>
@endsection

