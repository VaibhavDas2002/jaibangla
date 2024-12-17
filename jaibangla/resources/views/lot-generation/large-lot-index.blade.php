@extends('lot-generation.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Repeat Lot Generation</div>
                <div class="panel-body">
                    <div>
                     @if ( ($message = Session::get('success')) && ($id =Session::get('id')) )
                      <div class="alert alert-success alert-block">                        
                        <button type="button" class="close" data-dismiss="alert">×</button> 
                              <strong>{{ $message }} with Lot Number: {{$id}} </strong>
                      </div>
                      @endif
                    </div>
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('large-lot-generation.generatelot') }}">
                        {{ csrf_field() }}  

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Scheme</label>
                            <div class="col-md-6">
                                <select  id="scheme" class="form-control select2" name="scheme" required>
                                  <option value="">--Select Scheme--</option>
                                  <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                                                 
                                </select>
                                @if ($errors->has('scheme'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Category</label>
                            <div class="col-md-6">
                                <select  id="category" class="form-control select2" name="category" required>
                                  <option value="ALL">ALL</option>
                                  <option value="GENERAL">GENERAL</option>
                                  <option value="SC">SC</option>
                                  <option value="ST">ST</option>                               
                                </select>
                                @if ($errors->has('category'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('category') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('year') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Financial Year</label>
                            <div class="col-md-6">
                                <select  id="year" class="form-control select2" name="year" required>
                                  <option value="">--Select Fin Year--</option>
                                  <option value="2020-2021">2020-2021</option>
                                </select>
                                  @if ($errors->has('year'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('year') }}</strong>
                                      </span>
                                  @endif
                            </div>
                        </div>


                        <div class="form-group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Financial Month</label>
                            <div class="col-md-6">
                                <select  id="month" class="form-control select2 lotmonth" name="month" required>
                                  <option value="">--Select Fin Month--</option>
                                  <option value="April">April</option> 
                                  <option value="May">May</option> 
                                  <option value="June">June</option> 
                                  <option value="July">July</option> 
                                  <option value="August">August</option> 
                                  <option value="September">September</option> 
                                </select>
                                  @if ($errors->has('month'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('month') }}</strong>
                                      </span>
                                  @endif
                            </div>
                        </div>                 
                        
                        
                        <div class="form-group{{ $errors->has('lot_size') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Choose Lot Size(pensing benefeciary : <span id="pendingBenCount"></span>)</label>
                            <div class="col-md-6">
                                <select class="form-control select2" name="lot_size" required id="lot_size">
                                    <option value="">--Select Role--</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="200">200</option>
                                    <option value="300">300</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2000">2000</option>
                                    <option value="3000">3000</option>
                                    <option value="4000">4000</option>
                                    <option value="5000">5000</option>
                                    <option value="7500">7500</option>
                                    <option value="10000">10000</option>                                  
                                </select>
                                 @if ($errors->has('lot_size'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lot_size') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>                                        

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Generate Lot
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
