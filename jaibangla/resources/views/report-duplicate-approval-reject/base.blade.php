@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Report Duplicate Rejected
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Report Duplicate Rejected</a></li>
        <!-- <li class="active">Scheme</li> -->
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection