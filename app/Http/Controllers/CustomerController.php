<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Numbers;
use App\Models\NumberPreferences;
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
        Route::post('edit', '\App\Http\Controllers\CustomerController@edit');
        Route::get('list', '\App\Http\Controllers\CustomerController@get');
        Route::post('numbers', '\App\Http\Controllers\CustomerController@createNumber');
        Route::post('numbers/edit', '\App\Http\Controllers\CustomerController@editNumber');
        Route::get('numbers/list', '\App\Http\Controllers\CustomerController@getNumbers');
    }

    public function getNumbers(Request $request)
    {
        $numbers = DB::table('numbers AS nu')
            ->select(
                'nu.id',
                'nu.number',
                'nu.status',
                'cuser.permission',
            )
            ->join('customers AS cu', 'nu.customer_id', '=', 'cu.id')
            ->join('customer_user AS cuser', 'cuser.customer_id', '=', 'cu.id')
            ->where('cuser.user_id', Auth::user()->id)
            ->where('cu.id', $request->customer_id)
            ->groupBy(
                'nu.id',
                'nu.number',
                'nu.status',
                'cuser.permission',
            )
            ->orderBy($request->sort ?? 'number', $request->sort_desc ?? 'asc')
            ->paginate($request->per_page ?? 50)
            ->toArray();

        //agrupa as preferencias de um numero
        foreach ($numbers["data"] as &$value) {
            $prefs = NumberPreferences::where('number_id', $value->id)->get();
            foreach ($prefs as $pref) {
                $value->{$pref->name} = $pref->value ? true : false;
            }
        }

        return $numbers;
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

        $number_preference = new NumberPreferences;
        $number_preference->id = (string) Str::orderedUuid();
        $number_preference->number_id = $number->id;
        $number_preference->name = "auto_attendant";
        $number_preference->value = "0";
        if ($request->auto_attendant) {
            $number_preference->value = "1";
        }
        $number_preference->save();

        $number_preference = new NumberPreferences;
        $number_preference->id = (string) Str::orderedUuid();
        $number_preference->number_id = $number->id;
        $number_preference->name = "voicemail_enabled";
        $number_preference->value = "0";
        if ($request->voicemail_enabled) {
            $number_preference->value = "1";
        }
        $number_preference->save();

        return ["number" => $number->toArray()];
    }

    public function editNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|min:1|max:500',
            'number' => 'required|string|min:1|max:500',
            'status' => 'string|min:1|max:50',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $number = Numbers::find($request->id);
        if (!$number) {
            Errors::make("10005");
        }

        $number->number = $request->number ?? $number->number;
        $number->status = $request->status ?? $number->status;
        $number->save();

        if (isset($request->auto_attendant)) {
            $pref = NumberPreferences::where('name', 'auto_attendant')
                ->where('number_id', $request->id)
                ->first();
            $pref->value = "0";
            if ($request->auto_attendant) {
                $pref->value = "1";
            }
            $pref->save();
        }

        if (isset($request->voicemail_enabled)) {
            $pref = NumberPreferences::where('name', 'voicemail_enabled')
                ->where('number_id', $request->id)
                ->first();
            $pref->value = "0";
            if ($request->voicemail_enabled) {
                $pref->value = "1";
            }
            $pref->save();
        }

        return ["customer" => $number->toArray()];
    }

    public function get(Request $request)
    {
        $read = DB::table('customers AS cu')
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

        $customer_user = new CustomerUser;
        $customer_user->id = (string) Str::orderedUuid();
        $customer_user->user_id = Auth::user()->id;
        $customer_user->customer_id = $customer->id;
        $customer_user->permission = 'owner';
        $customer_user->save();

        return ["customer" => $customer->toArray()];
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

        return ["customer" => $customer->toArray()];
    }
}
