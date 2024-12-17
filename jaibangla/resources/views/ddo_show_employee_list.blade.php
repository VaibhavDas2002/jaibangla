@extends('employees-mgmt.base')

@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List Of Employees</h3>
        </div>
       @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with Application ID: {{$id}}</strong>
                <form method="POST" action="{{ route('nhmemployee.printSingleEmployee', ['id' => $id]) }}">
                       
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      
                        <button type="submit" class="btn btn-danger col-md-2 btn-lg" style="float: right; margin-top:-33px; margin-right:15px;">
                          Print
                        </button>
                </form>      
               
              </div>
       @endif
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
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Application ID</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Employee Code</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Designation</th>
                <th  width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Employee Name</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Posting Place</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
               
              </tr>
            </thead>
            <tbody>
            @foreach ($nhm_employee_details as $nhm_employee_detail)
                <tr role="row" class="odd">
                  <td>{{ $nhm_employee_detail->application_id }}</td>
                  <td>{{ $nhm_employee_detail->emp_code }}</td>
                  <td>{{ $nhm_employee_detail->designationMaster->name }}</td>
                  <td>{{ $nhm_employee_detail->first_name }} {{ $nhm_employee_detail->middle_name }} {{ $nhm_employee_detail->last_name }}</td>                  
                  <td>{{ $nhm_employee_detail->posting_level }}<br> 
                    {{ $nhm_employee_detail->posting_place }}</td>
                  
                  <td>
                  <form class="row" method="POST" action="{{ route('ddogenerateemployeepay', ['id' => $nhm_employee_detail->application_id]) }}">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      
                        <button type="submit" class="btn btn-info  btn-margin" >
                          Generate Payslip
                        </button>
                   </form>
                      

                  </td>
                  
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th width="5%" rowspan="1" colspan="1">Application ID</th>
                <th width="10%" rowspan="1" colspan="1">Employee Code</th>
                <th width="10%" rowspan="1" colspan="1">Designation</th>
                <th width="10%" rowspan="1" colspan="1">Employee Name</th>
                <th width="15%" rowspan="1" colspan="1">Posting Place</th>
                <th width="10%" rowspan="1" colspan="1">Action</th>
                
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
        <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($nhm_employee_details)}} of {{count($nhm_employee_details)}} entries</div>
            </div>
            <div class="col-sm-7">
               <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $nhm_employee_details->links() }}
              </div>
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