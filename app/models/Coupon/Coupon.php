<?php


namespace MissionNext\Models\Coupon;


use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected  $table = 'coupons';

    protected $fillable = ['code', 'is_active', 'value'];
} 