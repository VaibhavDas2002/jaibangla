@extends('application.policeverification.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of application</h3>
        </div>        
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6">
         
        </div>
        <div class="col-sm-6"></div>
      </div>

       <form method="POST"  action="{{ route('policeverification.search') }}"> 
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Search'])
          @component('layouts.two-cols-search-row', ['items' => ['application_id'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['Application_No'] : '']])
          @endcomponent
        @endcomponent
      </form>
      <!--Cut from here-->
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Application No</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Applicant Name</th>
                
                <th width="15%" class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Father Name</th>
                <th width="20%" class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Address</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
                //$file_name = json_decode($stored_file_name);
                /*echo "<pre>";
                print_r($stored_file_names);
                echo "</pre>";*/
               ?>

            <!--@foreach ($stored_file_names as $file_name)
              
            @endforeach-->
            <?php 

              /*$stored_file_name = json_decode($file_name->stored_file_name);
              $profile_img = $stored_file_name[4];

              echo "<pre>";
              print_r($file_name->stored_file_name);
              echo "<pre>";*/
            ?>
            @foreach ($applications as $application)
              @if($application->is_rejected == 'Y')
              <tr role="row" class="odd">
                  <td class="sorting_1">{{ $application->application_id }}</td>
                  <td>{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</td>
                  <td class="hidden-xs">{{ $application->father_name }}</td>
                 
                  <td class="hidden-xs">{{ $application->present_address_line1 }}</td>
                  <td>
                    <a href="{{ route('policeverification.edit', ['id' => $application->application_id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                        Prepare
                    </a>
                  </td>
              </tr>
              @endif

              @if($application->is_rejected == 'P')
                <tr role="row" class="odd">
                  <td class="sorting_1">{{ $application->application_id }}</td>
                  <td>{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</td>
                  <td class="hidden-xs">{{ $application->father_name }}</td>
                 
                  <td class="hidden-xs">{{ $application->present_address_line1 }}</td>
                  <td>
                    <a href="{{ route('policeverification.edit', ['id' => $application->application_id]) }}" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                        Pending
                    </a>
                  </td>
                </tr>
              @endif
              


              
            @endforeach
            
            </tbody>
            <tfoot>
              <tr role="row">
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Application No</th>
                <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Applicant Name</th> 
               
                <th width="15%" class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Father Name</th>
                <th width="20%" class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Address</th>
                <th  tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($applications)}} of {{count($applications)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $applications->links() }}
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