<?php

namespace App\Http\Controllers;

use App\Models\Numbers;
use App\Models\NumberPreferences;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NumberController extends Controller
{
    public static function routes()
    {
        Route::post('create', '\App\Http\Controllers\NumberController@create');
        Route::post('edit', '\App\Http\Controllers\NumberController@edit');
        Route::get('list', '\App\Http\Controllers\NumberController@list');
        Route::delete('{id}', '\App\Http\Controllers\NumberController@delete');
    }

    public function delete(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|string|min:1|max:50',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return !!DB::table('numbers')->where('id', $id)->update(['deleted_at' => \Carbon\Carbon::now()]);
    }

    public function list(Request $request)
    {
        $numbers = DB::table('numbers AS nu')
            ->select(
                'nu.id',
                'nu.number',
                'nu.status',
            )
            ->join('customers AS cu', 'nu.customer_id', '=', 'cu.id')
            ->where('cu.id', $request->customer_id)
            ->whereNull('nu.deleted_at')
            ->groupBy(
                'nu.id',
                'nu.number',
                'nu.status',
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

    public function create(Request $request)
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

        return $number->toArray();
    }

    public function edit(Request $request)
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

        return $number->toArray();
    }

}
