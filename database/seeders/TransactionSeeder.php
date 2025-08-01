<?php

namespace Database\Seeders;

use App\Models\Point;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try{
            for($i = 0; $i <= 1000; $i++){
                $amount = rand(10000, 500000);
                $point = floor($amount / 1000);
                $randomTimestamp = Carbon::now()->subYear()->addSeconds(rand(0, 365 * 24 * 60 * 60));
                $transaction = Transaction::create([
                    'user_id'       => 1,
                    'amount'        => $amount,
                    'transacted_at' => $randomTimestamp,
                    'description'   => fake()->sentences(3, true)
                ]);
                
                Point::create([
                    'transaction_id'    => $transaction->id,
                    'points'             => $point,
                ]);
            };

        DB::commit();
        }catch(Exception $e){
            DB::rollback();
            Log::error ($e);
        }
    }
}
