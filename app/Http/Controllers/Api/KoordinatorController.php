<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaerahIrigasi;
use Illuminate\Http\Request;
use App\Models\MasterPermasalahan;
use Illuminate\Support\Facades\Auth;

class KoordinatorController extends Controller
{
    public function getDaerahIrigasiUser()
    {
        $user = Auth::user();

        $daerahIrigasis = DaerahIrigasi::whereHas('kabupatens', function ($q) use ($user) {
            $q->whereIn('kabupatens.id', $user->kabupatens->pluck('id'));
        })->get();

        return $daerahIrigasis;
    }
}
