<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use App\Designation;
use App\Menu_item_master;
use App\Menu_designation_mapping;
use Carbon;
use Illuminate\Support\Facades\Storage;
use Session;
use Illuminate\Support\Facades\Validator;

class MenuManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('Admin');
    }

    public function menu_index()
    {
        try{
        $designation = Designation::get(['id', 'name'])->toArray();
        return view('menu-mgmt/menu_index')->with('roles', $designation);
        }
        catch (\Exception $e) {
            dd($e);
          }
    }

    public function getMenuList(Request $request)
    {
        if (request()->ajax()) {
            $limit = $request->input('length');
            $offset = $request->input('start');
            $serachvalue = $request->search['value'];
            $totalRecords = 0;
            $filterRecords = 0;
            if (empty($serachvalue)) {
                $menuArray = Menu_item_master::with('childMenu')->orderby('id')->offset($offset)->limit($limit)->get(['id', 'menu_name', 'parent_id', 'icon', 'link_url', 'url_type', 'rank', 'is_active', 'menu_class']);
                $totalRecords = Menu_item_master::count();
                $filterRecords = count($menuArray);
            } else {
                $likeParameter = "%" . strtolower($serachvalue) . "%";
                $menuArray = Menu_item_master::with('childMenu')->orderby('id')->offset($offset)->limit($limit)->where(DB::RAW('lower(menu_name)'), 'LIKE', $likeParameter)->get();
                $totalRecords = $filterRecords = count($menuArray);
            }
            return datatables()
                ->of($menuArray)
                ->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('url_type', function ($menuArray) {
                    if ($menuArray->url_type == '1') {
                        return "URL";
                    } elseif ($menuArray->url_type == '2') {
                        return "ROUTE";
                    } else return $menuArray->url_type;
                })
                ->addColumn('is_active', function ($menuArray) {
                    return ($menuArray->is_active == true) ? '<button class="glyphicon glyphicon-ok" onClick="toggleActivate(1,' . $menuArray->id . ')"></button>'
                        : '<button class="glyphicon glyphicon-remove" onClick="toggleActivate(1,' . $menuArray->id . ')"></button>';
                })
                ->addColumn('action', function ($menuArray) {
                    // $action = '<a href="javascript:void(0)" class="btn btn-warning col-md-3 btn-margin" onClick="CreatemenuForm('.$menuArray->id.')">Update</a>';
                    $action = '<button class="btn btn-warning ben_view_button" onClick="CreatemenuForm(' . $menuArray->id . ')">Update</button>';
                    return $action;
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }
        return view('menu-mgmt.menu_index');
    }

    public function menuItemToggleActivate(Request $request)
    {
        $menuid = $request[trim('menu_id')];
        //$type = 1 Menu_item_master for $type=2 Menu_designation_mapping
        $type = $request[trim('type')];
        $role = $request[trim('role')];
        $modelName = '';
        $search_field = '';
        if ($type == 1) {
            $modelName = 'App\Menu_item_master';
            $search_field = 'id';
        } else if ($type == 2) {
            $modelName = 'App\Menu_designation_mapping';
            $search_field = 'menu_id';
        }
        $mytime = Carbon\Carbon::now();


        $menuItem = $modelName::where($search_field, $menuid)->first();
        $toggleStatus = TRUE;
        if ($menuItem->is_active)
            $toggleStatus = FALSE;

        $modelName::where($search_field, $menuid)->update(['is_active' => $toggleStatus, 'active_deactive_at' => $mytime]);
        if ($type == 2) {
            $menu_contents = $this->updatejson($role);
            $json_data = json_encode($menu_contents);
            Storage::disk('local')->put('menu/' . $role . ".json", $json_data);
        }
        if ($type == 1) {
            $roles = Menu_designation_mapping::where('menu_id', $menuid)->get(['designation_id']);

            foreach ($roles as $role) {
                $menu_contents = $this->updatejson($role->designation_id);
                $json_data = json_encode($menu_contents);
                Storage::disk('local')->put('menu/' . $role->designation_id . ".json", $json_data);
            }
        }
        return "success";
    }

    public function getMenuUsingRole($role)
    {
        $menu_id_arr = [];

        // For Menu List Add remove    
        $menu_list = Menu_designation_mapping::where('designation_id', '=', $role)->where('m_menu_designation_mapping.is_active', TRUE)
            ->orderby('m_menu_designation_mapping.rank')
            ->join('m_menu_item_master', 'm_menu_item_master.id', '=', 'm_menu_designation_mapping.menu_id')
            ->where('m_menu_item_master.is_active', TRUE)
            ->orderBy('m_menu_item_master.rank')
            ->get(['menu_id'])->toArray();

        if (count($menu_list) > 0) {
            foreach ($menu_list as $menuArr) {
                array_push($menu_id_arr, $menuArr['menu_id']);
            }
        }
        $selected_list = Menu_item_master::whereIn('id', $menu_id_arr)->where('is_active', TRUE)->orderby('id')->get(['id', 'menu_name', 'parent_id'])->toArray();
        $not_selected_list = Menu_item_master::whereNotIn('id', $menu_id_arr)->where('is_active', TRUE)->orderby('id')->get(['id', 'menu_name', 'parent_id'])->toArray();

        $menu_contents = [];

        // For Menu Tree View
        $menu_contents = $this->updatejson($role);
        // $parent_menu_list = Menu_designation_mapping::where('designation_id','=',$role)->where('m_menu_designation_mapping.is_active',TRUE)
        //         ->orderby('m_menu_designation_mapping.rank')
        //         ->join('m_menu_item_master', 'm_menu_item_master.id', '=', 'm_menu_designation_mapping.menu_id')
        //         ->whereNull('m_menu_item_master.parent_id')
        //         ->where('m_menu_item_master.is_active',TRUE)
        //         ->orderBy('m_menu_item_master.rank')
        //         ->get(['menu_id','m_menu_item_master.menu_name','designation_id','m_menu_item_master.rank as master_rank','m_menu_designation_mapping.rank as map_rank','m_menu_designation_mapping.is_active as map_is_active',
        //         'm_menu_item_master.is_active as master_is_active','parent_id','menu_name','url_type','link_url','icon','menu_class'])->toArray();

        // foreach($parent_menu_list as $parent_menu){
        //     $menu_contents_item =[];

        //     $child_menu = Menu_designation_mapping::where('designation_id','=',$role)->where('m_menu_designation_mapping.is_active',TRUE)
        //     ->orderby('m_menu_designation_mapping.rank')
        //     ->join('m_menu_item_master', 'm_menu_item_master.id', '=', 'm_menu_designation_mapping.menu_id')
        //     ->whereNotNull('m_menu_item_master.parent_id')
        //     ->where('m_menu_item_master.parent_id',$parent_menu['menu_id'])
        //     ->where('m_menu_item_master.is_active',TRUE)
        //     ->orderBy('m_menu_item_master.rank')
        //     ->get(['menu_id','m_menu_item_master.menu_name','designation_id','m_menu_item_master.rank as master_rank','m_menu_designation_mapping.rank as map_rank','m_menu_designation_mapping.is_active as map_is_active',
        //     'm_menu_item_master.is_active as master_is_active','parent_id','menu_name','url_type','link_url','icon','menu_class'])->toArray();

        //     $menu_contents_item['id'] = $parent_menu['menu_id'];
        //     $menu_contents_item['menu_name']  = $parent_menu['menu_name'];
        //     $menu_contents_item['parent_id']  = $parent_menu['parent_id'];
        //     $menu_contents_item['icon']  = $parent_menu['icon'];
        //     $menu_contents_item['link_url']  = $parent_menu['link_url'];
        //     $menu_contents_item['url_type']  = $parent_menu['url_type'];
        //     $menu_contents_item['child_menu']  = $child_menu;

        //     array_push($menu_contents,$menu_contents_item); 
        // }

        $menuTree = '';
        foreach ($menu_contents as $mymenu) {
            if (empty($mymenu['child_menu'])) {
                $menuTree = $menuTree . '<li><a href="{{ ' . ($mymenu['url_type'] == '2' ? "route('" : "url('") . $mymenu['link_url'] . "') }}" . '>
                <i class="' . $mymenu['icon'] . '"></i> <span>' . $mymenu['menu_name'] . '</span></a></li>';
            } else {
                $menuTree = $menuTree . '<li class="treeview">' .
                    '<a href="' . $mymenu['link_url'] . '"><i class="' . $mymenu['icon'] . '"></i> <span>' . $mymenu['menu_name'] . '</span>
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span></a>
                    <ul class="treeview-menu">';
                foreach ($mymenu['child_menu'] as $mysubmenu) {
                    $menuTree = $menuTree . '<li><a href="{{ ' . ($mysubmenu['url_type'] == '2' ? "route('" : "url('") . $mysubmenu['link_url'] . "') }}" . '>
                    <i class="' . $mysubmenu['icon'] . '"></i> <span>' . $mysubmenu['menu_name'] . '</span></a></li>';
                }
                $menuTree = $menuTree . '</ul>
                </li>';
            }
        }
        $selected_menu = '';
        $not_selected_menu = '';

        foreach ($selected_list as $item) {
            $flag = '';
            if ($item['parent_id'] == null) {
                $flag = '<button type="button" class="btn btn-warning">P</button>&nbsp;&nbsp;';
            } else {
                $flag = '<button type="button" class="btn btn-primary">C - <span class="badge">' . $item['parent_id'] . '</span></button>&nbsp;&nbsp;';
            }
            $selected_menu = $selected_menu . '<li class="list-group-item" value="' . $item['id'] . '">' . $flag . $item['id'] . '-' . $item['menu_name'] . '</li>';
        }

        foreach ($not_selected_list as $item) {
            $flag = '';
            if ($item['parent_id'] == null) {
                $flag = '<button type="button" class="btn btn-warning">P</button>&nbsp;&nbsp;';
            } else {
                $flag = '<button type="button" class="btn btn-primary">C - <span class="badge">' . $item['parent_id'] . '</span></button>&nbsp;&nbsp;';
            }
            $not_selected_menu = $not_selected_menu . '<li class="list-group-item" value="' . $item['id'] . '">' . $flag . $item['id'] . '-' . $item['menu_name'] . '</li>';
        }


        $json_arr = array();
        $json_arr[0] = $selected_menu;
        $json_arr[1] = $not_selected_menu;
        $json_arr[2] = $menuTree;
        return response()->json($json_arr);
    }

    public function addRemoveMenuItemUserRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required|integer',
            'user_role' => 'required',
            'request_type' => 'required',
        ]);

        $request_type = $request[trim('request_type')];
        $menuId = $request[trim('menu_id')];
        $userRole = $request[trim('user_role')];
        $mytime = Carbon\Carbon::now();

        if ($request_type == 'add') {
            $menu_role_map = Menu_designation_mapping::whereIn('menu_id', $menuId)->where('designation_id', $userRole)->first();
            if ($menu_role_map != null) {
                if ($menu_role_map->is_active == FALSE) {
                    Menu_designation_mapping::whereIn('menu_id', $menuId)->where('designation_id', $userRole)
                        ->update(['is_active' => TRUE, 'active_deactive_at' => $mytime]);
                }
            } else {
                for ($i = 0; $i < count($menuId); $i++) {
                    $insertarrmap = array(
                        'menu_id' => $menuId[$i],
                        'designation_id' => $userRole,
                        'is_active' => TRUE,
                        'created_at' => $mytime
                    );
                    $save = Menu_designation_mapping::create($insertarrmap);
                }
            }
        }
        if ($request_type == 'remove') {
            Menu_designation_mapping::whereIn('menu_id', $menuId)->where('designation_id', $userRole)
                ->update(['is_active' => FALSE, 'active_deactive_at' => $mytime]);
        }

        // $menu_id_arr=[];  

        // $assign_menu_list = Menu_designation_mapping::where('designation_id','=',$userRole)->where('is_active', TRUE)->get(['menu_id'])->toArray();
        // if(count($assign_menu_list)>0){
        //     foreach ($assign_menu_list as $menuArr){
        //         array_push($menu_id_arr,$menuArr['menu_id']);
        //     }
        // }
        // $menu_contents = Menu_item_master::whereNull('parent_id')->whereIn('id',$menu_id_arr)->where('is_active',TRUE)
        //     ->with(array('childMenu'=>function($query)use($menu_id_arr){
        //         $query->whereIn('id',$menu_id_arr);
        //         $query->orderby('rank');
        //     }))->orderby('rank')->get()->toArray();
        $menu_contents = $this->updatejson($userRole);

        $menuTree = '';
        foreach ($menu_contents as $mymenu) {
            if (empty($mymenu['child_menu'])) {
                $menuTree = $menuTree . '<li><a href="' . ($mymenu['url_type'] == '2' ? 'route'('' . $mymenu['link_url']) : 'url'('' . $mymenu['link_url'])) . '" ><i class="' . $mymenu['icon'] .
                    '"></i> <span>' . $mymenu['menu_name'] . '</span></a></li>';
            } else {
                $menuTree = $menuTree . '<li class="treeview">' .
                    '<a href="' . $mymenu['link_url'] . '"><i class="' . $mymenu['icon'] . '"></i> <span>' . $mymenu['menu_name'] . '</span>
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span></a>
                    <ul class="treeview-menu">';
                foreach ($mymenu['child_menu'] as $mysubmenu) {
                    $menuTree = $menuTree . '<li><a href="' . ($mysubmenu['url_type'] == 2 ? 'route' : 'url'('' . $mysubmenu['link_url'])) . '" >
                    <i class="' . $mysubmenu['icon'] . '"></i> <span>' . $mysubmenu['menu_name'] . '</span></a></li>';
                }
                $menuTree = $menuTree . '</ul>
                </li>';
            }
        }

        $json_data = json_encode($menu_contents);
        Storage::disk('local')->put('menu/' . $userRole . ".json", $json_data);

        return $menuTree;
    }

    public function search(Request $request)
    {
        $designationarr = Designation::get()->toArray();
        $designation_id = $request[trim('designation_id')];
        $assign_menu_list = Menu_designation_mapping::where('designation_id', '=', $designation_id)->get(['menu_id'])->toArray();
        if (count($assign_menu_list) > 0) {
            $menu_id_arr = [];
            foreach ($assign_menu_list as $menuArr) {
                array_push($menu_id_arr, $menuArr['menu_id']);
            }
        }
        $mapmenuArr = Menu_item_master::whereNull('parent_id')->whereIn('id', $menu_id_arr)->where('is_active', TRUE)->with(array('childMenu' => function ($query) use ($menu_id_arr) {
            $query->whereIn('id', $menu_id_arr);
        }))->orderby('rank')->get()->toArray();
        $i = 0;
        $menu_list = array();
        foreach ($mapmenuArr as $menu_item) {
            $menu_list[$i]['id'] = $menu_item['id'];
            $menu_list[$i]['menu_name'] = $menu_item['menu_name'];
            $menu_list[$i]['parent'] = 'NULL';
            $menu_list[$i]['no_of_submenu'] = sizeof($menu_item['child_menu']);
            if (count($menu_item['child_menu']) > 0) {
                $menu_list[$i]['can_delete'] = 0;
                $i++;
                foreach ($menu_item['child_menu'] as $sub_menu_item) {
                    $menu_list[$i]['id'] = $sub_menu_item['id'];
                    $menu_list[$i]['menu_name'] = $sub_menu_item['menu_name'];
                    $menu_list[$i]['parent'] = $menu_item['menu_name'];
                    $menu_list[$i]['can_delete'] = 1;
                    $i++;
                }
            } else {
                $menu_list[$i]['can_delete'] = 1;
            }
            $i++;
        }
        return view('menu-mgmt/index', ['menus' => $menu_list, 'select_designation' => $designation_id, 'designations' => $designationarr]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designation_id' => 'required',
            'menu_name' => 'required|max:50',
            'parent_id' => 'nullable | integer',
            'menu_icon' => 'required|max:50',
            'link_url' => 'required|max:100',
            'url_type' => 'required|integer',
            'menu_class' => 'nullable|max:50',
            'menu_slug' => 'nullable|max:50',
            'rank' => 'nullable | integer'

        ]);


        if ($validator->passes()) {
            $mytime = Carbon\Carbon::now();
            //echo $mytime;die;
            if ($request['rank'] == '') {
                $rank = Menu_item_master::max('rank') + 1;
            } else
                $rank = $request['rank'];
            $id = $request['id'];
            $designation_id = $request['designation_id'];
            $pre_designation_list = array();
            if ($id) {
                $mymsg = "updated";
                $menu_details_app = Menu_designation_mapping::where('menu_id', $id)->where('is_active', TRUE)->get(['id', 'designation_id'])->toArray();
                foreach ($menu_details_app as $map) {
                    array_push($pre_designation_list, $map['designation_id']);
                }
                if ($pre_designation_list == $designation_id) {
                    $combine_arr = $designation_id;
                    $same_mapping = 1;
                    $return_msg = "same";
                } else {
                    $combine_arr = array_unique(array_merge($pre_designation_list, $designation_id));
                    $same_mapping = 0;
                    $return_msg = "diff";
                }
            } else {
                $combine_arr = $designation_id;
                $same_mapping = 0;
                $mymsg = "added";
            }
            $insert_status_main = $insert_staus_map = 0;
            DB::beginTransaction();
            if ($id) {
                $insert_status_main = $id;
                $updatearrmain = array(
                    'menu_name' => $request['menu_name'],
                    'parent_id' => $request['parent_id'],
                    'icon' => $request['menu_icon'],
                    'link_url' => $request['link_url'],
                    'url_type' => $request['url_type'],
                    'menu_class' => $request['menu_class'],
                    'menu_slug' => $request['menu_slug'],
                    'rank' => $rank,
                    'is_active' => TRUE,
                    'updated_at' => $mytime
                );
                $update_status = Menu_item_master::where('id', $id)->update($updatearrmain);
                if ($same_mapping == 0)
                    $delete_status = Menu_designation_mapping::where('menu_id', $id)->delete();
                else
                    $delete_status = 1;
            } else {
                $insertarrmain = array(
                    'menu_name' => $request['menu_name'],
                    'parent_id' => $request['parent_id'],
                    'icon' => $request['menu_icon'],
                    'link_url' => $request['link_url'],
                    'url_type' => $request['url_type'],
                    'menu_class' => $request['menu_class'],
                    'menu_slug' => $request['menu_slug'],
                    'rank' => $rank,
                    'is_active' => TRUE,
                    'created_at' => $mytime
                );
                $insert_status_main = Menu_item_master::create($insertarrmain)->id;
                $delete_status = 1;
                $update_status = 1;
            }
            if ($same_mapping == 0) {
                if (count($designation_id) > 0) {
                    $i = 0;
                    foreach ($designation_id as $designation) {
                        $insertarrmap = array(
                            'menu_id' => $insert_status_main,
                            'designation_id' => $designation,
                            'rank' => $rank,
                            'is_active' => TRUE,
                            'created_at' => $mytime
                        );
                        $save = Menu_designation_mapping::create($insertarrmap);
                        if (!empty($save->menu_id)) {
                            $i++;
                        }
                    }
                    if (count($designation_id) == $i)
                        $insert_staus_map = 1;
                    else
                        $insert_staus_map = 0;
                } else {
                    $insert_staus_map = 1;
                }
            } else {
                $insert_staus_map = 1;
            }
            if ($insert_status_main && $insert_staus_map && $update_status && $delete_status) {
                DB::commit();
                if (count($combine_arr) > 0) {
                    foreach ($combine_arr as $designation) {
                        $menu_contents = $this->updatejson($designation);
                        $json_data = json_encode($menu_contents);
                        Storage::disk('local')->put('menu/' . $designation . ".json", $json_data);
                    }
                }
                $return_status = 1;
                $return_text = "Menu has been successfully " . $mymsg;
                $return_msg = array("" . $return_text);
                Session::flash('message', $return_text);
                // session()->put('message',$return_text);
            } else {
                DB::rollBack();
                $return_status = 0;
                $return_text = "Error Occur .. Please try again";
                $return_msg = array("" . $return_text);
                Session::flash('error', $return_text);
                // session()->put('error', $return_text);
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }

    public function destroy(Request $request)
    {
        $id = $request['id'];
        if ($id != '' && ctype_digit($id)) {
            $menu_details = Menu_designation_mapping::where('menu_id', $id)->where('is_active', TRUE)->get(['id', 'designation_id'])->toArray();
            if (count($menu_details) > 0) {
                DB::beginTransaction();
                try {
                    Menu_designation_mapping::where('menu_id', $id)->delete();
                    Menu_item_master::where('id', $id)->delete();
                    DB::commit();
                    $menu_contents = $this->updatejson($menu_details[0]['designation_id']);
                    $json_data = json_encode($menu_contents);
                    Storage::disk('local')->put('menu/' . $menu_details[0]['designation_id'] . ".json", $json_data);

                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    //echo $e;die;
                    DB::rollBack();
                }

                if ($success) {
                    $message = "Menu has been successfully deleted";
                    return redirect('/menu-management')->with('message', $message);
                } else {
                    $message = "Error..Please try again";
                    return redirect('/menu-management')->with('error', $message);
                }
            } else {
                $message = "Menu id not found";
                return redirect('/menu-management')->with('error', $message);
            }
        } else {
            $message = "Menu id not found";
            return redirect('/menu-management')->with('error', $message);
            // echo "$test does not have all digits. \n";
        }

        return redirect()->intended('/employee-management')->with('users', $users);
    }

    public function getdesignationListfromMenu($menu_id)
    {
        $assign_designation_list = Menu_designation_mapping::where('menu_id', '=', $menu_id)->get(['designation_id'])->toArray();
        return response()->json($assign_designation_list);
    }
    public function loadMenuItemFormMaster()
    {
        $json_arr = array();

        $designation = Designation::get()->toArray();
        $designationmenu = "";
        foreach ($designation as $item) {
            $designationmenu = $designationmenu . '<option value="' . $item['name'] . '">' . $item['name'] . '</option>';
        }

        $parent_menu = Menu_item_master::whereNull('parent_id')->where('is_active', TRUE)->orderby('rank')->get(['id', 'menu_name'])->toArray();

        $parent_menu_option = "<option value=''>No Parent Menu</option>";
        foreach ($parent_menu as $item) {
            $parent_menu_option = $parent_menu_option . '<option value="' . $item['id'] . '">' . $item['menu_name'] . '</option>';
        }

        $json_arr[0] = $designationmenu;
        $json_arr[1] = $parent_menu_option;

        return response()->json($json_arr);
    }
    public function getdeMenuDetails($id)
    {
        $json_arr = array();

        $designation = Designation::get()->toArray();
        $designationmenu = "";
        foreach ($designation as $item) {
            $designationmenu = $designationmenu . '<option value="' . $item['name'] . '">' . $item['name'] . '</option>';
        }

        $parent_menu = Menu_item_master::whereNull('parent_id')->where('is_active', TRUE)->orderby('rank')->get(['id', 'menu_name'])->toArray();

        $parent_menu_option = "<option value=''>No Parent Menu</option>";
        foreach ($parent_menu as $item) {
            $parent_menu_option = $parent_menu_option . '<option value="' . $item['id'] . '">' . $item['menu_name'] . '</option>';
        }

        $menu_details = Menu_item_master::where('id', $id)->get(['id', 'menu_name', 'parent_id', 'icon', 'link_url', 'url_type', 'menu_class', 'menu_slug', 'rank'])->toArray();
        $menu_details_app = Menu_designation_mapping::where('menu_id', $id)->where('is_active', TRUE)->get(['id', 'designation_id'])->toArray();
        $json_arr[0] = $menu_details[0];
        $json_arr[1] = $menu_details_app;
        $json_arr[2] = $designationmenu;
        $json_arr[3] = $parent_menu_option;

        return response()->json($json_arr);
    }
    private function updatejson($role)
    {
        $menu_contents = [];

        // For Menu Tree View
        $parent_menu_list = Menu_designation_mapping::where('designation_id', '=', $role)->where('m_menu_designation_mapping.is_active', TRUE)
            ->orderby('m_menu_designation_mapping.rank')
            ->join('m_menu_item_master', 'm_menu_item_master.id', '=', 'm_menu_designation_mapping.menu_id')
            ->whereNull('m_menu_item_master.parent_id')
            ->where('m_menu_item_master.is_active', TRUE)
            ->orderBy('m_menu_item_master.rank')
            ->get([
                'menu_id', 'm_menu_item_master.menu_name', 'designation_id', 'm_menu_item_master.rank as master_rank', 'm_menu_designation_mapping.rank as map_rank', 'm_menu_designation_mapping.is_active as map_is_active',
                'm_menu_item_master.is_active as master_is_active', 'parent_id', 'menu_name', 'url_type', 'link_url', 'icon', 'menu_class'
            ])->toArray();

        foreach ($parent_menu_list as $parent_menu) {
            $menu_contents_item = [];

            $child_menu = Menu_designation_mapping::where('designation_id', '=', $role)->where('m_menu_designation_mapping.is_active', TRUE)
                ->orderby('m_menu_designation_mapping.rank')
                ->join('m_menu_item_master', 'm_menu_item_master.id', '=', 'm_menu_designation_mapping.menu_id')
                ->whereNotNull('m_menu_item_master.parent_id')
                ->where('m_menu_item_master.parent_id', $parent_menu['menu_id'])
                ->where('m_menu_item_master.is_active', TRUE)
                ->orderBy('m_menu_item_master.rank')
                ->get([
                    'menu_id', 'm_menu_item_master.menu_name', 'designation_id', 'm_menu_item_master.rank as master_rank', 'm_menu_designation_mapping.rank as map_rank', 'm_menu_designation_mapping.is_active as map_is_active',
                    'm_menu_item_master.is_active as master_is_active', 'parent_id', 'menu_name', 'url_type', 'link_url', 'icon', 'menu_class'
                ])->toArray();

            $menu_contents_item['id'] = $parent_menu['menu_id'];
            $menu_contents_item['menu_name']  = $parent_menu['menu_name'];
            $menu_contents_item['parent_id']  = $parent_menu['parent_id'];
            $menu_contents_item['icon']  = $parent_menu['icon'];
            $menu_contents_item['link_url']  = $parent_menu['link_url'];
            $menu_contents_item['url_type']  = $parent_menu['url_type'];
            $menu_contents_item['child_menu']  = $child_menu;

            array_push($menu_contents, $menu_contents_item);
        }
        return $menu_contents;
        //   $json_data = json_encode($menu_contents);
        //  Storage::disk('local')->put('menu/'.$role.".json", $json_data);

    }
    private function validateInput($request)
    {
        $this->validate($request, [
            'designation_id' => 'required',
            'menu_name' => 'required|max:50',
            'parent_id' => 'nullable | integer',
            'menu_icon' => 'required|max:50',
            'link_url' => 'required|max:50',
            'url_type' => 'required|integer',
            'menu_class' => 'nullable|max:50',
            'rank' => 'nullable | integer'
        ]);
    }

    public function getMenuItemFromRole(Request $request)
    {
        $role = $request['role'];


        $data = Menu_designation_mapping::where('designation_id', '=', $role)
            ->orderby('m_menu_designation_mapping.rank')
            ->join('m_menu_item_master', 'm_menu_item_master.id', '=', 'm_menu_designation_mapping.menu_id')
            ->where('m_menu_item_master.is_active', TRUE)
            ->orderBy('m_menu_item_master.rank')
            ->get([
                'menu_id', 'm_menu_designation_mapping.rank as map_rank', 'm_menu_designation_mapping.is_active as map_is_active',
                'menu_name', 'link_url', 'parent_id'
            ]);


        return datatables()
            ->of($data)
            ->addColumn('rank', function ($data) {
                return '<input type="text" name="map_rank_val" value="' . $data->map_rank . '" onchange="changeMapItemRank(' . $data->menu_id . ',this.value)">';
            })
            ->addColumn('is_active', function ($data) {
                return ($data->map_is_active == 1) ? '<button class="glyphicon glyphicon-ok" onClick="toggleActivate(2,' . $data->menu_id . ')"></button>'
                    : '<button class="glyphicon glyphicon-remove" onClick="toggleActivate(2,' . $data->menu_id . ')"></button>';
            })
            ->rawColumns(['is_active', 'rank'])
            ->make(true);
    }

    public function updateRoleBasedMenuRank(Request $request)
    {
        //$action_type = $request->type;
        $menu_id = $request->menu_id;
        $rank = $request->rank;
        $role = $request->role;



        Menu_designation_mapping::where('menu_id', $menu_id)->update(['rank' => $rank]);

        $menu_contents = $this->updatejson($role);
        $json_data = json_encode($menu_contents);
        Storage::disk('local')->put('menu/' . $role . ".json", $json_data);

        return "success";
    }
}
