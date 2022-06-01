<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
 
class Customer extends Model
{
    use  Notifiable,SoftDeletes;

    protected $fillable = [
        'name',
        'mobile',
        'gst',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function createCustomer($inputs = []){
        $response = array('status' => false);
        $customer_param = array(
                            'name' => $inputs['name'],
                            'mobile' => $inputs['mobile'],
                            'gst' => $inputs['gst']
                        );
                        
        $customer_res = Customer::create($customer_param);
        
        if(!empty($customer_param)){
            $response = array('status' => true);
        }

        return $response;
    }

    /**
     * Update customer info
     */
    public function updateCustomer($inputs,$customer_id){
        $response = array('status' => false);
        $customer_param = array(
                            'name' => $inputs['name'],
                            'mobile' => $inputs['mobile'],
                            'gst' => $inputs['gst'],
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        
        $customer_res = Customer::where('id',$customer_id)->update($customer_param);

        if(!empty($customer_res)){
            $response = array('status' => true);
        }

        return $response;
    }

    /**
     * Validate customer
     */
    public function validateCustomer($name, $mobile, $gst = ''){
        $response = array('status'=>false,'message' => 'Wrong informtion provided for consigner.');
        $obj = \App\Customer::where('mobile',$mobile)->first();
        if(empty($obj)){
            $flag = 0;
            //if mobile not exist add customer
            if(!empty($gst)){
                $g_obj = \App\Customer::where('gst',$gst)->first();
                if(!empty($g_obj) && ($g_obj->mobile != $mobile)){
                    $response['message'] = 'This GST no is already in use by another customer.';
                }else{
                    $flag = 1;
                }
            }

            if($flag == 1){
                \App\Customer::create(array('name'=>$name, 'mobile' => $mobile, 'gst' => $gst));
                $response = array('status'=> true, 'message' => 'Customer added successfully.');
            }
        }else{
            $flag = 0;
            if(!empty($gst)){
                $g_obj = \App\Customer::where('gst',$gst)->first();
                if(!empty($g_obj) && !empty($g_obj->mobile != $mobile)){
                    $response['message'] = 'This GST no is already in use by another customer.';
                }else{
                    \App\Customer::where('mobile',$mobile)->update(array('name'=>$name, 'gst' => $gst));
                    $response = array('status'=> true, 'message' => 'Customer updated successfully.');
                }
            }else{
                \App\Customer::where('mobile',$mobile)->update(array('name'=>$name));
                $response = array('status'=> true, 'message' => 'Customer updated successfully.');
            }
        }


        return $response;
    }
}
