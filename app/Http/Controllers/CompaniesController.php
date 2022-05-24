<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Company;

class CompaniesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "edit", "update", "save"]);
    }

    public function index()
    {
        $companies = Company::where('status',1)->paginate(10);
        return view('companies.index', ['companies' => $companies]);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'name' => 'nullable|max:60',
            'mobile' => 'nullable|digits:10',
            'email' => 'required|email|max:60',
            'gst_no' => 'required|max:120|unique:companies,gst_no'
        ]);

        $inputs = $request->all();
        
        $data = array(
                    'name' => $inputs['name'],
                    'mobile' => $inputs['mobile'],
                    'email' => $inputs['email'],
                    'gst_no' => $inputs['gst_no']
                );

        $company_obj = new \App\Company;
        $response = $company_obj->create_company($data);
 
        if($response == 1){
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Company added successfully.');
        }else{
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Something went wrong while saving the information.');
        }

        return redirect()->route('transport.create');
    }

    public function create()
    {
        return view('companies.create');
    }

    public function edit($id = 0)
    {
        $company = Company::find($id);

        if (!$company) {
            return redirect()->route('transport.index');
        }

        return view('companies.edit', ['company' => $company]);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        if($company){
            $this->validate($request, [
                'name' => 'required|max:60',
                'mobile' => 'nullable|digits:10',
                'email' => 'nullable|email|max:60',
                'gst_no' => 'required|max:120|unique:companies,gst_no,'.$id
            ]);

            $inputs = $request->all();

            $data = array(
                'name' => $inputs['name'],
                'mobile' => $inputs['mobile'],
                'email' => $inputs['email'],
                'gst_no' => $inputs['gst_no']
            );
 
            $company_obj = new \App\Company;
            $response = $company_obj->updateCompany($data,$id);

            if($response == 1){
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Information updated successfully.');
                return redirect()->route('transport.index');
            }else{
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Something went wrong while saving the information.');
                return redirect()->route('transport.update',$id);
            }
        }else{
            return redirect()->route('transport.index');
        }
    }

    public function delete(Request $request, $id){
        $res = Company::where('id', $id)->update(array('status'=>0));
        if($res){
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Company deleted successfully.');
        }else{
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Something went wrong while deleting the information.');
        }
        return redirect()->route('transport.index');
    }
}