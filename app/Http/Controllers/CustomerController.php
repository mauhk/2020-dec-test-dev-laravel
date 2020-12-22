<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public static function routes()
    {
        Route::post('create', '\App\Http\Controllers\CustomerController@create');
        Route::post('edit', '\App\Http\Controllers\CustomerController@edit');
        Route::get('list', '\App\Http\Controllers\CustomerController@list');
    }

    public function list(Request $request)
    {
        $read = DB::table('customers AS cu')
            ->select(
                'cu.id',
                'cu.name',
                'cu.document',
                'cu.status',
            )
            ->groupBy(
                'cu.id',
                'cu.name',
                'cu.document',
                'cu.status',
            )
            ->orderBy($request->sort ?? 'name', $request->sort_desc ?? 'asc');

        if (isset($request->per_page) && $request->per_page > 0) {
            $read = $read->paginate($request->per_page);
        }

        return $read->toArray();
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
        $customer->status = $request->status ?? "new";
        $customer->save();

        return $customer->toArray();
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|min:1|max:500',
            'name' => 'string|min:1|max:500',
            'document' => 'string|min:1|max:100',
            'status' => 'string|min:1|max:50',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $customer = Customer::find($request->id);
        if (!$customer) {
            Errors::make("10004");
        }

        $customer->name = $request->name ?? $customer->name;
        $customer->document = $request->document ?? $customer->document;
        $customer->status = $request->status ?? $customer->status;
        $customer->save();

        return $customer->toArray();
    }
}
