<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }
}
