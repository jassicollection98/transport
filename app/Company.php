<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Company extends Model
{
    use  Notifiable,SoftDeletes;

    protected $fillable = [
		'name',
        'mobile',
        'email',
        'gst_no',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function create_company($data){
        $res = Company::create($data);
        if($res){
            return 1;
        }else{
            return 0;
        }
    }

    public function updateCompany($data,$id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        $res = Company::where('id',$id)->update($data);
        if($res){
            return 1;
        }else{
            return 0;
        }
    }

    public function validateTransportCompany($string = ""){
        $flag = 0;
        $arr = explode("+",$string);
        if((count($arr) == 2) && isset($arr[0]) && $arr[1]){
            $company_name = str_replace("Name:- ","",$arr[0]);
            $company_gst = str_replace(" GST no:- ","",$arr[1]);
            $company_obj = \App\Company::where(array('name'=> $company_name, 'gst_no' => $company_gst, 'status' => 1))->first();
            if(!empty($company_obj)){
                $flag = 1;
                $response['id'] = $company_obj->id;
            }
        }
        $response['status'] = $flag;
        return $response;
    }
}
