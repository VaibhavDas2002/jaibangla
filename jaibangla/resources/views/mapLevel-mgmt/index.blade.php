@extends('mapLevel-mgmt.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List Map Level</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('maplevel-management.create') }}">Add Map Level</a>
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
                
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Scheme Name</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Designation </th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Final Level </th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Level</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
              
              
            
            @foreach($mapLavel as $leval)
                <tr role="row" class="odd">
                  <td class="sorting_1">{{$leval->schemename->scheme_name}}</td>
                  <td class="sorting_1">{{$leval->designationname->name}}</td>
                  <td class="sorting_1">{{$leval->parent_id == 0 ? 'Final Approver' : $leval->parentdesignationname->role_name}}</td>
                  <td class="sorting_1">
                     {{$leval->stack_level}}
                  </td>
                  <td>
                   <form class="row" method="POST" action="{{ route('maplevel-management.destroy', ['id' =>$leval->id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('maplevel-management.edit', ['id' => $leval->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
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
                <th width="10%" rowspan="1" colspan="1">Scheme Name</th>
                <th width="10%" rowspan="1" colspan="1">Designation</th>
                <th width="20%" rowspan="1" colspan="1">Final Level </th>
                <th width="20%" rowspan="1" colspan="1">Level </th>
                <th rowspan="1" colspan="2">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($mapLavel)}} of {{count($mapLavel)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $mapLavel->links() }}
          </div>
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