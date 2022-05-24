<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Package;
use App\Company;

class PackagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "save"]);
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            //show packages with fileter
            $obj = new Package;

            $obj = $obj->with(['packets']);
            
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
            $unique_code = $request->input('unique_code');
            $payment_status = $request->input('payment_status');
            $from_city = $request->input('from_city');
            $to_city = $request->input('to_city');

            if(!empty($from_date)){
                $obj->whereDate('bill_date','>=',$from_date);
            }

            if(!empty($to_date)){
                $obj->whereDate('bill_date','<=',$to_date);
            }

            if(!empty($unique_code)){
                $obj->orWhere('unique_code','LIKE',"%{$unique_code}%");
                $obj->orWhere('consigner_name','LIKE',"%{$unique_code}%");
                $obj->orWhere('consigner_mobile','LIKE',"%{$unique_code}%");
                $obj->orWhere('consigner_gst_no','LIKE',"%{$unique_code}%");
            }

            if($payment_status != ""){
                $obj->where('payment_status',$payment_status);
            }

            if($from_city != ""){
                $obj->where('from_city',$from_city);
            }

            if($to_city != ""){
                $obj->where('to_city',$to_city);
            }

            $packages = $obj->where('status',1)->paginate(2);
        }else{
            //show all packages
            $packages = Package::with(['packets'])->where('status',1)->paginate(10);
        }
        return view('packages.index',compact('packages'));
    }

    public function create()
    {
        $cities = \App\City::where('country_id',101)->get();
        $countries = \App\Country::all();
        $companies_obj = \App\Company::select('name','id','gst_no')->get();
        $companies = array();
        if(!empty($companies_obj)){
            foreach($companies_obj as $key => $company){
                $companies[] = "Name:- ".$company->name." + GST no:- ".$company->gst_no;
            }
        }
        return view('packages.create',compact('cities','countries','companies'));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'unique_code' => 'required',
            'bill_date' => 'required',
            'from_city' => 'required',
            'to_city' => 'required',
            'transport_company' => 'required',
            'delivered_status' => 'nullable',
            'consignee_name' => 'required',
            'consignee_mobile' => 'required',
            'consignee_gst_no' => 'required',
            'consigner_name' => 'required',
            'consigner_mobile' => 'required',
            'consigner_gst_no' => 'required',
            'received_by' => 'required',
            'delivered_by' => 'nullable',
            'delivered_date' => 'nullable'
        ]);

       $inputs = $request->all();
       $inputs['delivered_status'] = (empty($inputs['delivered_status'])) ? "off":"on";

       $company_obj = new Company;
       $vtc_response = $company_obj->validateTransportCompany($inputs['transport_company']);
       
       if($vtc_response['status'] == 1){
            $inputs['transport_company_id'] = $vtc_response['id'];
            $package_obj = new \App\Package;
            $response = $package_obj->createPackage($inputs); 
            if($response['status'] == true){
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Package created successfully.');
            }else{
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Something went wrong while saving the information.');
            }
        }else{
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Please select the transport company from the list.');
        }

        return redirect()->route('packages.create');
    }

    /**
     * Edit package
     */
    public function edit($en_id = 0){
        $id = base64_decode($en_id);
        $package = \App\Package::with(['packets'])->where('id',$id)->first();
        if(!empty($package)){
            $cities = \App\City::where('country_id',101)->get();
            $countries = \App\Country::all();
            $companies_obj = \App\Company::select('name','id','gst_no')->get();
            $companies = array();
            $package['transport_company_name'] = "";
            if(!empty($companies_obj)){
                foreach($companies_obj as $key => $company){
                    if($company->id == $package->transport_com_id){
                        $package['transport_company_name'] = "Name:- ".$company->name." + GST no:- ".$company->gst_no;
                    }
                    $companies[] = "Name:- ".$company->name." + GST no:- ".$company->gst_no;
                }
            }
            return view('packages.edit',compact('cities','countries','companies','package'));
        }else{
            return redirect()->route('packages.index');
        }
    }

    /**
     * update package
     */
    public function update(Request $request,$en_id = 0){
        $package_id = base64_decode($en_id);
        $this->validate($request, [
            'unique_code' => 'required|unique:packages,unique_code,'.$package_id,
            'bill_date' => 'required',
            'from_city' => 'required',
            'to_city' => 'required',
            'transport_company' => 'required',
            'delivered_status' => 'nullable',
            'consignee_name' => 'required',
            'consignee_mobile' => 'required',
            'consignee_gst_no' => 'required',
            'consigner_name' => 'required',
            'consigner_mobile' => 'required',
            'consigner_gst_no' => 'required',
            'received_by' => 'required',
            'delivered_by' => 'required',
            'delivered_date' => 'required'
        ]);

        $package_obj = \App\Package::where('id',$package_id)->first();
        if(empty($package_obj)){
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Invalid package id.');
            return redirect()->route('packages.index');
        }else{
            $inputs = $request->all();
            $inputs['delivered_status'] = (empty($inputs['delivered_status'])) ? "off":"on";
            $company_obj = new Company;
            $vtc_response = $company_obj->validateTransportCompany($inputs['transport_company']);
            if($vtc_response['status'] == 1){
                $inputs['transport_company_id'] = $vtc_response['id'];
                $p_obj = new \App\Package;
                $response = $p_obj->updatePackage($inputs,$package_id);
                if($response['status'] == true){
                    $request->session()->flash('message.level', 'success');
                    $request->session()->flash('message.content', 'Package updated successfully.');
                }else{
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Something went wrong while updating the information.');
                }
            }else{
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Please select the transport company from the list.');
            }
            return redirect()->route('packages.edit',$en_id);
        }
    }

    /**
     * Delete package
     */
    public function delete(Request $request){
        $package_id = base64_decode($request->input('package_id'));
        $package_obj = \App\Package::where('id',$package_id)->update(array('status'=>0));
        if(!empty($package_obj)){
            $response = array('status'=>true,'message'=>'Package deleted successfully');
        }else{
            $response = array('status'=>false,'message'=>'Something went wrong while deleting data.');
        }
        return $response;
    }

    /**
     * Get state based on country id
     */
    public function getStates(Request $request){

        $country_id = $request->input('country_id');
        $states = \App\State::where('country_id',$country_id)->get()->toArray();
        if(!empty($states)){
            $response = array('status' => true, 'states' => $states);
        }else{
            $response = array('status' => false);
        }
        return $response;
    }

    /**
     * Add new city
     */
    public function addCity(Request $request){
        $inputs = $request->all();
        $city_obj = new \App\City;
        $response = $city_obj->addNewCity($inputs);
        return $response;
    }

}