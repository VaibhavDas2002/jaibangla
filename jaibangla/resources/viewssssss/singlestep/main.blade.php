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
                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
                        </div>
                        
                        <div class="col-md-9 col-md-offset-3">
                            <button type="button" id="form_submit" class="btn btn-primary" onclick="la()">Proceed</button>    
                        </div>

                        <script>
                            function la()
                            {
                                var scheme = $('#scheme').val();
                                 if(scheme!=''){
                                  var src= "{{ url('verify') }}/"+scheme;
                                  window.location=src;
                                 }
                               
                            }
                            
                        </script>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


