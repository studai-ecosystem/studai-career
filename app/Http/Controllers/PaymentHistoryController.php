<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentHistoryController extends Controller
{
    /**
     * Display payment history page
     */
    public function index(Request $request)
    {
        if ($request->query('diag') === '1') {
            try {
                $user = $request->user();
                $transactions = $user->paymentTransactions()
                    ->with('subscriptionPlan')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

                return response(view('payments.history', compact('transactions'))->render());
            } catch (\Throwable $e) {
                return response('DIAG payments: ' . get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine(), 200)
                    ->header('Content-Type', 'text/plain');
            }
        }

        $user = $request->user();
        
        $transactions = $user->paymentTransactions()
            ->with('subscriptionPlan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('payments.history', compact('transactions'));
    }

    /**
     * Show single transaction details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $transaction = $user->paymentTransactions()
            ->with('subscriptionPlan')
            ->findOrFail($id);
        
        return view('payments.show', compact('transaction'));
    }
}
