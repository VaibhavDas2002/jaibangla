<style type="text/css">
  .full-width{
    width:100%!important;
  }
</style>

@extends('employee-report.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
      <div class="box-header">
        <div class="row">
            <div class="col-sm-8">
              <h3 class="box-title">List of HMS Employee</h3>
            </div>
            <!-- <div class="col-sm-4">
              <a class="btn btn-primary" href="{{ route('user-management.create') }}">Add new user</a>
            </div> -->
        </div>
      </div>
      <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
       <!--  <div class="col-md-12"> -->
        <form method="POST" role="form" action="{{ route('employeereport.fetch') }}">
           {{ csrf_field() }} 
          <div class="form-group col-md-4">
                            <label class=" control-label">Level</label>
                           <!--  <div class=""> -->
                                <select class="form-control  full-width js-reportlevel1"  name="level1">
                                    <option value="">--Select--</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="ULB">ULB</option>  
                                    <option value="Block">Block</option> 
                                         
                                </select>
                           <!--  </div> -->
          </div>
           <div class="form-group col-md-4">
                            <label class=" control-label">List of State/District/Block</label>
                           <!--  <div class=""> -->
                                <select class="form-control col-md-12 full-width js-reportlevel2" name="level2">
                                  <option value="">-----Select Option-----</option>
                                   <!--  <option value="">--Select--</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>      -->  
                                </select>
                           <!--  </div> -->
          </div>
           <div class="form-group col-md-4">
                            <label class=" control-label">SPMU/MCH/DPMU/Hospital/CPMU/UPHC/BPMU/SC</label>
                           <!--  <div class=""> -->
                                <select class="form-control col-md-12 full-width js-reportlevel3" name="level3">
                                    <option value="">--Select Option--</option>
                                  <!--   <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>   -->     
                                </select>
                          <!--   </div> -->
          </div><br><br>
            <div class="form-group col-md-8">
                            <label class=" control-label" style="margin-top: 2%;">List of Health Facility</label>
                           <!--  <div class=""> -->
                                <select class="form-control col-md-12 full-width js-reportlevel4" name="level4">
                                    <option value="">--Select Option--</option>
                                  <!--   <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>   -->     
                                </select>
                          <!--   </div> -->
          </div>

          <div class="col-md-4"  style="margin-top: 2%;">
          <input type="submit" class="btn btn-success btn-lg " name="btn_submit_report" id="btn_submit_report" value="Submit" ></div>
          </form>
       <!--  </div> -->

                  
        <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Serial No</th>
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Application Id</th>
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Name</th>
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Father/Guardian Name</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Appointing Authority</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Posting Level</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Posting Place</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Entered At</th>
                <!-- <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th> -->
              </tr>
            </thead>
            <tbody>
              <?php $i=1; ?>
            @foreach ($employeedatas as $employeedata)
                <tr role="row" class="odd">
                  <td class="sorting_1"><?php echo $i++; ?></td>
                  <td class="sorting_1">{{ $employeedata->id }}</td>
                  <td class="sorting_1">{{ $employeedata->first_name }} {{ $employeedata->middle_name }} {{ $employeedata->last_name }} </td>
                  <td class="sorting_1">{{ $employeedata->guardian_name }}</td>
                  <td>{{ $employeedata->appointing_authority }}</td>
                   <td>{{ $employeedata->posting_level }}</td>
                  <td>{{ $employeedata->posting_place }}</td>
                  <td>{{ $employeedata->created_at }}</td>
                  
              </tr>
            @endforeach 
          
            </tbody>

            <tfoot>
              <tr>
                <th width="10%" rowspan="1" colspan="1">Serial No</th>
                <th width="10%" rowspan="1" colspan="1">Application Id</th>
                <th width="10%" rowspan="1" colspan="1">Employee Name</th>
                <th width="10%" rowspan="1" colspan="1">Father/Guardian Name</th>
                <th width="20%" rowspan="1" colspan="1">Appointing Authority</th>
                <th width="20%" rowspan="1" colspan="1">Posting Level</th>
                <th width="20%" rowspan="1" colspan="1">Posting Place</th>
                <th rowspan="1" colspan="2">Entered At</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <?php //if($flag==1):?>
     
    <?php// endif;?>
    </div>
    </div>
    </section>
    <!-- /.content -->
  </div>
@endsection