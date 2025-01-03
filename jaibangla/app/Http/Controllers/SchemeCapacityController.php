<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\BeneficiaryPensions;
use App\Block;
use App\District;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\SchemeCapacity;
use App\SubDistrict;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Helpers\AuthChecker;

class SchemeCapacityController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		set_time_limit(200);
	}
	public function index()
	{
		$user_id = AuthChecker::getUserId();
		$schemeObj = Configduty::select('scheme_id')->where('user_id', '=', $user_id)->get();
		$scheme = Scheme::whereIn('id', $schemeObj)->get();
		if (Auth::user()->designation_id == 'HOD' || Auth::user()->designation_id == 'Admin') {
			$district = District::all();
			//$scheme = Scheme::get();
			return view('scheme-capacity/index', ['schemes' => $scheme, 'districts' => $district]);
		} else {
			print 'Invalid User';
		}
	}
	public function listSchemeCap(Request $request)
	{
		$this->validate($request, [
			'scheme_type' => 'required|not-in:0',
			'cap_level' => 'required|not-in:0'
		]);
		$cap_l = $request->cap_level;
		$district = $request->district;
		$scheme_id = $request->scheme_type;
		$is_urban = 0;
		if ($cap_l == 'SD') {
			$is_urban = 1;
		} else if ($cap_l == 'BK') {
			$is_urban = 2;
		}
		$districtObj = District::where('district_code', $district)->first();
		$subDivison = SubDistrict::where('district_code', $district)->get();
		$urban_bodys = UrbanBody::where('district_code', $district)->get();
		$blocks = Block::where('district_code', $district)->get();
		// dd($districtObj->district_name);
		// dd($subDivison);
		// dd($is_urban);
		$sObj = Scheme::where('id', $scheme_id)->first();
		// District

		// dd($sObj->scheme_name,$scheme_id,$is_urban, $blocks,$urban_bodys,$districtObj,$district);
		if ($cap_l == 'D') {
			$capacity = DB::select(DB::raw("SELECT d.district_name AS name, d.district_code AS code, c.capacity FROM m_district d LEFT JOIN m_cap c ON d.district_code = c.district_code AND c.scheme_id = " . $scheme_id . " where c.local_body_code IS null;"));
			return view('scheme-capacity/add_capacity', [
				'scheme_name' => $sObj->scheme_name,
				'cap' => $capacity,
				'scheme_id' => $scheme_id,
				'is_urban' => $is_urban,
				'districtObj' => $districtObj,

			]);
		}
		if ($cap_l == 'SD') {
			$capacity = DB::select(DB::raw("select s.sub_district_name as name,s.sub_district_code as code,(select c.capacity from m_cap c where c.district_code=s.district_code and c.local_body_code = s.sub_district_code    and c.scheme_id= " . $scheme_id . ") from m_sub_district s where district_code = " . $district . ""));
			return view('scheme-capacity/add_capacity', [
				'scheme_name' => $sObj->scheme_name,
				'cap' => $capacity,
				'scheme_id' => $scheme_id,
				'is_urban' => $is_urban,
				'urban_bodys' => $urban_bodys,
				'districtObj' => $districtObj,

			]);
		}
		if ($cap_l == 'BK') {
			$capacity = DB::select(DB::raw("select b.block_name as name,b.block_code as code,(select c.capacity from m_cap c where c.district_code=b.district_code and c.local_body_code = b.block_code  and c.scheme_id= " . $scheme_id . ") from m_block b where district_code = " . $district . ""));
			return view('scheme-capacity/add_capacity', [
				'scheme_name' => $sObj->scheme_name,
				'cap' => $capacity,
				'scheme_id' => $scheme_id,
				'is_urban' => $is_urban,
				'blocks' => $blocks,
				'districtObj' => $districtObj,

			]);
		}


	}
	// Add Capacity
	public function addCapacity(Request $request)
	{
		// dd($request->all());
		// dd($request->special_quota);
		$dist_code = $request->dist_code;
		$capacity = $request->capacity;
		$scheme = $request->scheme_id;
		$is_urban = $request->is_urban;
		$special_quota = $request->special_quota;
		// dd($is_urban);
		if ($is_urban == 0) {
			$dist_code = $request->dist_arr;
		} else if ($is_urban == 1) {
			$urban_code = $request->urban_arr;
		} else if ($is_urban == 2) {
			$block_code = $request->block_arr;
		}
		// dd($urban_code);

		$scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
		if (in_array($scheme, $scheme_dedup_list)) {
			$pending_s_update = 1;
		} else {
			$pending_s_update = 0;
		}
		$final_dist = 0;
		$scheme_obj = Scheme::where('id', $scheme)->where('is_active', 1)->first();
		if (!empty($scheme_obj->short_code)) {
			$schema = $scheme_obj->short_code;
			$scheme_length = $scheme_obj->scheme_length;
			$id_length = $scheme_obj->id_length;
		} else {
			$schema = "pension";
			$scheme_length = NULL;
			$id_length = NULL;
		}
		try {
			$arch_status = DB::statement("INSERT INTO public.m_cap_arch(scheme_id, district_code, local_body_code, capacity, created_at, 
			updated_at, deleted_at, rural_urban_id, user_id, approve, id, special_capacity,updated_on
                               ) (SELECT scheme_id, district_code, local_body_code, capacity, created_at, 
	updated_at, deleted_at, rural_urban_id, user_id, approve, id, special_capacity,'" . date("Y-m-d h:i:s") . "' from public.m_cap)");
			DB::beginTransaction();
			$k = 0;
			//District
			if ($is_urban == 0) {
				for ($i = 0; $i < count($dist_code); $i++) {
					if ($capacity[$i] != '') {
						$arr = explode('_', $dist_code[$i]);
						$row = SchemeCapacity::where('district_code', $arr[2])->where('scheme_id', $scheme)->count();
						if ($row == 0) {
							$input = [
								'scheme_id' => $scheme,
								'district_code' => $arr[2],
								'user_id' => Auth::user()->id,
							];
							if ($special_quota == 1) {
								$input['special_quota'] = $capacity[$i];
							} else {
								$input['capacity'] = $capacity[$i];
							}
							$is_saved = SchemeCapacity::create($input);
						} else {
							$input = [
								'user_id' => Auth::user()->id
							];
							if ($special_quota == 1) {
								$input['special_quota'] = $capacity[$i];
							} else {
								$input['capacity'] = $capacity[$i];
							}
							$is_saved = SchemeCapacity::where('district_code', $arr[2])->where('scheme_id', $scheme)->update($input);

						}
						$final_dist = $final_dist + 1;
						if ($pending_s_update == 1) {
							$free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme . ", in_district_code => " . $arr[2] . ")");
							//dd($free_pending_bank_duplicate_arr);
							$free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
						} else {
							$free_pending_bank_duplicate_data = 1;
						}
						if ($is_saved && $free_pending_bank_duplicate_data == 1) {
							$k++;

						}
					}
				}
				if ($k == count($dist_code)) {
					if ($special_quota == 0) {
						DB::commit();
						return redirect('scheme-capacity')->with('msg', $final_dist . ' districts capacity added successfully');

					} else {
						DB::commit();
						return redirect('scheme-capacity')->with('msg', $final_dist . ' districts Special Quota capacity added successfully');
					}
				} else {
					DB::rollback();
					return redirect('scheme-capacity')->with('msg1', 'Something Wrong !!');
				}
			} //Sub District
			else if ($is_urban == 1) {
				for ($i = 0; $i < count($urban_code); $i++) {
					if ($capacity[$i] != '') {
						$arr = explode('_', $urban_code[$i]);
						$row = SchemeCapacity::where('district_code', $arr[2])->where('scheme_id', $scheme)->count();
						if ($row == 0) {
							$input = [
								'scheme_id' => $scheme,
								'district_code' => $dist_code,
								'local_body_code' => $arr[2],
								'user_id' => Auth::user()->id,
								//'approve' => $approve
							];
							// Insert into the appropriate column based on $special_quota
							if ($special_quota == 1) {
								$input['special_quota'] = $capacity[$i];
							} else {
								$input['capacity'] = $capacity[$i];
							}
							$is_saved = SchemeCapacity::create($input);
						} else {
							$input = [
								'user_id' => Auth::user()->id
							];
							if ($special_quota == 1) {
								$input['special_quota'] = $capacity[$i];
							} else {
								$input['capacity'] = $capacity[$i];
							}
							$is_saved = SchemeCapacity::where('district_code', $arr[2])->where('scheme_id', $scheme)->update($input);

						}
						$final_dist = $final_dist + 1;
						if ($pending_s_update == 1) {
							$free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme . ", in_district_code => " . $arr[2] . ")");
							//dd($free_pending_bank_duplicate_arr);
							$free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
						} else {
							$free_pending_bank_duplicate_data = 1;
						}
						if ($is_saved && $free_pending_bank_duplicate_data == 1) {
							$k++;

						}
					}
				}
				if ($k == count($urban_code)) {
					if($special_quota == 0)
					{
						DB::commit();
						return redirect('scheme-capacity')->with('msg', $final_dist . ' Sub Division capacity added successfully');
					}
					else{
						DB::commit();
						return redirect('scheme-capacity')->with('msg', $final_dist . ' Sub Division Special quota capacity added successfully');
					}
					
				} else {
					DB::rollback();
					return redirect('scheme-capacity')->with('msg1', 'Something Wrong !!');
				}

			} // Block
			else if ($is_urban == 2) {
				for ($i = 0; $i < count($block_code); $i++) {
					if ($capacity[$i] != '') {
						$arr = explode('_', $block_code[$i]);
						$row = SchemeCapacity::where('district_code', $arr[2])->where('scheme_id', $scheme)->count();
						if ($row == 0) {
							$input = [
								'scheme_id' => $scheme,
								'district_code' => $dist_code,
								'local_body_code' => $arr[2],
								'user_id' => Auth::user()->id,
								//'approve' => $approve
							];
							// Insert into the appropriate column based on $special_quota
							if ($special_quota == 1) {
								$input['special_quota'] = $capacity[$i];
							} else {
								$input['capacity'] = $capacity[$i];
							}
							$is_saved = SchemeCapacity::create($input);
						} else {
							$input = [
								'user_id' => Auth::user()->id
							];

							if ($special_quota == 1) {
								$input['special_quota'] = $capacity[$i];
							} else {
								$input['capacity'] = $capacity[$i];
							}

							$is_saved = SchemeCapacity::where('district_code', $arr[2])->where('scheme_id', $scheme)->update($input);

						}
						$final_dist = $final_dist + 1;
						if ($pending_s_update == 1) {
							$free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme . ", in_district_code => " . $arr[2] . ")");
							//dd($free_pending_bank_duplicate_arr);
							$free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
						} else {
							$free_pending_bank_duplicate_data = 1;
						}
						if ($is_saved && $free_pending_bank_duplicate_data == 1) {
							$k++;

						}
					}
				}
				if ($k == count($block_code)) {
					if($special_quota == 0)
					{
						DB::commit();
						return redirect('scheme-capacity')->with('msg', $final_dist . ' Block capacity added successfully');
					}
					else{
						DB::commit();
						return redirect('scheme-capacity')->with('msg', $final_dist . ' Block Special Quota capacity  added successfully');
					}

					
				} else {
					DB::rollback();
					return redirect('scheme-capacity')->with('msg1', 'Something Wrong !!');
				}
			} else {
				return redirect('scheme-capacity')->with('msg1', 'Something Wrong !! :: Selected level not allowed');
			}
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
			return redirect('scheme-capacity')->with('msg1', 'Something Wrong !!');
		}
	}
}
