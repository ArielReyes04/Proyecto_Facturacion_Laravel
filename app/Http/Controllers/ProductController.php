<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AuditLog;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\RestoreProductRequest;
use App\Http\Requests\ForceDeleteProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Búsqueda por nombre o descripción
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Registros por página
        $perPage = $request->input('per_page', 10);
        $perPage = max(1, min(100, (int) $perPage)); // Limit between 1 and 100

        $products = $query->latest()->paginate($perPage)->withQueryString();

        // Handle AJAX requests
        if ($request->ajax() || $request->has('ajax')) {
            try {
                $tableHtml = view('products.partials.products-table', compact('products'))->render();
                $paginationHtml = view('products.partials.products-pagination', compact('products'))->render();
                
                return response()->json([
                    'table' => $tableHtml,
                    'pagination' => $paginationHtml,
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'debug' => [
                        'search' => $request->input('search'),
                        'per_page' => $perPage,
                        'count' => $products->count()
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        }

        return view('products.index', compact('products'));
    }


    public function create()
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        
        // Convertir coma a punto en el precio si existe
        if (isset($validated['price'])) {
            $validated['price'] = str_replace(',', '.', $validated['price']);
        }

        $product = Product::create($validated);

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'table_name' => 'products',
            'record_id' => $product->id,
            'old_values' => null,
            'new_values' => json_encode($validated),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        
        // Convertir coma a punto en el precio si existe
        if (isset($validated['price'])) {
            $validated['price'] = str_replace(',', '.', $validated['price']);
        }

        $oldValues = $product->toArray();

        $product->update($validated);

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'table_name' => 'products',
            'record_id' => $product->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($validated),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(DeleteProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        $oldValues = $product->toArray();

        $product->delete();

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'table_name' => 'products',
            'record_id' => $product->id,
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode([
                'razon' => $validated['razon'],
                'deleted_by' => Auth::user() ? Auth::user()->name : 'Sistema',

            ]),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function restore(RestoreProductRequest $request, $id)
    {
        $validated = $request->validated();

        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        // Registrar en audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'restore',
            'table_name' => 'products',
            'record_id' => $product->id,
            'old_values' => json_encode(['deleted_at' => $product->deleted_at]),
            'new_values' => json_encode(['razon' => $validated['razon'], 'restored_by' => Auth::user()->name]),
        ]);

        return redirect()->route('products.index')->with('success', 'Producto restaurado correctamente.');
    }

    public function forceDelete(ForceDeleteProductRequest $request, $id)
    {
        $validated = $request->validated();

        DB::beginTransaction(); // Iniciar transacción

        try {
            $product = Product::withTrashed()->findOrFail($id);
            $oldValues = $product->toArray();

            // Registrar en audit log antes de eliminar
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'force_delete',
                'table_name' => 'products',
                'record_id' => $product->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode([
                    'razon' => $validated['razon'],
                    'force_deleted_by' => Auth::user()->name
                ]),
            ]);

            // Intentar eliminación permanente
            $product->forceDelete();

            DB::commit(); // Confirmar transacción

            return redirect()->route('products.index')->with('success', 'Producto eliminado permanentemente.');
        } catch (\Exception $e) {
            DB::rollback(); // Revertir transacción en caso de error

            // Retornar mensaje de error amigable si tiene relaciones
            $errorMessage = 'No se pudo eliminar el producto.';
            if (str_contains($e->getMessage(), 'FOREIGN KEY')) {
                $errorMessage .= ' El producto tiene facturas asociadas.';
            }

            return redirect()->route('products.index')->with('error', $errorMessage);
        }
    }


    public function eliminados(Request $request)
    {
        $query = Product::onlyTrashed();

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Cantidad de registros por página
        $perPage = $request->input('per_page', 10);

        $productosEliminados = $query->orderBy('deleted_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('products.eliminados', ['products' => $productosEliminados]);
    }

    public function validateField(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $productId = $request->input('product_id'); // For edit validation
        
        $errors = [];
        $valid = true;
        
        switch ($field) {
            case 'name':
                if (empty($value)) {
                    $errors[] = 'El nombre del producto es obligatorio.';
                    $valid = false;
                } elseif (strlen($value) > 255) {
                    $errors[] = 'El nombre no puede exceder 255 caracteres.';
                    $valid = false;
                } else {
                    // Check uniqueness
                    $query = Product::where('name', $value);
                    if ($productId) {
                        $query->where('id', '!=', $productId);
                    }
                    if ($query->exists()) {
                        $errors[] = 'Ya existe un producto con este nombre.';
                        $valid = false;
                    }
                }
                break;
                
            case 'price':
                if (empty($value)) {
                    $errors[] = 'El precio es obligatorio.';
                    $valid = false;
                } elseif (!preg_match('/^\d+([,.]?\d{1,2})?$/', $value)) {
                    $errors[] = 'El precio debe ser un número válido con máximo 2 decimales.';
                    $valid = false;
                } else {
                    $numericValue = floatval(str_replace(',', '.', $value));
                    if ($numericValue <= 0) {
                        $errors[] = 'El precio debe ser mayor que 0.';
                        $valid = false;
                    } elseif ($numericValue > 99999999.99) {
                        $errors[] = 'El precio no puede exceder 99,999,999.99.';
                        $valid = false;
                    }
                }
                break;
                
            case 'stock':
                if ($value === '' || $value === null) {
                    $errors[] = 'El stock es obligatorio.';
                    $valid = false;
                } elseif (!is_numeric($value) || !ctype_digit($value)) {
                    $errors[] = 'El stock debe ser un número entero.';
                    $valid = false;
                } elseif (intval($value) < 0) {
                    $errors[] = 'El stock no puede ser negativo.';
                    $valid = false;
                }
                break;
                
            case 'description':
                if (!empty($value) && strlen($value) > 1000) {
                    $errors[] = 'La descripción no puede exceder 1000 caracteres.';
                    $valid = false;
                }
                break;
        }
        
        return response()->json([
            'valid' => $valid,
            'errors' => $errors
        ]);
    }

    public function getProduct($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // Obtener todos los productos
    public function getProducts()
    {
        return response()->json(Product::all());
    }

    // Obtener un producto por ID
    public function getProductById($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        return response()->json($product);
    }

    // Crear producto
    public function createProduct(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();

            // Convertir coma a punto en el precio si existe
            if (isset($validated['price'])) {
                $validated['price'] = str_replace(',', '.', $validated['price']);
            }

            $product = Product::create($validated);

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'table_name' => 'products',
                'record_id' => $product->id,
                'old_values' => null,
                'new_values' => json_encode($validated),
            ]);

            return response()->json([
                'message' => 'Producto creado exitosamente.',
                'product' => $product
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación.',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Actualizar producto
    public function updateProduct(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $validated = $request->validated();

            // Convertir coma a punto en el precio si existe
            if (isset($validated['price'])) {
                $validated['price'] = str_replace(',', '.', $validated['price']);
            }

            $oldValues = $product->toArray();

            $product->update($validated);

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'table_name' => 'products',
                'record_id' => $product->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($validated),
            ]);

            return response()->json([
                'message' => 'Producto actualizado exitosamente.',
                'product' => $product
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación.',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar (soft delete) producto
    public function deleteProduct(DeleteProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $validated = $request->validated();

            $oldValues = $product->toArray();

            $product->delete();

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'table_name' => 'products',
                'record_id' => $product->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode([
                    'razon' => $validated['razon'],
                    'deleted_by' => Auth::user() ? Auth::user()->name : 'Sistema',
                ]),
            ]);

            return response()->json([
                'message' => 'Producto eliminado exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Restaurar producto eliminado
    public function restoreProduct(RestoreProductRequest $request, $id)
    {
        try {
            $product = Product::withTrashed()->find($id);

            if (!$product) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $validated = $request->validated();

            $product->restore();

            // Registrar en audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'restore',
                'table_name' => 'products',
                'record_id' => $product->id,
                'old_values' => json_encode(['deleted_at' => $product->deleted_at]),
                'new_values' => json_encode(['razon' => $validated['razon'], 'restored_by' => Auth::user()->name]),
            ]);

            return response()->json([
                'message' => 'Producto restaurado exitosamente.',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar permanentemente producto
    public function forceDeleteProduct(DeleteProductRequest $request, $id)
    {
        $validated = $request->validated();

        DB::beginTransaction(); // Iniciar transacción

        try {
            $product = Product::withTrashed()->findOrFail($id);
            $oldValues = $product->toArray();

            // Registrar en audit log antes de eliminar
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'force_delete',
                'table_name' => 'products',
                'record_id' => $product->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode([
                    'razon' => $validated['razon'],
                    'force_deleted_by' => Auth::user()->name,
                ]),
            ]);

            // Intentar eliminación permanente
            $product->forceDelete();

            DB::commit(); // Confirmar transacción

            return response()->json(['message' => 'Producto eliminado permanentemente.']);

        } catch (\Exception $e) {
            DB::rollback(); // Revertir transacción en caso de error

            $errorMessage = 'No se pudo eliminar el producto.';
            if (str_contains($e->getMessage(), 'FOREIGN KEY')) {
                $errorMessage .= ' El producto tiene facturas asociadas.';
            }

            return response()->json([
                'error' => $errorMessage,
                'details' => $e->getMessage()
            ], 500);
        }
    }

}
