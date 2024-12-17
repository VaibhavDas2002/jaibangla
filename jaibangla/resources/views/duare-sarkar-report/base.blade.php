@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Duare Sarkar Report<small>দুয়ারে সরকার</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Duare Sarkar Report</a></li>
        <!-- <li class="active">Duplicate Approve</li> -->
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection