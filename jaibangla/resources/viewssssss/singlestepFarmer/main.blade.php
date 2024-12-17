@extends('portal.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Select Pension Scheme</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        {{ csrf_field() }} 

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-3 control-label">Select Scheme</label>

                            <div class="col-md-4">
                                <select class="form-control select2" name="scheme"  id="scheme">
                                    <option value="">--Select--</option>
                                    <option value="2">Manabik [WCD]</option>
                                    <option value="10">Old Age Pension [WCD]</option>
                                    <option value="11">Widow Pension [WCD]</option>
                                    <option value="12">Old Age Pension for ST [WCD]</option>
                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('scheme_type') ? ' has-error' : '' }}">    
                            <label for="scheme_type" class="col-md-3 control-label">Scheme Type</label>
                            <div class="col-md-2">    
                                <select class="form-control select2" name="scheme_type"  id="scheme_type">
                                    <option value="O">Online</option>
                                    <option value="Q">Quota</option>
                                </select>
                                <span id="error_construction_type" class="text-danger"></span>
                            </div>    
                        </div>
                        <div class="col-md-9 col-md-offset-3">
                            <button type="button" id="form_submit" class="btn btn-primary" onclick="la()">Proceed</button>    
                        </div>

                        <script>
                            function la()
                            {
                                var scheme = $('#scheme').val();
                                var scheme_type = $('#scheme_type').val();
                                var src= "{{ url('verify') }}/"+scheme+"/"+scheme_type;
                                if(scheme=='12' && scheme_type=='Q'){
                                    alert('No QUOTA Type Beneficiary under this scheme');
                                    return false;
                                }
				/*if(scheme=='2' && scheme_type=='Q'){
                                    alert('Migration is under process');
                                    return false;
                                }*/
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


