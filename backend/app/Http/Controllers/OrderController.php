<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Post(
        path: '/api/orders',
        summary: 'Crear una orden',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['items'],
                properties: [
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        description: 'Productos incluidos en la orden',
                        items: new OA\Items(
                            type: 'object',
                            required: ['product_id', 'quantity'],
                            properties: [
                                new OA\Property(
                                    property: 'product_id',
                                    type: 'integer',
                                    example: 1
                                ),
                                new OA\Property(
                                    property: 'quantity',
                                    type: 'integer',
                                    example: 2
                                )
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Orden creada correctamente'
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado'
            ),
            new OA\Response(
                response: 422,
                description: 'Datos inválidos o stock insuficiente'
            )
        ]
    )]
    public function store(OrderRequest $request): JsonResponse
    {
        $order = DB::transaction(function () use ($request) {

            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => 0,
                'status' => 'pending',
            ]);

            $total = 0;

            foreach ($request->items as $item) {

                $product = \App\Models\Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                 if ($product->stock < $item['quantity']) {
                    throw new HttpException(
                        422,
                        'Stock insuficiente para el producto: ' . $product->name
                    );
                }

                $subtotal = round($product->price * $item['quantity'], 2);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $product->decrement('stock', $item['quantity']);

                $total = round($total + $subtotal, 2);
            }

            $order->update([
                'total' => $total,
            ]);

            return $order;
        });

        return response()->json([
            'message' => 'Orden creada correctamente',
            'order' => $order,
        ], 201);
    }

    #[OA\Get(
        path: '/api/orders',
        summary: 'Listar órdenes del usuario autenticado',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de órdenes del usuario autenticado'
            ),
                new OA\Response(
                    response: 401,
                    description: 'No autenticado'
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.product')
            ->get();

        return response()->json($orders);
    }
}
