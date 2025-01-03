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
        $designation_id = Auth::user()->designation_id;
        @endphp
        
        @if(Storage::exists('menu/'.$designation_id.".json"))
        @php
        $menu_contents =json_decode(Storage::disk('local')->get('menu/'.$designation_id.'.json'),JSON_FORCE_OBJECT);
        @endphp 
        
        @foreach($menu_contents as $mymenu)
        @if(empty($mymenu['child_menu']))
         <li><a href="{{ $mymenu['url_type'] == '2' ? 'route' (''.$mymenu['link_url']) : 'url' (''.$mymenu['link_url'])}}" ><i class="{{$mymenu['icon']}}"></i> <span>{{$mymenu['menu_name']}}</span></a></li> 
        @else 
        <li class="treeview">
          <a href="{{$mymenu['link_url']}}"><i class="{{$mymenu['icon']}}"></i> <span>{{$mymenu['menu_name']}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          @foreach($mymenu['child_menu'] as $mysubmenu)
          <li><a href="{{ $mysubmenu['url_type'] == 2 ? 'route' (''.$mysubmenu['link_url']) : 'url' (''.$mysubmenu['link_url'])}}" ><i class="{{$mysubmenu['icon']}}"></i> <span>{{$mysubmenu['menu_name']}}</span></a></li> 
          @endforeach 
          </ul>

        </li>
        @endif
        @endforeach   
        @endif
        </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>