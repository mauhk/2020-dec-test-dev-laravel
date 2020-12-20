<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerUser;
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
