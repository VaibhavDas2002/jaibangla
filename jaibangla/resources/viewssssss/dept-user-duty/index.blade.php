<link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
<link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">
<style type="text/css">
table.dataTable thead .sorting_asc:after {
    content: ""!important;
}
table.dataTable thead .sorting:after {
    opacity: 0.2;
    content: ""!important;
}
</style>
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
            @if ($error = Session::get('error') )
              <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $error }}</strong>
               
               
              </div>
              @endif
        </div>
      </div>

      <div class="box-body">
          <a href="{{ route('dept-user-duty.create') }}" class="col-md-3 col-md-push-9 btn btn-primary" style="margin-bottom: 1%"> Add Approver and Assign Role</a>
        <div class="col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                     <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1"  colspan="1" aria-label="country: activate to sort column ascending">Mapping Level</th>
                    <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1"  colspan="1" aria-label="country: activate to sort column ascending">Location</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1"  colspan="1" aria-label="country: activate to sort column ascending">Designation</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1"  colspan="1"  aria-label="country: activate to sort column ascending">Username</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1"  colspan="1"  aria-label="country: activate to sort column ascending">Mobile No</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1"  colspan="1" aria-label="country: activate to sort column ascending">Email</th>
                    <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                  </tr>
                </thead>
                <tbody>

               
                @foreach($results as $map)
                  
                   
                    <tr role="row" class="odd">
                      <td>{{ $map->mapping_level }}</td>
                      
                      <td colspan="1">
                        @if($map->mapping_level == "State")
                          State<br>
                          Scheme:{{$map->Scheme->scheme_name}}
                        @elseif($map->mapping_level == "District")
                          District :{{$map->district->district_name }},<br>
                          Scheme:{{$map->Scheme->scheme_name}}
                        @elseif($map->mapping_level == "Block")
                          @if($map->is_urban == 1)
                            District :{{$map->district->district_name }} ,<br> MC: {{$map->urban->urban_body_name}},<br>
                            Scheme:{{$map->Scheme->scheme_name}}
                          @else
                            District :{{$map->district->district_name }} ,<br> Block: {{$map->taluka->block_name}},<br>
                            Scheme:{{$map->Scheme->scheme_name}}
                          @endif
                        @elseif($map->mapping_level == "Subdiv")
                          @if($map->is_urban == 1)
                            District :{{$map->district->district_name }} ,<br> Sub Div: {{$map->subdiv->sub_district_name}},<br>
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
                        <a href="{{ route('enabledisable-config-duty-dept', ['id' => $map->id]) }}" class="btn btn-danger col-md-9 btn-margin"> 
                        Disable
                        </a> 
                        @else
                        <a href="{{ route('enabledisable-config-duty-dept', ['id' => $map->id]) }}" class="btn btn-success col-md-9 btn-margin"> 
                         Enable
                        </a> 
                        @endif
                        
                        
                   
                      </td>
                      
                    </tr>
                   
                @endforeach
               
               
                   
                  

               
                </tbody>
                <tfoot>
                  <tr>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" aria-label="country: activate to sort column ascending">Mapping Level</th>
                    <th width="20%" rowspan="1"  colspan="1">Location</th>
                    <th width="10%" rowspan="1"  colspan="1">Designation</th>                    
                    <th width="10%" rowspan="1"  colspan="1">Username</th>
                    <th width="10%" rowspan="1"  colspan="1">Mobile No</th>
                    <th width="10%" rowspan="1"  colspan="1">Email</th>
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
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- <script src="{{ asset("js/select2.full.min.js") }}"></script> -->
<!---data table------->
<!--  <script src="{{ asset("js/jquery-1.12.4.js") }}"></script> -->
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script>

<!---data table end--->

<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script> -->
<script>
  $(document).ready(function() {
    $('#example2').DataTable( {
      dom: 'Bflrtip',
      "paging": true,
      "pageLength":10,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      buttons: [
      'pdf','excel','csv','print','copy'
      ]
    } );
  } );
</script>