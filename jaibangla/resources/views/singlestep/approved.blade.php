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
                            <label for="scheme" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <select onchange="la(this.value)" class="form-control select2" name="scheme"  id="scheme">
                                    <option value="">--Select--</option>
                                    <option value="{{ url('singlestep_report') }}/2/A">Manabik [WCD]</option>
                                    <option value="{{ url('singlestep_report') }}/10/A">Old Age Pension [WCD]</option>
                                    <option value="{{ url('singlestep_report') }}/11/A">Widow Pension [WCD]</option>
                                    <option value="{{ url('singlestep_report') }}/12/A">Old Age Pension for ST [WCD]</option>
                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
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


