<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // -----------------------------------------------------
    // CHECKOUT — CREATE ORDER FROM CART TABLE
    // -----------------------------------------------------
    public function checkout(Request $request)
    {
        $userId = auth()->id();

        // Fetch cart items for logged-in user
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Step 1: Calculate total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item->product->price * $item->quantity;
            }

            // Step 2: Create Order
            $order = Order::create([
                'user_id'      => $userId,
                'total_amount' => $total
            ]);

            // Step 3: Create OrderItems & Reduce Stock
            foreach ($cartItems as $item) {

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->price
                ]);

                // Reduce stock
                $product = Product::find($item->product_id);

                if ($product->stock < $item->quantity) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Not enough stock for '.$product->name
                    ], 400);
                }

                $product->stock -= $item->quantity;
                $product->save();
            }

            DB::commit();

            // Step 4: Clear cart items from DB
            Cart::where('user_id', $userId)->delete();

            return response()->json([
                'message' => 'Order placed successfully',
                'order'   => $order,
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Order failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // -----------------------------------------------------
    // ADMIN — LIST ALL ORDERS
    // -----------------------------------------------------
    public function allOrders()
    {
        $orders = Order::with('items.product', 'user')->get();

        return response()->json([
            'orders' => $orders
        ]);
    }
}
