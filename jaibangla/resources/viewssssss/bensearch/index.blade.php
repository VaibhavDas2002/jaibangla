@extends('bensearch.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  
  <!-- /.box-header -->
  <form method="POST" action="{{route('search')}}">
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      
         {{ csrf_field() }}                 
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="inputschemetype" class="col-sm-3 control-label">Scheme Type</label>
                <div class="col-sm-9">
                  <select class="form-control select2" name="scheme"  id="scheme">
                      <option value="">--Select--</option>
                      <option value="1">Toposili Bandhu(for SC)</option>
                      <option value="2">Jai Johar(for ST)</option>
                      <option value="3">Manabik</option>

                      <option value="#">Old Age Pension</option>
                      <option value="#">Widow Pension</option>
                      <option value="#">Farmer's Old Age Pension</option>
                      <option value="#">Old Age Pension for Fishermen</option>
                      <option value="#">Old Age Pension for Artisans and Handloom Weavers</option>
                      <option value="#">Lok Prasar Prakalpa</option>
                                            
                  </select>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="inputschemetype" class="col-sm-3 control-label">Application ID</label>
                <div class="col-sm-9">
                  <input value="" type="text" class="form-control" name="applicationId" id="applicationId" placeholder="Scheme Type">
                </div>
              </div>
            </div>
          
          </div><!-- .row -->

                  
         
  </div>
  <!-- /.box-body -->
  <div class="box-footer">
    <button type="submit" class="btn btn-primary">
      <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
      Search
    </button>
  </div>
  </form> 
</div>

@isset($ben)
This is new line here
{{ $ben->id }}
@endisset



    </section>
    <!-- /.content -->
  </div>
@endsection