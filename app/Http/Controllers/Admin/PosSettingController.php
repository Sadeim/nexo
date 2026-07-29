<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\PosSettings;
use Illuminate\Http\Request;

/**
 * POS-only knobs, kept apart from the big website Settings form so the two
 * can't clobber each other.
 */
class PosSettingController extends Controller
{
    public function index()
    {
        return view('admin.pos_settings.index', [
            'cardFee' => PosSettings::cardFee(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'pos_card_fee' => 'required|numeric|min:0|max:1000',
        ]);

        Setting::updateOrCreate(
            ['key' => PosSettings::CARD_FEE_KEY],
            ['value' => number_format((float) $data['pos_card_fee'], 2, '.', '')]
        );

        return back()->with('success', 'POS settings saved.');
    }
}
