<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Package extends Model
{
    use  Notifiable,SoftDeletes;

    protected $fillable = [
        'unique_code',
        'transport_com_id',
        'bill_date',
        'from_city',
        'to_city',
        'consignee_name',
        'consignee_mobile',
        'consignee_gst',
        'payment_status',
        'paid_by_me',
        'consigner_name',
        'consigner_mobile',
        'consigner_gst_no',
        'delivery_status',
        'received_by',
        'delivered_by',
        'delivered_date',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function createPackage($inputs = []){
        $response = array('status' => false);
        $package_param = array(
                            'unique_code' => $inputs['unique_code'],
                            'transport_com_id' => $inputs['transport_company_id'],
                            'bill_date' => $inputs['bill_date'],
                            'from_city' => $inputs['from_city'],
                            'to_city' => $inputs['to_city'],
                            'consignee_name' => $inputs['consignee_name'],
                            'consignee_mobile' => $inputs['consignee_mobile'],
                            'consignee_gst' => $inputs['consignee_gst_no'],
                            'payment_status' => $inputs['payment_status'],
                            'paid_by_me' => $inputs['paid_by_me'],
                            'consigner_name' => $inputs['consigner_name'],
                            'consigner_mobile' => $inputs['consigner_mobile'],
                            'consigner_gst_no' => $inputs['consigner_gst_no'],
                            'delivery_status' => $inputs['delivered_status'],
                            'received_by' => $inputs['received_by'],
                            'delivered_by' => $inputs['delivered_by'],
                            'delivered_date' => $inputs['delivered_date']
                        );
                        
        $package_res = Package::create($package_param);
        
        if(!empty($package_res)){
            $packet_param = array(
                                'package_id' => $package_res->id,
                                'no_of_packets' => $inputs['package_no_of_packets'][0],
                                'description' => $inputs['package_description'][0],
                                'weight' => $inputs['package_weight'][0],
                                'freight' => $inputs['package_freight'][0]
                            );
            $packet_res = \App\packet::create($packet_param);
            if(!empty($packet_res)){
                $response = array('status' => true);
            }
        }

        return $response;
    }

    /**
     * Update package info
     */
    public function updatePackage($inputs,$package_id){
        $response = array('status' => false);
        $package_param = array(
                            'unique_code' => $inputs['unique_code'],
                            'transport_com_id' => $inputs['transport_company_id'],
                            'bill_date' => $inputs['bill_date'],
                            'from_city' => $inputs['from_city'],
                            'to_city' => $inputs['to_city'],
                            'consignee_name' => $inputs['consignee_name'],
                            'consignee_mobile' => $inputs['consignee_mobile'],
                            'consignee_gst' => $inputs['consignee_gst_no'],
                            'payment_status' => $inputs['payment_status'],
                            'paid_by_me' => $inputs['paid_by_me'],
                            'consigner_name' => $inputs['consigner_name'],
                            'consigner_mobile' => $inputs['consigner_mobile'],
                            'consigner_gst_no' => $inputs['consigner_gst_no'],
                            'delivery_status' => $inputs['delivered_status'],
                            'received_by' => $inputs['received_by'],
                            'delivered_by' => $inputs['delivered_by'],
                            'delivered_date' => $inputs['delivered_date'],
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        
        $package_res = Package::where('id',$package_id)->update($package_param);

        if(!empty($package_res)){
            $packet_param = array(
                                'no_of_packets' => $inputs['package_no_of_packets'][0],
                                'description' => $inputs['package_description'][0],
                                'weight' => $inputs['package_weight'][0],
                                'freight' => $inputs['package_freight'][0]
                            );
            $packet_res = \App\packet::where(array('package_id'=>$package_id))->update($packet_param);
            if(!empty($packet_res)){
                $response = array('status' => true);
            }
        }

        return $response;
    }

    /**
     * packets relation
     */
    public function packets() {
        return $this->hasMany('App\Packet', 'package_id', 'id');
    }
}
