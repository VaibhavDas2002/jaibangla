@extends('BriefReport.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{$report_type_name}}</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        {{ csrf_field() }} 

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-4 control-label">Select Scheme: </label>

                            <div class="col-md-6">
                                <select onchange="la(this.value)" class="form-control">
                                    <option value="">--Select--</option>
                                     <option value="10">Old Age Pension [WCD]</option>
                                    <option value="11">Widow Pension [WCD]</option>
                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
                                                </div>
                        <script>	
                            function la(val)
                            {
                                
                                window.location = "{{ url('/') }}/application-list-common-brief?pr1="+val+"&type={{$type}}"; 
                            }
                            
                        </script>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function display_c(){
    var refresh=1000; // Refresh rate in milli seconds
    mytime=setTimeout('display_ct()',refresh)
}

function display_ct() {
    var x = new Date()
    document.getElementById('ct').innerHTML = x.toUTCString();
    display_c();
} 

$(document).ready(function(){ 
    display_ct();
});
</script>
@endsection



