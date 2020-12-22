<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Middleware\Uuids;

class Customer extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'document', 'status'];
    public $incrementing = false;
    protected $hidden = [];

}
