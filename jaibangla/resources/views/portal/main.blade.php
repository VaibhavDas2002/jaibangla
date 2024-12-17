@extends('portal.base')
@section('action-content')
@php
        $is_active = 0;
        $oap_code = 10;
        $oap_is_active = 0;
        $wp_is_active = 0;
        // $base_url=url('/');
        // echo $base_url.'/images/';exit;        

        $roleArray = session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $oap_code) {
                $oap_is_active = 1;
                $oap_district_code= $roleObj['district_code'];
                break;
            }
        }
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $oap_code) {
                $wp_is_active = 1;
                $wp_district_code= $roleObj['district_code'];
                break;
            }
        }
if ($oap_is_active == 1 && ($oap_district_code==312 || $oap_district_code==315 )){
    $oap_visible=1;
 }
 else{
     $oap_visible=0;
 }
 if ($wp_is_active == 1 && $wp_district_code==312){
    $wp_visible=1;
 }
 else{
     $wp_visible=0;
 }
 $wp_visible=1;
 @endphp
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
                                    <option value="{{ url('pensionform') }}?pr1=sc">Toposili Bandhu(for SC)</option>
                                    <!-- <option value="{{ url('under_maintainance') }}">Toposili Bandhu(for SC)</option>
 -->
                                    <option value="{{ url('pensionform') }}?pr1=st">Jai Johar(for ST)</option>
                                    <!-- <option value="{{ url('under_maintainance') }}">Jai Johar(for ST)</option>
                                     -->
                                    <!-- <option value="{{ url('prachesta') }}">Prachesta</option>
                                    <option value="{{ url('manabik') }}">Manabik</option> -->
                                    <option value="{{ url('fisherman') }}">Old Age Pension for Fishermen</option>
                                    <option value="{{ url('msme') }}" >Old Age Pension to Handicrafts and Village Industries Artisans</option>
                                    <option value="{{ url('textile') }}" >The West Bengal Pension Rules for weavers, 1990</option>
                                     @if($oap_visible===1)
                                     <option value="{{ url('oap-wcd') }}">WCD Old Age Pension</option> 
                                     @endif
                                      @if($wp_visible===1)
                                     <option value="{{ url('wp-wcd') }}">WCD Widow Pension</option>
                                    @endif
                                    <option value="{{ url('manabik-wcd') }}">WCD Manabik</option>
                                    <!--  <option value="{{ url('lb-wcd') }}">Lokkhir BhanDar</option>-->
                                    <option value="{{ url('purohit') }}">State Welfare scheme for Purohits</option>
                                    <!-- <option value="{{ url('under_maintainance') }}">State Welfare scheme for Purohits</option> -->
                                    <option value="{{ url('oap-farmer') }}" >Farmer's Old Age Pension</option>
                                    <option value="#" disabled >Lok Prasar Prakalpa</option>
                                                          
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


