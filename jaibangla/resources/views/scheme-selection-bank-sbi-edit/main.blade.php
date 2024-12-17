@extends('layouts.app-template')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            SBI  Failed
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Payment Failure</a></li>
            <li class="active">SBI </li>
        </ol>
    </section>

    <div class="container">


        <div class="row">

            <div class="col-md-8 col-md-offset-1" style="margin-top:10px;">
                <div class="panel panel-default">
                    <div class="panel-heading">SBI Failed</div>
                    <div class="panel-body">
                        @if(Session::has('error'))
                        <p class="alert {{ Session::get('alert-class', 'alert-info') }} successErrorMessage">
                            {{ Session::get('error') }}</p>
                        @endif
                        @if(Session::has('success'))
                        <p class="alert {{ Session::get('alert-class', 'alert-info') }} successErrorMessage">
                            {{ Session::get('success') }}</p>
                        @endif
                        <form class="form-horizontal" role="form" method="POST" action="">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                                <label for="scheme" class="col-md-4 control-label">Scheme Type</label>

                                <div class="col-md-6">
                                    <select onchange="la(this.value)" class="form-control select2" name="scheme"
                                        id="scheme">

                                        <option value="">--Select--</option>
                                        {{-- <option value="{{ url('bank-edit-sbi') }}?pr1=sc">Toposili Bandhu(for SC)
                                        </option>
                                        <option value="{{ url('bank-edit-sbi') }}?pr1=st">Jai Johar(for ST)</option>
                                        <option value="{{ url('bank-edit-sbi') }}?pr1=manabik">Manabik</option>

                                        <option value="{{ url('bank-edit-sbi') }}?pr1=oap">Old Age Pension</option>
                                        <option value="{{ url('bank-edit-sbi') }}?pr1=wp">Widow Pension</option>
                                        <option value="{{ url('bank-edit-sbi') }}?pr1=lppret">Lok Prasar Prakalpa
                                            Retainer</option>
                                        <option value="{{ url('bank-edit-sbi') }}?pr1=lpppen">Lok Prasar Prakalpa
                                            Pensioner</option>
                                        <option value="{{ url('bank-edit-sbi') }}?pr1=purohit">State Welfare Scheme for
                                            Purohits</option> --}}
                                        <!-- <option value="{{ url('bank-edit-sbi') }}?pr1=lppret">Lok Prasar Prakalpa Retainer</option>
                                        <option value="{{ url('bank-edit-sbi') }}?pr1=lpppen">Lok Prasar Prakalpa Pensioner</option> -->
                                        @if(isset($report))
                                        @foreach($report as $report)


                                        <option
                                            value="{{ url('bank-edit-sbi') }}?pr1={{config('constants.schemeurl.'.$report->id)}}">
                                            {{$report->scheme_name}}</option>
                                        @endforeach
                                        @endif
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
    <!-- /.content -->
</div>
@endsection