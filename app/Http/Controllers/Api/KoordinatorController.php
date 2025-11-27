<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaerahIrigasi;
use Illuminate\Http\Request;
use App\Models\MasterPermasalahan;
use Illuminate\Support\Facades\Auth;

class KoordinatorController extends Controller
{
    public function getDaerahIrigasiUser(Request $request)
    {
        $user = Auth::user();

        $query = DaerahIrigasi::whereHas('kabupatens', function ($q) use ($user) {
            $q->whereIn('kabupatens.id', $user->kabupatens->pluck('id'));
        });

        // 🔍 Filter hanya DI Induk jika dikirim ?is_induk=1
        if ($request->filled('is_induk') && $request->is_induk == 1) {
            $query->whereNull('parent_id');
        }


        return $query->get();
    }
}
