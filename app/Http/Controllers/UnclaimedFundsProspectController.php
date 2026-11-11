<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnclaimedFundsProspectController extends Controller
{
    public function index(Request $request): View
    {
        $stateCode = strtoupper((string) $request->input('state', ''));
        $status = (string) $request->input('status', '');

        $prospects = Prospect::with('state')
            ->when($stateCode !== '', fn ($query) => $query->whereHas('state', fn ($state) => $state->where('code', $stateCode)))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('unclaimed-funds.prospects.index', compact('prospects'));
    }
}
