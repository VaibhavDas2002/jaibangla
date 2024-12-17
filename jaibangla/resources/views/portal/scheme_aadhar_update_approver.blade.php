@extends('portal.scheme_base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Select Scheme</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        {{ csrf_field() }} 

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <select onchange="la(this.value)" class="form-control select2" name="scheme"  id="scheme">
                                    <option value="">--Select--</option>
                                    @foreach($approverObj as $approver)
                                        <option value="{{ url('aadhar-update-list-approver') }}?scheme_id={{ $approver->scheme_id }}">{{ $approver->scheme_name }}</option>
                                    @endforeach
                                  


                                    <!-- <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=3">Toposili Bandhu(for SC)</option>
                                    <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=1">Jai Johar(for ST)</option>
                                    <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=5" >Old Age Pension for Fishermen</option>

                                     <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=6" >Old Age Pension to Handicrafts and Village Industries Artisans</option>
                                    <option value="{{ url('application-list-aadhar-update') }}?scheme_id=7" >The West Bengal Pension Rules for weavers, 1990</option>

                                    <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=10">WCD Old Age Pension</option>
                                    <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=11">WCD Widow Pension</option>
                                    <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=2">WCD Manabik</option>                  

                                  

                                    <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=13" >Farmer's Old Age Pension</option>

                                     <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=17">Purohits Monthly Financial Assistance</option>
                                    <option value="{{ url('aadhar-update-list-approver') }}?scheme_id=18">Housing Scheme for Purohits</option> -->

                                    <!-- <option value="{{ url('application-list-read-only') }}?pr1=purohitmonthly">Purohits Monthly Financial Assistance</option>
                                    <option value="{{ url('application-list-read-only') }}?pr1=purohithousing">Housing Scheme for Purohits</option>
                                    <option value="#" disabled >Lok Prasar Prakalpa</option> -->
                                                          
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


