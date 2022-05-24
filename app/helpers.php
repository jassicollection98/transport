<?php namespace App\Helpers;

use App\City;

class Helpers
{
    function getCities(){
        $cities = City::all();
        return $cities;
    }
}