@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Report Repeat Lot Master
      </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-dashboard"></i> Report Repeat Lot Master</a></li>
        <!-- <li class="active"></li> -->
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection