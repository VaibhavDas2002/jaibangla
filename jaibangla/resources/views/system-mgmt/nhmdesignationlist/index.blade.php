@extends('system-mgmt.nhmdesignationlist.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of Designations</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('nhmDesignationList.create') }}">Add new Designation</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
       <form method="POST" action="{{ route('nhmDesignationList.search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Search'])
          @component('layouts.two-cols-search-row', ['items' => ['Designation Name'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['name'] : '']])
          @endcomponent
        @endcomponent
      </form>
   
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                 <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Serial Number</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Service Category</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Major Programme Head</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Programme Head</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Level</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Department: activate to sort column ascending">Designation</th>
                <th width="40%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
               <?php $count=1;?>
            @foreach ($nhm_designation_lists as $nhm_designation_list)
                <tr role="row" class="odd">
                  <td><?php echo $count;$count++;?></td>
                  <td>{{ $nhm_designation_list->nhm_service_category->name }}</td>
                  <td>{{ $nhm_designation_list->majorProgammeHeadMaster->name }}</td>
                  <td>{{ $nhm_designation_list->programmeHeadMaster->name }}</td>
                  <td >{{ $nhm_designation_list->level }}</td>
                  <td>{{ $nhm_designation_list->name }}</td>
                  <td >
                    <form class="row" method="POST" action="{{ route('nhmDesignationList.destroy', ['id' => $nhm_designation_list->id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('nhmDesignationList.edit', ['id' => $nhm_designation_list->id]) }}" class="btn btn-warning col-md-3  btn-margin">
                        Update
                        </a>
                        <button type="submit" class="btn btn-danger col-md-3  btn-margin">
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
                <th width="15%" rowspan="1" colspan="1">Service Category</th>
                <th width="20%" rowspan="1" colspan="1">Major Programme Head</th>
                <th width="20%" rowspan="1" colspan="1">Programme Head</th>
                <th width="10%" rowspan="1" colspan="1">Level</th>
                <th width="10%" rowspan="1" colspan="1">Designation</th>
                <th rowspan="40%" colspan="1">Action</th>
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