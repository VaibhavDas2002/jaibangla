@extends('layouts.app-template-datatable')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <?php
      // $pensioner_type='';
      // if($schemetype=='O')
      // $pensioner_type = '[ Type: ONLINE ]';
      // elseif($schemetype=='Q')
      // $pensioner_type = '[ Type: QUOTA ]';
    ?>
   
      
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection