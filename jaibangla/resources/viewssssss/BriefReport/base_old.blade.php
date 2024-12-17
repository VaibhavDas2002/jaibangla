@extends('layouts.app-template-datatable')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <?php
      $pensioner_type='';
      if($schemetype=='O')
      $pensioner_type = '[ Type: ONLINE ]';
      elseif($schemetype=='Q')
      $pensioner_type = '[ Type: QUOTA ]';
    ?>
      <h1>
        Scheme Name: {{$scheme_name}} {{$pensioner_type}}
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-clock-o"></i><b> Date: </b></a></li>
        <li class="active"><span id='ct' ></span></li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection