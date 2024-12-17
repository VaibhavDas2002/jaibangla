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
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('repeat-lot-generation.generatelot') }}">
                        {{ csrf_field() }}  

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
                                <select  id="month" class="form-control select2" name="month" required>
                                  <option value="">--Select Fin Month--</option>
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
                        
                        
                        <div class="form-group{{ $errors->has('lot_no') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Choose Lot(from Seccessful Lot)</label>
                            <div class="col-md-6">
                                <select class="form-control select2" name="lot_no" required>
                                    <option value="">--Select Role--</option>
                                    @foreach ($lots as $lot)
                                        <option value="{{$lot->lot_no}}">{{$lot->lot_no}}({{$lot->ben_count}})-Ref.{{$lot->ref_no}}&nbsp; Failed at &nbsp;IFMS:{{$lot->ifms_wrongdata_count}}&nbsp;RBI:{{$lot->rbi_failed_count}}</option>
                                    @endforeach
                                </select>
                                 @if ($errors->has('lot_no'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lot_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> 

                        
                        <input type="hidden" name="scheme_id" value="{{ $scheme_id->scheme_id }}">                                              

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Repeat Lot
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
