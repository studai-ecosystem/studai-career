<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class RazorpayController extends Controller
{
    public function createOrder(Request $request)
    {
        Log::info('Razorpay Order Creation Started', ['amount' => $request->amount]);
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        try {
            if ($request->amount <= 0) {
                return response()->json(['error' => 'Amount must be greater than zero for Razorpay orders.'], 400);
            }

            $orderData = [
                'receipt' => 'receipt_' . rand(1000, 9999),
                'amount' => $request->amount * 100, // amount in paise
                'currency' => 'INR',
            ];

            $order = $api->order->create($orderData);
            Log::info('Razorpay Order Created Successfully', ['order_id' => $order['id']]);

            return response()->json([
                'order_id' => $order['id']
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);
            return response()->json(['error' => 'Could not create order: ' . $e->getMessage()], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        Log::info('Razorpay Payment Verification Started', ['request' => $request->all()]);
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            $api->utility->verifyPaymentSignature($attributes);
            Log::info('Razorpay Signature Verified');

            // Store transaction in database
            $transaction = PaymentTransaction::create([
                'user_id' => auth()->id(),
                'transaction_id' => $request->razorpay_payment_id,
                'order_id' => $request->razorpay_order_id,
                'payment_gateway' => 'razorpay',
                'amount' => $request->amount ?? 499,
                'currency' => 'INR',
                'status' => PaymentTransaction::STATUS_SUCCESS,
                'paid_at' => now(),
                'completed_at' => now(),
            ]);
            Log::info('Payment Transaction Recorded', ['transaction_id' => $transaction->id]);

            return response()->json(['status' => 'success', 'redirect_url' => url('/payments/success')]);

        } catch (\Exception $e) {
            Log::error('Razorpay Verification Failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);
            
            PaymentTransaction::create([
                'user_id' => auth()->id(),
                'transaction_id' => $request->razorpay_payment_id,
                'order_id' => $request->razorpay_order_id,
                'payment_gateway' => 'razorpay',
                'status' => PaymentTransaction::STATUS_FAILED,
                'error_message' => $e->getMessage(),
                'failed_at' => now(),
            ]);

            return response()->json(['status' => 'failed', 'redirect_url' => url('/payments/failed')], 400);
        }
    }
}
