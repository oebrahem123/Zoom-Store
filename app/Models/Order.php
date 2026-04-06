<?php

namespace App\Models;
use App\Models\orderdetails;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
public function orderdetails()
{
    return $this->hasMany(orderdetails::class, 'order_id', 'id');
}}
