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

    public function save(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'mobile' => 'required|unique:customers,mobile',
            'gst' => 'nullable|unique:customers,gst'
        ]);

        $inputs = $request->all();

        $customer_obj = new \App\Customer;

        $response = $customer_obj->createCustomer($inputs); 

        if($response['status'] == true){
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Customer created successfully.');  
        }else{
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Something went wrong while saving the information.');
        }

        return redirect()->route('customer.index');
    }

    /**
     * Edit package
     */
    public function edit($en_id = 0){
        $id = base64_decode($en_id);
        $customer = \App\Customer::where('id',$id)->first();
        if(!empty($customer)){
            return view('customers.edit',compact('customer'));
        }else{
            return redirect()->route('customer.index');
        }
    }

    /**
     * update customer
     */
    public function update(Request $request,$en_id = 0){
        $customer_id = base64_decode($en_id);
        $this->validate($request, [
            'mobile' => 'required|unique:customers,mobile,'.$customer_id,
            'name' => 'required',
            'gst' => 'nullable|unique:customers,gst,'.$customer_id
        ]);

        $customer_obj = \App\Customer::where('id',$customer_id)->first();
        if(empty($customer_obj)){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Invalid customer id.');
            return redirect()->route('customer.index');
        }else{
            $inputs = $request->all();
            $c_obj = new \App\Customer;
            $response = $c_obj->updateCustomer($inputs,$customer_id);
            if($response['status'] == true){
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Customer updated successfully.');
            }else{
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Something went wrong while updating the information.');
            }
            return redirect()->route('customer.edit',$en_id);
        }
    }
}