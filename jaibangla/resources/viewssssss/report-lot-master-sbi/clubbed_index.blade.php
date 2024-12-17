@extends('report-lot-master-sbi.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if (($message = Session::get('success')) )
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }} </strong>
                </div>
            @endif
            @if (($message = Session::get('message')))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
                </div>
            @endif
            @if (($message = Session::get('msg1')))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
                </div>
            @endif            
            <div class="panel panel-default">
                <div class="panel-heading">Lot Report -- SBI</div>
                <div class="panel-body">
                       <form class="form-horizontal" role="form" method="POST" action="{{ route('clubbed-lot-master-sbi-list') }}" onsubmit="return validate();">
                        {{ csrf_field() }}
                        
                        <div class="form-group{{ $errors->has('select_scheme') ? ' has-error' : '' }}">
                                 <label for="select_scheme" class="col-md-4 control-label">Select Scheme</label>
                                   <div class="col-md-6">     
                                   <select name="select_scheme" id="select_scheme" required class="form-control select2">
                                        <option value="" selected>---Select Scheme---</option>
                                        @foreach($reports as $report)
                                        <option value="{{$report->id}}">{{$report->scheme_name}}</option>
                                    @endforeach 
                                    </select>
                                    </div>
                                </div>


                        <div class="form-group{{ $errors->has('lot_year') ? ' has-error' : '' }}">
                            <label for="lot_year" class="col-md-4 control-label">Financial Year</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="lot_year"  id="lot_year">
                                    <option value="">--Select--</option>
                                    <option value="2020-2021">2020-2021</option>
                                    <option value="2021-2022">2021-2022</option>                                                  
                                </select>
                                <span id="error_lot_year" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('lot_month') ? ' has-error' : '' }}">
                            <label for="lot_month" class="col-md-4 control-label">Month</label>

                            <div class="col-md-6">
                                <select  class="form-control select2" name="lot_month"  id="lot_month">
                                    <option value="">--Select--</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>   
                                    <option value="June">June</option>   
                                    <option value="July">July</option>   
                                    <option value="August">August</option>      
                                    <option value="September">September</option>   
                                    <option value="October">October</option>   
                                    <option value="November">November</option>   
                                    <option value="December">December</option>  
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>                                                          
                                </select>
                                <span id="error_lot_month" class="text-danger"></span>
                            </div> 
                        </div>                        
                               
                        
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Submit
                                </button>
                            </div>
                        </div>
                        
                    </form>
                    <div class="text-primary"><b></b></div>
                </div>
            </div>
           
        </div>
    </div>
</div>
<script>
    function validate() {
        if (document.getElementById('select_scheme').value == '') {
            alert('Please select scheme');
            return false;
        }
        if (document.getElementById('lot_year').value == '') {
            alert('Please select year');
            return false;
        }
        if (document.getElementById('lot_month').value == '') {
            alert('Please select month');
            return false;
        }
        return true;
    }
</script>
@endsection


