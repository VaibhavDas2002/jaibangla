@extends('report-duplicate-stop-payment.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Duplicate and Stop Payment</div> 
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('linelisting-duplicate-stop-report') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('scheme_id') ? ' has-error' : '' }}">
                            <label for="scheme_id" class="col-md-4 control-label">Scheme</label>

                            <div class="col-md-6">
                                <select class="form-control select2" name="scheme_id"  id="scheme_id">
                                    <option value="">--Select--</option>
                                    @foreach ($schemes as $scheme)
                                    <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                    @endforeach
                                                          
                                </select>
                                <span id="error_scheme" class="text-danger"></span>
                            </div>
                        </div> 

                        <div class="form-group{{ $errors->has('month') ? ' has-error' : '' }}">
                            <label for="month" class="col-md-4 control-label">Filter</label>

                            <div class="col-md-6">
                                <select  class="form-control select2" name="month"  id="month">
                                    <option value="">--Select--</option>
                                    <option value="all">All</option>
                                    <option value="current">This Month (<?php print date('F') ?>)</option>
                                                          
                                </select>
                                <span id="error_month" class="text-danger"></span>
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
    error_month='';
    error_scheme='';
    $('#go').click(function(){
        if($.trim($('#month').val()).length == 0)
        {
            error_month = 'Filter is Required';
            $('#error_month').text(error_month);
            $('#error_month').next().find('.select2-selection').addClass('has-error');
        }
        else
        {
        error_month = '';
        $('#error_month').text(error_month);
        $('#error_month').next().find('.select2-selection').removeClass('has-error');
        }

        if($.trim($('#scheme_id').val()).length == 0)
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

        if( error_month !='' || error_scheme!='')
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


