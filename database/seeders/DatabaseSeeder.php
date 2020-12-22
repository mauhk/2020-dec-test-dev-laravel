<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Numbers;
use App\Models\NumberPreferences;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::delete('DELETE FROM number_preferences');
        DB::delete('DELETE FROM numbers');
        DB::delete('DELETE FROM customers');
        DB::delete('DELETE FROM users');

        $user = new User;
        $user->id = (string) Str::orderedUuid();
        $user->name = "admin";
        $user->email = "admin@admin.com";
        $user->password = bcrypt("admin");
        $user->save();

        $user2 = new User;
        $user2->id = (string) Str::orderedUuid();
        $user2->name = "admin2";
        $user2->email = "admin2@admin.com";
        $user2->password = bcrypt("admin2");
        $user2->save();

        Auth::attempt([
            'email' => $user->email,
            'password' => $user->password,
        ]);

        $q_customer = 10;
        while ($q_customer--) {
            $customer = new Customer;
            $customer->id = (string) Str::orderedUuid();
            $customer->name = "name_" . Str::random(10);
            $customer->document = "doc_" . Str::random(10);
            $customer->status = "new";
            $customer->save();

            $q_number = rand(1, 10);
            while ($q_number--) {

                $number = new Numbers;
                $number->id = (string) Str::orderedUuid();
                $number->number = "number_" . Str::random(10);
                $number->customer_id = $customer->id;
                $number->save();

                $number_preference = new NumberPreferences;
                $number_preference->id = (string) Str::orderedUuid();
                $number_preference->number_id = $number->id;
                $number_preference->name = "auto_attendant";
                $number_preference->value = (string) rand(0, 1);
                $number_preference->save();

                $number_preference = new NumberPreferences;
                $number_preference->id = (string) Str::orderedUuid();
                $number_preference->number_id = $number->id;
                $number_preference->name = "voicemail_enabled";
                $number_preference->value = (string) rand(0, 1);
                $number_preference->save();
            }
        }
    }
}
