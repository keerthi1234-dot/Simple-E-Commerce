<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // View Cart
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json($cartItems);
    }

    // Add to Cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $cart = Cart::where('user_id', auth()->id())
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($cart) {
            $cart->quantity += 1;
            $cart->save();
        } else {
            $cart = Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => 1
            ]);
        }

        return response()->json([
            'message' => 'Added to cart',
            'cart' => $cart
        ]);
    }

    // Remove single item
    public function remove($id)
    {
        $cartItem = Cart::where('user_id', auth()->id())
                        ->where('id', $id)
                        ->first();

        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item removed']);
    }

    // Clear cart
    public function clearCart()
    {
        Cart::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
}
