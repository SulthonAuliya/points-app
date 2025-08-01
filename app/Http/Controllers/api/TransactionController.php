<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request){
        $startDate = $request->query('start_date', Carbon::now()->subYear()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());
        $perPage = $request->query('per_page', 20);

        $transactions = Transaction::with('point')
            ->whereBetween('transacted_at', [$startDate, $endDate])
            ->orderBy('transacted_at', 'desc')
            ->paginate($perPage);

        $data = $transactions->through(function ($transaction) {
            return [
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'points' => $transaction->point->points ?? 0,
                'description' => $transaction->description,
                'transacted_at' => $transaction->transacted_at,
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'total_page' => $transactions->total(),
            ],
        ]);
    }

    public function transactions(Request $request){
        DB::beginTransaction();
        try{
            if(!(int)$request->amount > 0){
                return response('Amount harus besar dari 0');
            }   
            $transaction = Transaction::create([
                'user_id'   => $request->user_id,
                'amount'    => $request->amount,
                'description'    => $request->description,
                'transacted_at' => $request->transacted_at
            ]);
            $point = floor($request->amount / 1000);
            if($point > 0){
                Point::create([
                    'transaction_id' => $transaction->id,
                    'points'         => $point
                ]);
            }
            DB::commit();
            return response('Berhasil Menyimpan transaksi!');
        }catch(Exception $e){
            DB::rollBack();
            Log::error($e);
            return $e;
        }
    }
}
