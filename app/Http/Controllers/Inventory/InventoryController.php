<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    // ================= AJAX SEARCH (For DataTable) =================
    public function ajaxSearch(Request $request)
    {
        $query = $request->q;
        $category = $request->category;
        $stock = $request->stock;

        $products = Product::when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%$query%")
                        ->orWhere('product_code', 'like', "%$query%")
                        ->orWhere('category', 'like', "%$query%");
                });
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->when($stock === 'low', function ($q) {
                $q->where('quantity', '<=', 10);
            })
            ->when($stock === 'normal', function ($q) {
                $q->where('quantity', '>', 10);
            })
            ->get();

        // Add image URLs to products
        $products->transform(function ($product) {
            $product->image_url = $this->getImageUrl($product->image);
            return $product;
        });

        // Calculate stats for filtered results
        $lowStockCount = $products->where('quantity', '<=', 10)->count();
        $totalValue = $products->sum(function ($product) {
            return $product->price * $product->quantity;
        });

        return response()->json([
            'products' => $products,
            'stats' => [
                'low_stock_count' => $lowStockCount,
                'total_value' => $totalValue,
            ]
        ]);
    }

    public function index(Request $request)
    {
        // DataTable के लिए ALL products get करें
        $products = Product::latest()->get();

        // Calculate statistics
        $lowStockCount = Product::where('quantity', '<=', 10)->count();
        $totalValue = Product::sum(DB::raw('price * quantity'));
        $categories = Product::distinct()->pluck('category')->sort();

        return view('inventory.index', compact('products', 'lowStockCount', 'totalValue', 'categories'));
    }

    // ================= DataTable AJAX Data Source =================
    private function getDataTableData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        // Total records
        $totalRecords = Product::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Product::select('count(*) as allcount')->count();

        // Fetch records
        $records = Product::orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach($records as $record){
            $data_arr[] = array(
                "product_code" => $record->product_code,
                "name" => $record->name,
                "quantity" => $record->quantity,
                "price" => $record->price,
                "category" => $record->category,
                "image" => $this->getImageUrl($record->image),
                "actions" => view('inventory.actions', ['product' => $record])->render()
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    // ================= Helper function to get image URL =================
    private function getImageUrl($image)
    {
        if (!$image) {
            return asset('images/no-image.png');
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        return asset('storage/' . $image);
    }

    // ================= CREATE =================
    public function create()
    {
        return view('inventory.create');
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'quantity'    => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url'   => 'nullable|url',
        ]);

        // Generate product code
        $nextId = Product::max('id') + 1;
        $code = 'PRD' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $data = [
            'product_code' => $code,
            'name'         => $request->name,
            'quantity'     => $request->quantity,
            'price'        => $request->price,
            'category'     => $request->category,
            'description'  => $request->description,
        ];

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // File upload
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        } elseif ($request->filled('image_url')) {
            // URL upload
            $data['image'] = $request->image_url;
        }

        Product::create($data);

        return redirect()->route('inventory.index')
            ->with('success', 'Product added successfully with image!');
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('inventory.edit', compact('product'));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'quantity'    => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url'   => 'nullable|url',
        ]);

        $product = Product::findOrFail($id);

        $data = [
            'name'        => $request->name,
            'quantity'    => $request->quantity,
            'price'       => $request->price,
            'category'    => $request->category,
            'description' => $request->description,
        ];

        // Handle Image Upload/Update
        if ($request->hasFile('image')) {
            // Delete old image if exists and is not URL
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }

            // Upload new image
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }
        elseif ($request->filled('image_url')) {
            // URL upload
            $data['image'] = $request->image_url;
        }
        elseif ($request->has('remove_image')) {
            // Remove image
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        }

        $product->update($data);

        return redirect()->route('inventory.index')
            ->with('success', 'Product updated successfully!');
    }

    // ================= SHOW =================
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('inventory.show', compact('product'));
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete image if exists and is not URL
        if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Product deleted successfully!');
    }

    // ================= SIMPLE BARCODE PREVIEW =================
    public function barcodePreview(Request $request)
    {
        try {
            // Get product codes from request
            $productCodes = $request->input('product_ids');

            // If it's a string, convert to array
            if (is_string($productCodes)) {
                $productCodes = explode(',', $productCodes);
            }

            // Clean and filter empty values
            $productCodes = array_filter(array_map('trim', (array)$productCodes));

            // If no products selected, return to inventory page
            if (empty($productCodes)) {
                return redirect()->route('inventory.index')
                    ->with('error', 'Please select at least one product.');
            }

            // Get products from database
            $products = Product::whereIn('product_code', $productCodes)->get();

            // If no products found
            if ($products->isEmpty()) {
                // Try with first 3 products as fallback
                $products = Product::limit(3)->get();

                if ($products->isEmpty()) {
                    return redirect()->route('inventory.index')
                        ->with('error', 'No products found in database.');
                }
            }

            // Return preview page
            return view('inventory.barcode.preview', compact('products'));

        } catch (\Exception $e) {
            // Log error
            Log::error('Barcode Preview Error: ' . $e->getMessage());

            // Redirect back with error
            return redirect()->route('inventory.index')
                ->with('error', 'Error loading barcode preview. Please try again.');
        }
    }

    // ================= BARCODE PDF DOWNLOAD =================
    public function barcodeDownload(Request $request)
    {
        try {
            // Get product codes from session or request
            $productCodes = $request->product_ids ? explode(',', $request->product_ids) : [];

            if (empty($productCodes)) {
                return redirect()->route('inventory.index')
                    ->with('error', 'No products selected for barcode.');
            }

            // Get products from database
            $products = Product::whereIn('product_code', $productCodes)->get();

            if ($products->isEmpty()) {
                return redirect()->route('inventory.index')
                    ->with('error', 'No products found.');
            }

            // Generate PDF
            $pdf = Pdf::loadView('inventory.barcode.pdf-view', compact('products'));

            // Download PDF with filename
            $filename = 'product-barcodes-' . date('Y-m-d-H-i-s') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            // Log error
            Log::error('Barcode Download Error: ' . $e->getMessage());

            // Redirect back with error
            return redirect()->route('inventory.barcode.preview')
                ->with('error', 'Error generating PDF. Please try again.');
        }
    }
}
