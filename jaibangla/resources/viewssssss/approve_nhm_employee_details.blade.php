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
             <form method="POST" action="{{ route('nhmemployee.MassEmployeeApproval') }}" class="submit-once">
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <thead>
              <tr role="row">
                <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Application ID</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Employee Code</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Employee Information</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Approval Status</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                 <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Check</th>
              </tr>
            </thead>
            
            <tbody>
           
            @foreach ($nhm_employee_details as $nhm_employee_detail)
                <tr role="row" class="odd">
                  <td>{{ $nhm_employee_detail->id }}</td>
                  @if ($nhm_employee_detail->approval_status=="Not Approved" || $nhm_employee_detail->approval_status=="Disapproved")
                    <td>Employee Code Not Generated</td>
                  @elseif ($nhm_employee_detail->approval_status=="Approved")
                   <td>{{ $nhm_employee_detail->emp_code}}</td>
                  @endif
                  <td>{{ $nhm_employee_detail->first_name }} {{ $nhm_employee_detail->middle_name }} {{ $nhm_employee_detail->last_name }}</td>
                  <td>{{ $nhm_employee_detail->approval_status }}</td>
                  <td>
                    <!-- <form class="row" method="POST" action="{{ route('nhmemployee.showSingleEmployeeApproval', ['id' => $nhm_employee_detail->id]) }}"> -->
                       
                       
                      
                        <a href="{{ route('nhmemployee.showSingleEmployeeApproval', ['id' => $nhm_employee_detail->id]) }}" class="btn btn-info col-xs-5 btn-margin">
                        View
                        </a>
                        
                    <!-- </form> -->
                      
                  </td>
                 

                  @if($nhm_employee_detail->approval_status=="Not Approved")                                                        
                  <td> <input type="checkbox" name="approvalcheck[]" onchange="document.getElementById('bulk_approve').disabled = !this.checked;" value="{{ $nhm_employee_detail->id }}"></td>
                   @endif
              </tr>
            @endforeach

            </tbody>
            
            <tfoot>
              <tr>
                <th width="5%" rowspan="1" colspan="1">Application ID</th>
                <th width="20%" rowspan="1" colspan="1">Employee Code</th>
                <th rowspan="1" colspan="1">Employee Information</th>
                <th rowspan="1" colspan="1">Approval Status</th>
                <th rowspan="1" colspan="1">Action</th>
                <th rowspan="1" colspan="1">Check</th>
              </tr>
            </tfoot>
              <button type="submit" name="bulk_approve" id="bulk_approve" class="btn btn-info col-sm-3 col-xs-5 btn-margin" disabled>
                         Bulk Approve
              </button>
            </form>
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
 <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script> 
<script>
$(document).ready(function(){  
$('form.submit-once').submit(function(e){
    if( $(this).hasClass('form-submitted') ){
        e.preventDefault();
        return;
    }
    $(this).addClass('form-submitted');
});
});
</script>


@endsection