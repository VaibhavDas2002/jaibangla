  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset("/bower_components/AdminLTE/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info" >
          <p >{{ Auth::user()->username}}  </p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <!-- search form (Optional) -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <!-- Optionally, you can add icons to the links -->
        <li class="active"><a href="{{ url('/backendlogin') }}"><i class="fa fa-link"></i> <span>Dashboard</span></a></li>
        <!-- <li class="active"><a href="{{ url('resetpassword') }}"><i class="fa fa-link"></i> <span>Reset Password</span></a></li> -->
        @php
        if(session()->has('menu')){
        foreach(Session::get('menu') as $mymenu){
        $submenu_yes=0;
        if($mymenu['url_type']==2)
        $url_type = 'route';
        else
        $url_type = 'url';
        if($mymenu['parent_id']){
        $submenu_yes=1;
        }
        @endphp
        @if($submenu_yes)
        <li><a href="{{ $url_type (''.$mymenu['url'])}}" ><i class="{{$mymenu['icon']}}"></i> <span>{{$mymenu['name']}}</span></a></li> 
        @endif
        }
        }
        @endphp
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>