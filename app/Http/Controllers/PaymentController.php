<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Str;
use Karim007\SslcommerzLaravel\Facade\SSLCommerzPayment;
use Karim007\SslcommerzLaravel\Sslcommerz;

class PaymentController extends Controller
{

    public function test(){
        return response()->json(['status'=>'success']);

    //    $sslcommerz = new SSLCommerzPayment();

      //  dd( $sslcommerz);

    }
    public function initiate(Request $request)
    {
        $tran_id = uniqid();

        $randomString = Str::random(6);

        $user =  User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>$randomString,
        ]);

        $order = Order::create([
            'tran_id' => $tran_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);


        $post_data = [
            'total_amount' => $order->amount,
            'currency' => $order->currency,
            'tran_id' => $order->tran_id,

            'success_url' => route('payment.success'),
            'fail_url' => route('payment.fail'),
            'cancel_url' => route('payment.cancel'),

            'cus_name' => $order->name,
            'cus_email' => $order->email,
            'cus_phone' => $order->phone,
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
        ];

        $sslcommerz = new SSLCommerzPayment();
        $response = $sslcommerz->makePayment($post_data, 'hosted');

        return response()->json($response);
    }

    public function success(Request $request)
    {
        $tran_id = $request->tran_id;
        $order = Order::where('tran_id', $tran_id)->first();

        if ($order && $order->status === 'pending') {
            $order->update(['status' => 'completed']);
        }

        return redirect(env("FRONTEND_URL")."/payment-result?status=success&tran_id={$tran_id}");
    }

    public function fail(Request $request)
    {
        $tran_id = $request->tran_id;
        $order = Order::where('tran_id', $tran_id)->first();

        if ($order) {
            $order->update(['status' => 'failed']);
        }

        return redirect(env("FRONTEND_URL")."/payment-result?status=failed&tran_id={$tran_id}");
    }

    public function cancel(Request $request)
    {
        $tran_id = $request->tran_id;
        $order = Order::where('tran_id', $tran_id)->first();

        if ($order) {
            $order->update(['status' => 'cancelled']);
        }

        return redirect(env("FRONTEND_URL")."/payment-result?status=cancelled&tran_id={$tran_id}");
    }

    // Optional: fetch order details (for Nuxt frontend)
    public function getOrder($tran_id)
    {
        return Order::where('tran_id', $tran_id)->firstOrFail();
    }

}
