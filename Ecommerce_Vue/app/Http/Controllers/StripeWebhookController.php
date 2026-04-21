<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\Exception $e) {
            return response('Invalid', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            Log::info('Stripe session ID: ' . $session->id);

            $order = Order::where('session_id', $session->id)->first();
            if (!$order) {
                return response('Order not found', 404);
            }

            $order->status = 'paid';
            $order->save();

            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity = (int)$product->quantity - (int)$item->quantity;

                    if ($product->quantity < 0) {
                        $product->quantity = 0;
                    }
                    $product->save();
                }
            }
        }

        return response('Success', 200);
    }
}
