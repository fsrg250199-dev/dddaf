<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $activeClients = Client::where('is_active', true)->count();
        $inactiveClients = Client::where('is_active', false)->count();

        $recentClients = Client::orderBy('created_at', 'desc')->take(5)->get();

        $monthlyStats = [
            'clients_this_month' => Client::whereMonth('created_at', now()->month)->count(),
            'clients_last_month' => Client::whereMonth('created_at', now()->subMonth()->month)->count(),
        ];

        return view('admin.dashboard', compact(
            'totalClients',
            'activeClients',
            'inactiveClients',
            'recentClients',
            'monthlyStats'
        ));
    }
}
