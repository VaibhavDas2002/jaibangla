@extends('layouts.app-template-approver')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Role Management
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Role Management</a></li>
        
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection