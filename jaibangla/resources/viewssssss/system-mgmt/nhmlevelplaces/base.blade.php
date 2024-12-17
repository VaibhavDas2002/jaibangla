@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Health Facility Management
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>  Health Facility Management</a></li>
        <li class="active"> Health Facility</li> 
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection