@extends('application.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
      <div class="col-md-12" >
          <div class="box box-primary">
            <div class="box-header with-border">
              <h2 class="box-title">Certificate Download for below Link</h2>

                    <div class="col-md-8 imgPosition ">
                      <a  href="{{URL::to('/')}}/images/{{$applications->application_id}}/{{$applications->signed_certificate}}" target="_blank" width=""><h4> Click here for DownLoad Certificate</h4></a>
                    </div>
              <!-- /.box-tools -->
            </div>
            
            
          </div>
          <!-- /. box -->
        </div>
        </div>
    </section>
    
@endsection