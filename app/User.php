<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'roles', 'country_code','mobile', 'alternate_mobile', 'gender','dob','state_id','city_id','pincode','area_name','street_no','address','profile_pic','aadhaar_number','aadhaar_front','aadhaar_back','pan_number','pan_card','deal_in','no_of_deal','no_of_product','status'
    ];
    protected $appends = ['city'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function getCityAttribute()
    {
        $value=City::find($this->city_id);
        return $value;
    }
}
