@extends('application-list.base')
@section('action-content')
<section class="content">
      <div class="box" >
      <div class="col-md-12" >
        <div class="row">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Inbox</h3>

              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <input type="text" class="form-control input-sm" placeholder="Search Mail">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                </button>
                <div class="btn-group">                                    
                  <button  type="button" id="ddl_application_assignment" class="btn btn-default dropdown-toggle btn-sm policelist" data-toggle="dropdown">Action
                      <span class="fa fa-caret-down"></span></button>
                      <ul class="dropdown-menu" id="ul_assignment">
                        <li><a href="{{url('/applicationList/assign')}}?agent=1&val=Bidhannagar North PS&applications=">Bidhannagar North PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=2&val=Bidhannagar South PS&applications=">Bidhannagar South PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=3&val=Bidhannagar East PS&applications=">Bidhannagar East PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=4&val=Baguiati PS&applications=">Baguiati PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=534&val=Rajarhat PS&applications=">Rajarhat PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=535&val=New Town PS&applications=">New Town PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=536&val=Lake Town PS&applications=">Lake Town PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=537&val=BDN EC PS&applications=">BDN EC PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=538&val=AIRPORT PS&applications=">AIRPORT PS</a></li>
                        <li><a href="{{url('/applicationList/assign')}}?agent=539&val=NSCBI Airport PS&applications=">NSCBI Airport PS</a></li>
                        
                        
                        
                      </ul>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm" ><i id="page_refresh" class="fa fa-refresh"></i></button>
                <div class="pull-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
              </div>
              <div class="table-responsive mailbox-messages" >
                <table class="table table-hover table-striped" id="tbl_application_list">
                  <thead>
                    <tr>
                      <th></th>
                    <th>Application Id</th>
                    <th>Applicant Name</th>
                    <th>Father Name</th>
                    <th>Address</th>
                    <th>Present Pincode</th>
                    <th>Police Station Name</th>
                    <th>Application time</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($applications as $application)
                  <tr>
                    <td width="2%"><input " type="checkbox" class="assignment-checkbox policestationlist" data-applictionno="{{$application->application_id}}" ></td>
                    <td width="10%" class="mailbox-star"><a href="#">{{$application->application_id}}</a></td>
                    <td width="15%" class="mailbox-name">{{$application->first_name}} {{$application->middle_name}}{{$application->last_name}}</td>
                    <td width="15%" class="mailbox-subject">{{$application->father_name}}</td>
                    <td width="20%" class="mailbox-subject">{{$application->present_address_line1}}, {{$application->present_address_line2}},{{$application->present_address_landmark}}
                    </td>
                    <td width="8%" class="mailbox-subject">{{$application->present_pincode}}</td>
                    <td width="15%" class="mailbox-subject">{{$application->police_station_name}}</td>
                    <td width="15%" class="mailbox-subject">{{ date('d-m-Y H:i:s', strtotime($application->application_datetime))}}
                      </td>
                    
                  </tr>
                  @endforeach
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-padding">
              <div class="mailbox-controls">
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($applications)}} of {{count($applications)}}  entries</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                       {{ $applications->links() }}
                    </div>
                  </div>
                </div>
                <!-- /.pull-right -->
              </div>
            </div>
          </div>
        </div>
          <!-- /. box -->
        </div>
        </div>
    </section>
@endsection