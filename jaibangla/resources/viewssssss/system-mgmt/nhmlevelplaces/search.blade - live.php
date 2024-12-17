@extends('system-mgmt.nhmlevelplaces.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of Health Facilities</h3>
        </div>
        <div class="col-sm-4">
       
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
     
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                 <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Serial Number</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Facility Code</th>
                <th width="25%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Facility Name</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">District</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Location</th>
                <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Facility Type</th>
                <th width="30%"tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>

              <?php $count=1;?>
              
            @foreach ($nhm_level_places as $nhm_level_place)
                <tr role="row" class="odd">
                  <td><?php  echo $count++;?></td>
                  <td>{{ $nhm_level_place->facilty_code }}</td> 
                  <td>{{ $nhm_level_place->facility_name }}</td>
                  <td>{{ $nhm_level_place->district_name}}</td>
                 
                  @if($nhm_level_place->facility_type=="UPHC")
                      @if($nhm_level_place->taluka_code==null)
                      <td>Null</td>
                      @else
                      <td>{{$nhm_level_place->urban_body_name}}</td>
                      @endif     
                  @else 
                      @if($nhm_level_place->taluka_code==null)
                      <td>Null</td>
                      @else
                      <td>{{ $nhm_level_place->taluka_name}}</td> 
                      @endif       
                  @endif
                  <td>{{ $nhm_level_place->facility_type }}</td>   
                  <td>
                    <form class="row" method="POST" action="{{ route('nhmPlace.destroy', ['id' => $nhm_level_place->id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('nhmPlace.edit', ['id' => $nhm_level_place->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                        Update
                        </a>
                        <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                          Delete
                        </button>
                    </form>
                  </td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th width="5%" rowspan="1" colspan="1">Serial Number</th>
                <th width="10%" rowspan="1" colspan="1">Facility Code</th>
                <th width="25%" rowspan="1" colspan="1">Facility Name</th>
                <th width="10%" rowspan="1" colspan="1">District</th>
                <th width="20%" rowspan="1" colspan="1">Location</th>
                <th width="5%" rowspan="1" colspan="1">Facility Type</th>
                <th  width="30%" rowspan="1" colspan="2">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection