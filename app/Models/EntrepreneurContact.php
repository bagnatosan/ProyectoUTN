<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrepreneurContact extends Model
{
    protected $fillable = [
        'business_name',
        'contact_name',
        'email',
        'phone',
        'message',
    ];
}
