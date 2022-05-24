<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Package;
use App\Customer;

class CustomerController extends Controller
{

    public function __construct(){

    }

    public function index()
    {
        $customers = Customer::where('status',1)->paginate(10);
        return view('customers.index', ['customers' => $customers]);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function delete(Request $request){
        $user_id = base64_decode($request->input('user_id'));
        $user_obj = \App\Customer::where('id',$user_id)->update(array('status'=>0));
        if(!empty($user_obj)){
            $response = array('status'=>true,'message'=>'User deleted successfully');
        }else{
            $response = array('status'=>false,'message'=>'Something went wrong while deleting data.');
        }
        return $response;
    }
}