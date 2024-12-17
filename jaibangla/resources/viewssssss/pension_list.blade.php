@extends('employees-mgmt.base_pension')

@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Application List</h3>
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
            <thead>
              <tr role="row">
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Application ID</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Beneficiary Information</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                <!-- <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Verification Status</th> -->
              </tr>
            </thead>
            <tbody>
            @foreach ($nhm_employee_details as $nhm_employee_detail)
                <tr role="row" class="odd">
                  <td>{{ $nhm_employee_detail->id }}</td>
                  <td>{{ $nhm_employee_detail->ben_fname }} {{ $nhm_employee_detail->ben_mname }} {{ $nhm_employee_detail->ben_lname }}</td>
                  <td>
                  <table>
                  <tr>
                  
                    
                 <form class="row" method="POST" action="{{ route('pensionform.application-details', ['id' => $nhm_employee_detail->id]) }}">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      
                        <button type="submit" class="btn btn-info col-sm-3 col-xs-5 btn-margin" >
                          View
                        </button>
                   </form>

                
 
                   <form class="row" method="POST" action="{{ route('pensionform.application-edit-view') }}">

                    <input type="hidden" name="id" value="{{$nhm_employee_detail->id}}">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      
                        <button type="submit" class="btn btn-info col-sm-3 col-xs-5 btn-margin" >
                          Update
                        </button>
                   </form>

              
                   

                 
</tr>
</table>
                  </td>
                  <!-- <td>{{ $nhm_employee_detail->verification_status }}</td> -->
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th width="20%" rowspan="1" colspan="1">Application ID</th>
                <th rowspan="1" colspan="1">Beneficiary Information</th>
                <th rowspan="1" colspan="1">Action</th>
              <!--   <th rowspan="1" colspan="1">Verification Status</th> -->
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