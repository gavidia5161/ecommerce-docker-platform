<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\JsonResponse;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    #[OA\Post(
        path: '/api/payments',
        summary: 'Procesar el pago de una orden',
        tags: ['Payments'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['order_id'],
                properties: [
                    new OA\Property(
                        property: 'order_id',
                        type: 'integer',
                        example: 8,
                        description: 'ID de la orden que se desea pagar'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Pago procesado correctamente'
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado'
            ),
            new OA\Response(
                response: 403,
                description: 'La orden no pertenece al usuario autenticado'
            ),
            new OA\Response(
                response: 404,
                description: 'Orden no encontrada'
            )
        ]
    )]
    public function store(PaymentRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'No tienes permiso para pagar esta orden'
            ], 403);
        }

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $order->total * 100,
            'currency' => 'usd',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total,
            'status' => 'paid',
            'stripe_payment_id' => $paymentIntent->id,
        ]);

        return response()->json([
            'message' => 'Pago procesado correctamente',
            'payment_id' => $paymentIntent->id,
        ], 201);
    }
}
