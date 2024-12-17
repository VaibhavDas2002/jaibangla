@extends('employees-mgmt.base')

@section('action-content')
<style>


</style>
<section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Employee Detail</h3>
        </div>
        <!-- <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('commissionerate.create') }}">Add new Commissionerate</a>
        </div> -->
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
     
       <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Level</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Location</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Employee Code</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Employee Name</th>
                 <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Guardian's Name</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($created_employee_lists as $created_employee_list)
                <tr role="row" class="odd">
                  <td>{{ $created_employee_list->emp_code }}</td>
                  <td>{{ $created_employee_list->emp_code }}</td>
                  <td>{{ $created_employee_list->emp_code }}</td>
                  <td>{{ $created_employee_list->title }} {{ $created_employee_list->first_name }} {{ $created_employee_list->middle_name }} {{ $created_employee_list->last_name }}</td>
                  <td>{{ $created_employee_list->guardian_name }}</td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th width="20%" rowspan="1" colspan="1">Level</th>
                <th rowspan="1" colspan="1">Location</th>
                <th rowspan="1" colspan="1">Employee Code</th>
                <th rowspan="1" colspan="1">Employee Name</th>
                <th rowspan="1" colspan="1">Guardian's Name</th>
              </tr>
            </tfoot>
          </table>
        <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($created_employee_lists)}} of {{count($created_employee_lists)}} entries</div>
            </div>
            <div class="col-sm-7">
               <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $created_employee_lists->links() }}
              </div>
            </div>
          </div>
      </div>
     
</div>
</section>
    


@endsection