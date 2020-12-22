<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Middleware\Uuids;

class Numbers extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $table = 'numbers';
    protected $primaryKey = 'id';
    protected $fillable = ['number', 'status', 'customer_id'];
    public $incrementing = false;
    protected $hidden = [];

}
