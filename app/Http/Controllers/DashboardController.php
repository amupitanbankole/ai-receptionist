<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Followup;
use App\Models\Lead;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalLeads = Lead::count();
        $hotLeads = Lead::where('temperature', 'hot')->count();
        $qualified = Lead::where('status', 'qualified')->count();
        $demos = Lead::where('status', 'demo')->count();
        $customers = Lead::where('status', 'customer')->count();
        $newLeads = Lead::where('status', 'new')->count();

        $recentActivities = Activity::with(['lead', 'company'])->latest('occurred_at')->limit(8)->get();
        $upcomingFollowups = Followup::with(['lead.company'])->where('status', 'pending')->orderBy('due_at')->limit(5)->get();

        return view('dashboard.index', compact(
            'totalLeads','hotLeads','qualified','demos','customers','newLeads',
            'recentActivities','upcomingFollowups'
        ));
    }
}
