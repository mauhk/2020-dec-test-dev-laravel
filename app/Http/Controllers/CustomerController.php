<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Numbers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public static function routes()
    {
        Route::post('create', '\App\Http\Controllers\CustomerController@create');
        Route::get('list', '\App\Http\Controllers\CustomerController@get');
        Route::post('numbers', '\App\Http\Controllers\CustomerController@createNumber');
        Route::get('numbers/list', '\App\Http\Controllers\CustomerController@getNumbers');
    }

    public function createNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string|min:1|max:500',
            'number' => 'required|string|min:1|max:500',
            'status' => 'string|min:1|max:50',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $number = Numbers::where('number', $request->number)
            ->where('customer_id', $request->customer_id)
            ->first();
        if (!!$number) {
            Errors::make("10003");
        }

        $number = new Numbers;
        $number->id = (string) Str::orderedUuid();
        $number->number = $request->number;
        $number->customer_id = $request->customer_id;
        $number->save();

        return ["number" => $number->toArray()];
    }

    public function getNumbers(Request $request)
    {
        return DB::table('numbers AS nu')
            ->select(
                'nu.id',
                'nu.number',
                'nu.status',
                'cuser.permission'
            )
            ->join('customers AS cu', 'nu.customer_id', '=', 'cu.id')
            ->join('customer_user AS cuser', 'cuser.customer_id', '=', 'cu.id')
            ->where('cuser.user_id', Auth::user()->id)
            ->groupBy(
                'nu.id',
                'nu.number',
                'nu.status',
                'cuser.permission'
            )
            ->orderBy($request->sort ?? 'number', $request->sort_desc ?? 'asc')
            ->paginate($request->per_page ?? 50)
            ->toArray();
    }

    public function get(Request $request)
    {
        return DB::table('customers AS cu')
            ->select(
                'cu.id',
                'cu.name',
                'cu.document',
                'cu.status',
                'cuser.permission'
            )
            ->join('customer_user AS cuser', 'cuser.customer_id', '=', 'cu.id')
            ->where('cuser.user_id', Auth::user()->id)
            ->groupBy(
                'cu.id',
                'cu.name',
                'cu.document',
                'cu.status',
                'cuser.permission'
            )
            ->orderBy($request->sort ?? 'name', $request->sort_desc ?? 'asc')
            ->paginate($request->per_page ?? 50)
            ->toArray();
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:500',
            'document' => 'required|string|min:1|max:100',
            'status' => 'string|min:1|max:50',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $customer = Customer::where('document', $request->document)->first();
        if (!!$customer) {
            Errors::make("10002");
        }

        $customer = new Customer;
        $customer->id = (string) Str::orderedUuid();
        $customer->name = $request->name;
        $customer->document = $request->document;
        $customer->save();

        $customer_user = new CustomerUser;
        $customer_user->id = (string) Str::orderedUuid();
        $customer_user->user_id = Auth::user()->id;
        $customer_user->customer_id = $customer->id;
        $customer_user->permission = 'owner';
        $customer_user->save();

        return ["customer" => $customer->toArray()];
    }
}
