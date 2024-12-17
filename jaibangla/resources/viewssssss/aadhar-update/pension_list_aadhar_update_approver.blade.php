@extends('aadhar-update.base_pension')

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

       @if ( Session::get('message'))
      <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{{ Session::get('message') }}</strong>     
        
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

      @php
       $sc_id = $scheme_id;
       $obj = DB::connection('pgsql_mis')->table('m_scheme')->where('id',$sc_id)->first();
      @endphp
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Application List of {{ $obj->scheme_name }} Scheme</h3>
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
            <thead style="font-size: 12px;">
              <tr role="row">
                <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Application ID</th>
                <th tabindex="25%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Beneficiary Name</th>
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Account NO.</th>
                <!-- <th tabindex="10%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Gender Name</th> -->
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Block Name</th>
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">GP/Ward Name</th>
               <!--  <th tabindex="5%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Status</th> -->
                <th tabindex="15" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                <!-- <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Status</th> -->
              </tr>
            </thead>
            <tbody>
            @foreach ($nhm_employee_details as $nhm_employee_detail)
                <tr role="row" class="odd">
                  <td>{{$nhm_employee_detail->getBenidAttribute()}}</td>
                  <td>{{ $nhm_employee_detail->ben_fname }} {{ $nhm_employee_detail->ben_mname }} {{ $nhm_employee_detail->ben_lname }}</td>
                  <td>{{ $nhm_employee_detail->bank_code }}</td>
                  <!-- <td>{{ $nhm_employee_detail->gender }}</td> -->
                  <td>{{ $nhm_employee_detail->block_ulb_name }}</td>
                  <td>{{ $nhm_employee_detail->gp_ward_name }}</td>
                 
                  <td>
                  <table>
                  <tr>
                   <!--  <form class="row" method="POST" action="{{ route('pensionform.application-details-read-only', ['id' => $nhm_employee_detail->id]) }}">
                      <td>  
                       
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <input type="hidden" name="scheme_id" value="{{$scheme_id}}">
                      
                        <button type="submit" class="btn btn-info btn-margin" >
                          View
                        </button>
                      </td>  
                    </form> -->
                   
                    <!-- <form class="row" method="GET" action="{{ route('application-aadhar-update-view', ['id' => $nhm_employee_detail->id]) }}">
                    <td> -->
                        <!-- <input type="hidden" name="id" value="{{$nhm_employee_detail->id}}"> -->
                        <!-- <input type="hidden" name="scheme_id" value="{{$scheme_id}}"> -->
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                       <!--  <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                      
                       <!--  <button type="submit" class="btn btn-info btn-margin" >
                          Update
                        </button>
                      </td>
                    </form> -->
                  

                   @php                   

                     echo "<a href='showAadharApplicantDetailsApprover?id=".urlencode($nhm_employee_detail->id)."&scheme_id=".urlencode($scheme_id)."'  class='btn btn-info btn-margin'> View </a>";


                    @endphp
                    
                  </tr>
                  </table>
                  </td>
                  <!-- <td>
                    @if($nhm_employee_detail->aadhar_edit_role_id == 3)
                      <span class="label label-success">Approved</span>
                    
                    @elseif($nhm_employee_detail->aadhar_edit_role_id == 2)
                      <span class="label label-default">Pending</span>
                    
                    @elseif($nhm_employee_detail->aadhar_edit_role_id == -4)
                      <span class="label label-warning">Rejected by Verifier</span>
                    
                    @elseif($nhm_employee_detail->aadhar_edit_role_id == -5)
                      <span class="label label-warning">Rejected by Approver</span>
                    
                    @endif
                  </td> -->
                  <!-- <td>{{ $nhm_employee_detail->verification_status }}</td> -->
              </tr>
            @endforeach
            </tbody>
            <tfoot style="font-size: 12px;">
              <tr>
               <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Employee Details: activate to sort column ascending">Application ID</th>
                <th tabindex="25%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Beneficiary Name</th>
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Age</th>
                <th tabindex="10%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Gender Name</th>
                <th tabindex="15%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Assembly Name</th>
                <!-- <th tabindex="5%" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Status</th> -->
                <th tabindex="15" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
                <!-- <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Status</th> -->
              </tr>
            </tfoot>
          </table>
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
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script type="text/javascript" charset="utf-8" async defer>
  $(document).ready( function () {
    $('#example2').DataTable({
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":10,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "scrollX": true,
      buttons: [
       
      ],
    });
} );
</script>