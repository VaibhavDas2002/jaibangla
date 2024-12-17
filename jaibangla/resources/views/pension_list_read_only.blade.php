@extends('employees-mgmt.base_pension')

@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
      @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
      <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{{ $message }} with Application ID: {{$id}}</strong>     
        
      </div>
      @endif
      @if(count($errors) > 0)
      <div class="alert alert-danger alert-block">
        <ul>
          @foreach($errors->all() as $error)
          <li><strong> {{ $error }}</strong></li>
          @endforeach
        </ul>
      </div>
      @endif
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Application List </h3>
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
                <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Application ID</th>
                <th tabindex="25%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Beneficiary Name</th>
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Age</th>
                <th tabindex="10%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Gender Name</th>
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Assembly Name</th>
                <th tabindex="5%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Status</th>
                <th tabindex="15" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                <!-- <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Verification Status</th> -->
              </tr>
            </thead>
            <tbody>
            @foreach ($nhm_employee_details as $nhm_employee_detail)
                <tr role="row" class="odd">
                  <td>{{$nhm_employee_detail->getBenidAttribute()}}</td>
                  <td>{{ $nhm_employee_detail->ben_fname }} {{ $nhm_employee_detail->ben_mname }} {{ $nhm_employee_detail->ben_lname }}</td>
                  <td>{{ $nhm_employee_detail->ben_age }}</td>
                  <td>{{ $nhm_employee_detail->gender }}</td>
                  <td>{{ $nhm_employee_detail->assembly_name }}</td>

                  @if($list_type=='0')
                    @if($nhm_employee_detail->next_level_role_id==-1)
                    <td><span class="label label-danger">Rejected</span></td>
                    @else
                    <td><span class="label label-primary">New</span></td>
                    @endif
                  @else
                    @if($nhm_employee_detail->next_level_role_id==-1)
                    <td><span class="label label-danger">Rejected</span></td>
                    @elseif($nhm_employee_detail->next_level_role_id > 0)
                    <td><span class="label label-info">Verified</span></td>
                    @else
                    <td><span class="label label-success">Approved</span></td>  
                    @endif
                  @endif
                  <td>
                  <table>
                  <tr>
                    <form class="row" method="POST" action="{{ route('pensionform.application-details-read-only', ['id' => $nhm_employee_detail->id]) }}">
                      <td>  
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <input type="hidden" name="scheme_id" value="{{$scheme_id}}">
                      
                        <button type="submit" class="btn btn-info btn-margin" >
                          View
                        </button>
                      </td>  
                    </form>
                    @if($list_type==0)
                    <form class="row" method="POST" action="{{ route('pensionform.application-edit-view') }}">
                    <td>
                        <input type="hidden" name="id" value="{{$nhm_employee_detail->id}}">
                        <input type="hidden" name="scheme_id" value="{{$scheme_id}}">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      
                        <button type="submit" class="btn btn-info btn-margin" >
                          Update
                        </button>
                      </td>
                    </form>
                    @endif
                  </tr>
                  </table>
                  </td>
                  <!-- <td>{{ $nhm_employee_detail->verification_status }}</td> -->
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
               <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Application ID</th>
                <th tabindex="25%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Beneficiary Name</th>
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Age</th>
                <th tabindex="10%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Gender Name</th>
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Assembly Name</th>
                <th tabindex="5%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Status</th>
                <th tabindex="15" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
        <div class="row">
            <div class="col-sm-5">  
              <!-- <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($nhm_employee_details)}} of {{count($nhm_employee_details)}} entries</div> -->
            </div>
            <div class="col-sm-7">
               <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $nhm_employee_details->appends(request()->query())->links() }}
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
