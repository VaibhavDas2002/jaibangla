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
        @php
        $isLifeCertificate=0;
        $incDetailsEntry=0;
        $special_cases=0;
        $marking_ds_8=0;
        $user_id = Auth::user()->id;
        $mapObj = DB::table('public.duty_assignement')->where('user_id',$user_id)->first();
        @endphp
        @if($mapObj->district_code == 315 && $mapObj->urban_body_code == 99999 && Auth::user()->designation_id_old == 'Operator') 
          <li><a href="{{ route('ben-payment-status') }}"><i class="fa fa-link"></i> <span>Beneficiary Payment Status</span></a></li>
        @endif

        @if($mapObj->district_code == 304 && Auth::user()->designation_id_old == 'Approver' && $user_id == 3606) 
          <li><a href="{{ url('inactive-special') }}"><i class="fa fa-link"></i> <span>Re-activate Beneficiary</span></a></li>
        @endif
        
        @php
        if($mapObj->district_code == 315 && (Auth::user()->designation_id_old == 'Verifier' || Auth::user()->designation_id_old == 'Approver')) {
         $special_cases=1;
        }
        if($mapObj->district_code == 315 && (Auth::user()->designation_id_old == 'Verifier')) {
         $marking_ds_8=1;
        }
        $designation_id_old = Auth::user()->designation_id_old;
        if($designation_id_old=='Operator'){
          $district_code=$mapObj->district_code;
          if($mapObj->is_urban==1){
            $block_ulb_code=$mapObj->urban_body_code;
          }
          else{
            $block_ulb_code=$mapObj->taluka_code;
          }
          $specialEntry = DB::table('public.m_block_urban_entry_mapping')->where('special_entry',TRUE)->where('district_code',$district_code)->where('block_ulb_code',$block_ulb_code)->where('is_urban',$mapObj->is_urban)->count();
        }
        else{
          $specialEntry =0;
        }
        $mapObjSchemeList = DB::table('public.duty_assignement')->where('is_active',1)->where('user_id',$user_id)->pluck('scheme_id')->toarray();
        if(in_array(17,$mapObjSchemeList) && ($designation_id_old=='Operator' || $designation_id_old=='Verifier') ){
          $isLifeCertificate=1;
        }
        @endphp
        @php
       if((in_array(17,$mapObjSchemeList) || in_array(8,$mapObjSchemeList) || in_array(9,$mapObjSchemeList)) && ($designation_id_old=='Approver') ){
          $incDetailsEntry=1;
        }
        @endphp
        @if(Storage::exists('menu/'.$designation_id_old.".json"))
        @php
        $menu_contents =json_decode(Storage::disk('local')->get('menu/'.$designation_id_old.'.json'),JSON_FORCE_OBJECT);
        @endphp 
        
        @foreach($menu_contents as $mymenu)
        @if($mymenu['menu_name']=='Life Certificate' && $isLifeCertificate==0)
        @continue;
        @endif
        @if($mymenu['menu_name']=='Beneficiary Details Entry' && $incDetailsEntry==0)
        @continue;
        @endif
        @if($mymenu['id']==314 && $special_cases==0)
        @continue;
        @endif
        @if(empty($mymenu['child_menu']))
        @if($mymenu['menu_name']=='Jai Bangla Form(Special Quota)')
        @if($specialEntry==0)
        @continue;
        @endif
        @endif
      
         <li><a href="{{ $mymenu['url_type'] == '2' ? 'route' (''.$mymenu['link_url']) : 'url' (''.$mymenu['link_url'])}}" ><i class="{{$mymenu['icon']}}"></i> <span>{{$mymenu['menu_name']}}</span></a></li> 
        @else 
        <li class="treeview">
          <a href="{{$mymenu['link_url']}}"><i class="{{$mymenu['icon']}}"></i> <span>
            
            {{$mymenu['menu_name']}}
           
          </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          @foreach($mymenu['child_menu'] as $mysubmenu)
          @if($mysubmenu['menu_name']=='Mark as Duare Sarkar VII')
          @if($marking_ds_8==0)
          @continue;
          @endif
          @endif
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