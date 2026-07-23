<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    /**
     * The POS terminal screen. Shows only sellable (priced) services as
     * buttons; NULL-priced services are shown disabled so they can never be
     * sold at a wrong price.
     */
    public function index()
    {
        $cashier = Auth::guard('pos')->user();

        // Every service, so the cashier can see (disabled) unpriced ones too.
        $services = Service::orderBy('name')->get(['id', 'name', 'price', 'icon', 'image']);

        return view('pos.index', [
            'cashier'  => $cashier,
            'services' => $services,
        ]);
    }
}
