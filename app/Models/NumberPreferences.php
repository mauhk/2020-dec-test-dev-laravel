<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Middleware\Uuids;

class NumberPreferences extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $table = 'number_preferences';
    protected $primaryKey = 'id';
    protected $fillable = ['number_id', 'name', 'value'];
    public $incrementing = false;
    protected $hidden = [];

}
