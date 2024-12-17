<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;

class PolicyController extends Controller
{
	public function __construct()
	{
	}
	public function copyright(Request $request)
	{
		return view(
			'Policy.copyright',
			[
			]
		);
	}
	public function privacy(Request $request)
	{
		return view(
			'Policy.privacy',
			[
			]
		);
	}
	public function hyperlink(Request $request)
	{
		return view(
			'Policy.hyperlink',
			[
			]
		);
	}
	public function terms_condition(Request $request)
	{
		return view(
			'Policy.terms_condition',
			[
			]
		);
	}
}
