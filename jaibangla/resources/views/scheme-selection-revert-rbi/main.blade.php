@extends('scheme-selection-revert-rbi.rbi_failed_base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
	    @if(Session::has('error'))
                <p class="alert alert-danger successErrorMessage">
                <font size="3">{{ Session::get('error') }}</font></p>
            @endif
            @if(Session::has('success'))
                <p class="alert alert-success successErrorMessage">
                <font size="3">{{ Session::get('success') }}</font></p>
            @endif
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
                                    <option value="{{ url('revert-rbi') }}?pr1=sc">Toposili Bandhu(for SC)</option>
                                    <option value="{{ url('revert-rbi') }}?pr1=st">Jai Johar(for ST)</option>
                                    <option value="{{ url('revert-rbi') }}?pr1=manabik">Manabik</option>

                                    <option value="{{ url('revert-rbi') }}?pr1=oap_wcd">WCD Old Age Pension</option>
                                    <option value="{{ url('revert-rbi') }}?pr1=wp_wcd" >WCD Widow Pension</option>
                                    <option value="{{ url('revert-rbi') }}?pr1=foap">Farmer's Old Age Pension</option>
                                    <option value="{{ url('revert-rbi') }}?pr1=fisherman">Old Age Pension for Fishermen</option>
                                    <option value="{{ url('revert-rbi') }}?pr1=weaver">Old Age Pension for Artisans and Handloom Weavers</option>
                                    <option value="{{ url('revert-rbi') }}?pr1=lpp" >Lok Prasar Prakalpa</option>
                                    <option value="{{ url('revert-rbi') }}?pr1=oap_st" >Old Age Pension for ST (Legacy Data)</option>
                                    
                                                          
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


