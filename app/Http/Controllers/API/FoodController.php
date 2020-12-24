<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $types = $request->input('types');

        $price_form = $request->input('price_form');
        $price_to = $request->input('price_to');

        $rate_form = $request->input('rate_form');
        $rate_to = $request->input('rate_to');

        if ($id) {
            $food = Food::find($id);
            if ($food) {
                return ResponseFormatter::success($food, 'data produk berhasil diambil');
            } else {
                return ResponseFormatter::error(null, 'data produk tidak ada', 404);
            }
        }

        $food = Food::query();
        if ($name) {
            $food->where('name', 'like', '%' . $name . '%');
        }
        if ($types) {
            $food->where('types', 'like', '%' . $types . '%');
        }
        if ($price_form) {
            $food->where('price', '>=',  $price_form);
        }
        if ($price_to) {
            $food->where('price', '<=',  $price_to);
        }
        if ($rate_form) {
            $food->where('rate', '>=',  $rate_form);
        }
        if ($rate_to) {
            $food->where('rate', '<=',  $rate_to);
        }

        return ResponseFormatter::success(
            $food->paginate($limit),
            'data list produk berhasil diambil'
        );
    }
}
