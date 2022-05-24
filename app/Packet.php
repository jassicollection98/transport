<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Packet extends Model
{
    use  Notifiable,SoftDeletes;

    protected $table = 'packets_info';

    protected $fillable = [
        'package_id',
        'no_of_packets',
        'description',
        'weight',
        'freight',
        'created_at',
        'updated_at'
    ];

}
