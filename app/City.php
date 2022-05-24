<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cities';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /**
     * add new city
     */
    public function addNewCity($data = []){
        $response = array('status' => false, 'message' => 'All fields are required.');
        if(isset($data['state_id']) && isset($data['city_name']) && isset($data['country_id'])){ 
            $cityObj = \App\City::where(array('country_id'=>$data['country_id'],'state_id'=>$data['state_id'],'name'=>$data['city_name']))->first();
            if(!empty($cityObj)){
                $response = array('status' => false, 'message' => 'City already exists for this name.');
                return $response;
            }else{

                $params = array(
                                'name' => $data['city_name'],
                                'state_id' => $data['state_id'],
                                'country_id' => $data['country_id']
                            );
                \App\City::create($params);
                $cities = \App\City::where(array('country_id'=>$data['country_id']))->get();
                $response = array('status' => true, 'message' => 'City added successfully.','cities'=>$cities);
            }
        }
        return $response;
    }
}
