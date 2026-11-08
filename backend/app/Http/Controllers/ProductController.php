<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductRequest;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'Listar productos',
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de productos'
            )
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(Product::all());
    }

    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Obtener un producto',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID del producto',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto encontrado'
            ),
            new OA\Response(
                response: 404,
                description: 'Producto no encontrado'
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        return response()->json(Product::findOrFail($id));
    }

    #[OA\Post(
        path: '/api/products',
        summary: 'Crear un producto',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
            required: ['name', 'price', 'stock'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'Laptop Lenovo'
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        nullable: true,
                        example: 'Laptop para trabajo y estudio'
                    ),
                    new OA\Property(
                        property: 'price',
                        type: 'number',
                        format: 'float',
                        example: 899.99
                    ),
                    new OA\Property(
                        property: 'stock',
                        type: 'integer',
                        example: 10
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Producto creado correctamente'
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado'
            ),
            new OA\Response(
                response: 422,
                description: 'Datos de validación incorrectos'
            )
        ]
    )]
    public function store(ProductRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            ]);

        return response()->json([
            'message' => 'Producto creado correctamente',
            'product' => $product,
            ], 201);
    }
    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Actualizar un producto',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID del producto',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'stock'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'Laptop Lenovo Actualizada'
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        nullable: true,
                        example: 'Descripción actualizada'
                    ),
                    new OA\Property(
                        property: 'price',
                        type: 'number',
                        format: 'float',
                        example: 949.99
                    ),
                    new OA\Property(
                        property: 'stock',
                        type: 'integer',
                        example: 15
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto actualizado correctamente'
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado'
            ),
            new OA\Response(
                response: 404,
                description: 'Producto no encontrado'
            ),
            new OA\Response(
                response: 422,
                description: 'Datos de validación incorrectos'
            )
        ]
    )]
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            ]);

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'product' => $product,
            ]);
            
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Eliminar un producto',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID del producto',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto eliminado correctamente'
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado'
            ),
            new OA\Response(
                response: 404,
                description: 'Producto no encontrado'
            )
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente'
            ]);
    }
}
