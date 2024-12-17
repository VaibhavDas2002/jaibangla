@extends('portal.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Select Financial Year And Month</div> 
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{route('report_lot_master_main')}}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-4 control-label">Scheme</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="scheme_id"  id="scheme">
                                    <option value="">--Select--</option>
                                    @foreach ($schemes as $scheme)
                                    <option value="{{$scheme->Scheme->id}}">{{$scheme->Scheme->scheme_name}}</option>
                                    @endforeach 

                                   <!--  <option value="#" disabled >Old Age Pension</option>
                                    <option value="#" disabled >Widow Pension</option>
                                    <option value="#" disabled >Farmer's Old Age Pension</option>
                                    <option value="#" disabled >Old Age Pension for Fishermen</option>
                                    <option value="#" disabled >Old Age Pension for Artisans and HandloomWeavers</option>
                                    <option value="#" disabled >Lok Prasar Prakalpa</option>  -->
                                                          
                                </select>
                                <span id="error_scheme" class="text-danger"></span>
                            </div>
                        </div> 

                        <div class="form-group{{ $errors->has('lot_year') ? ' has-error' : '' }}">
                            <label for="lot_year" class="col-md-4 control-label">Financial Year</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="lot_year"  id="lot_year">
                                    <option value="">--Select--</option>
                                    <option value="2020-2021">2020-2021</option>
                                    <option value="2021-2022">2021-2022</option>

                                   <!--  <option value="#" disabled >Old Age Pension</option>
                                    <option value="#" disabled >Widow Pension</option>
                                    <option value="#" disabled >Farmer's Old Age Pension</option>
                                    <option value="#" disabled >Old Age Pension for Fishermen</option>
                                    <option value="#" disabled >Old Age Pension for Artisans and HandloomWeavers</option>
                                    <option value="#" disabled >Lok Prasar Prakalpa</option>  -->
                                                          
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
                        <div class="col-md-12">
                        <button  style="border:1px solid black ; display:block;margin-left: 40%;" type="submit" name="go" id="go" value="approve" class="btn btn-info col-md-3 btn-margin" >
                                   Go
                        </button>
                        </div>
                        <script>
                           
                            function la(src)
                            {
                                window.location=src;
                            }
                            
                        </script>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
$(document).ready(function(){
error_lot_year='';
error_lot_month='';
error_scheme='';
$('#go').click(function(){
if($.trim($('#lot_year').val()).length == 0)
{
    error_lot_year = 'Financial Year is Required';
    $('#error_lot_year').text(error_lot_year);
    $('#error_lot_year').next().find('.select2-selection').addClass('has-error');
}
else
{
error_lot_year = '';
$('#error_lot_year').text(error_lot_year);
$('#error_lot_year').next().find('.select2-selection').removeClass('has-error');
}

if($.trim($('#lot_month').val()).length == 0)
{
    error_lot_month = 'Month is Required';
    $('#error_lot_month').text(error_lot_month);
    $('#error_lot_month').next().find('.select2-selection').addClass('has-error');
}
else
{
error_lot_month = '';
$('#error_lot_month').text(error_lot_month);
$('#error_lot_month').next().find('.select2-selection').removeClass('has-error');
}

if($.trim($('#scheme').val()).length == 0)
{
    error_scheme = 'Scheme is Required';
    $('#error_scheme').text(error_scheme);
    $('#error_scheme').next().find('.select2-selection').addClass('has-error');
}
else
{
error_scheme = '';
$('#error_scheme').text(error_scheme);
$('#error_scheme').next().find('.select2-selection').removeClass('has-error');
}

if( error_lot_year != '' || error_lot_month !='' || error_scheme!='')
{
   return false;
}
else
{
return true;
}

});

});
</script>


