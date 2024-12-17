@extends('application.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
      <div class="col-md-12" >
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Inbox</h3>

              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <input type="text" class="form-control input-sm" placeholder="Search Mail">
                  <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
              </div>
              <div class="col-md-12" >
                <form method="POST"  action="{{ route('thirdOfficer.search') }}"> 
                   {{ csrf_field() }}
                   @component('layouts.search', ['title' => 'Search'])
                    @component('layouts.two-cols-search-row', ['items' => ['application_id'], 
                    'oldVals' => [isset($searchingVals) ? $searchingVals['Application_No'] : '']])
                    @endcomponent
                  @endcomponent
                </form>
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
                  <button type="button" id="ddl_application_assignment" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown">Action
                        <span class="fa fa-caret-down"></span></button>
                      <ul class="dropdown-menu" id="ul_assignment">
                        <li><a href="{{url('/applicationList/assign')}}?agent=2&applications=">Assign to SI Laketown(Gaurav Basu)</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                      </ul>

                 
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
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
                  <tbody>
                  @foreach ($applictions as $application)
                  <tr>
                    <td width="4%"><input type="checkbox" class="assignment-checkbox" data-applictionno="{{$application->application_id}}"></td>
                    <td width="15%">{{$application->application_id}}</td>
                    <td class="mailbox-name">{{$application->first_name}} {{$application->last_name}}</td>
                     <td class="mailbox-subject"><a href="{{url('downloadCertificate/'.$application->application_id)}}" target="_blank" class="btn btn-primary">DownLoad for Print Certificate</a>
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date"></td>
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
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                </button>
                <div class="btn-group">                                    
                  <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown">Action
                        <span class="fa fa-caret-down"></span></button>
                        <ul class="dropdown-menu">
                          <li><a href="#">Assign to SI Laketown(Gaurav Basu)</a></li>
                          <li><a href="#">Another action</a></li>
                          <li><a href="#">Something else here</a></li>
                          <li class="divider"></li>
                          <li><a href="#">Separated link</a></li>
                        </ul>

                 
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
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
            </div>
          </div>
          <!-- /. box -->
        </div>
        </div>
    </section>
    
@endsection