<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * List all orders
     */
    public function index()
    {
        return Order::with('items')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Store order from offline sync
     */
  public function store(Request $request)
{
    try {
        DB::beginTransaction();

        // Validate
        $request->validate([
            'id' => 'required|uuid',
            'client_id' => 'required|uuid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.line_total' => 'required|numeric',
        ]);

        // 🔑 IMPORTANT: idempotent save
        $order = Order::updateOrCreate(
            ['id' => $request->id],
            [
                'client_id'  => $request->client_id,
                'created_by' => $request->user_id ?? 'desktop-app',
                'status'     => $request->status ?? 'draft',
                'subtotal'   => $request->subtotal,
                'total'      => $request->total,
            ]
        );

        // Always re-sync items
        OrderItem::where('order_id', $order->id)->delete();

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'line_total' => $item['line_total'],
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => 1,
            'message' => 'Order synced'
        ]);

    } catch (QueryException $e) {

        DB::rollBack();

        // Duplicate = already synced → OK
        if ($e->getCode() === '23000') {
            return response()->json([
                'status' => 1,
                'message' => 'Order already synced'
            ]);
        }

        return response()->json([
            'status' => 0,
            'code' => 'DB_UNAVAILABLE',
            'message' => 'Database unavailable'
        ], 503);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'status' => 0,
            'code' => 'SERVER_ERROR',
            'message' => 'Unexpected error'
        ], 500);
    }
}

}