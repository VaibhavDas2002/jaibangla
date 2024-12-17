@extends('application.policeverification.base')
@section('action-content')

<section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title"> 
          	{{ csrf_field() }}
          	
                  <h4 class="col-sm-10 col-sm-offset-3 info info-success" id="id" style="background: green; padding: 15px; color: #fff;">
                      {{$message}}
                  </h4>
            
          	 </h3>
        </div>        
    </div>
  </div>
  </div>
</section>

@endsection