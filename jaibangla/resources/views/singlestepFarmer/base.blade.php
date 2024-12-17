@extends('layouts.app-template-datatable')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <?php
      $pensioner_type='';
      if($schemetype=='O')
      $pensioner_type = 'ONLINE';
      elseif($schemetype=='Q')
      $pensioner_type = 'QUOTA';
    ?>
      <h1>
        Scheme Name: {{$scheme_name}} [ Type: {{$pensioner_type}} ]
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-clock-o"></i><b> Date: </b></a></li>
        <li class="active">{{date('d-m-Y')}}</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection