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
                                    <!-- <option value="{{ url('workflow') }}?pr1=sc">Toposili Bandhu(for SC)</option>
                                    <option value="{{ url('workflow') }}?pr1=st">Jai Johar(for ST)</option> -->
                                    <!-- <option value="{{ url('workflow') }}?pr1=prachesta">Prachesta</option> -->
                                    <option value="{{ url('exchangeflow') }}?pr1=wcd&wcd_type=10">Old Age Pension WCD</option>
                                    <option value="{{ url('exchangeflow') }}?pr1=wcd&wcd_type=11">Widow Pension WCD</option>
                                    <option value="{{ url('exchangeflow') }}?pr1=wcd&wcd_type=2">Manabik for WCD</option>
                                    <!-- <option value="#" disabled >Farmer's Old Age Pension</option>
                                    <option value="{{ url('workflow') }}?pr1=fisherman">Old Age Pension for Fishermen</option>
                                    <option value="#" disabled >Old Age Pension for Artisans and Handloom Weavers</option>
                                    <option value="#" disabled >Lok Prasar Prakalpa</option>
                                     -->                      
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


