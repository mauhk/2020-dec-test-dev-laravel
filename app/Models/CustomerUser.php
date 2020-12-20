<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Http\Middleware\Uuids;

class CustomerUser extends Model
{
    use Uuids;

    protected $table = 'customer_user';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'customer_id', 'permission'];
    public $incrementing = false;
    protected $hidden = [];

}
