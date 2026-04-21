<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;

use Inertia\Inertia;

class AdminController extends Controller
{

    function index()
    {
        $salesThisMonth = Payment::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->count();

        $paidPayments = Payment::where('status', 'paid')->get();
        $totalRevenue = $paidPayments->sum('amount');

        $lastMonthRevenue = Payment::where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount');

        $growthPercentage = $lastMonthRevenue > 0
            ? (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Active users calculation
        $activeUsers = User::count();

        // Users created last month
        $lastMonthActiveUsers = User::whereMonth('created_at', now()->subMonth()->month)->count();

        $activeUserGrowth = $lastMonthActiveUsers > 0
            ? (($activeUsers - $lastMonthActiveUsers) / $lastMonthActiveUsers) * 100
            : 0;

        $totalProduct = Product::count();

        $monthlyRevenue = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyRevenue[] = Payment::where('status', 'paid')
                ->whereMonth('created_at', $m)
                ->sum('amount');
        }

        $transactions = Payment::with('user') // eager load the user relationship
            ->latest()
            ->select([
                'id',
                'order_id',
                'amount',
                'status',
                'type',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at'
            ])
            ->paginate(10);

        return Inertia::render('Admin/Dashboard', [
            'monthlyRevenue' => $monthlyRevenue,
            'totalRevenue' => $totalRevenue,
            'growthPercentage' => round($growthPercentage, 1),
            'activeUsers' => $activeUsers,
            'activeUserGrowth' => round($activeUserGrowth, 1),
            'salesThisMonth' => $salesThisMonth,
            'totalProduct' => $totalProduct,
            'transactions' => $transactions
        ]);
    }

    public function update(HttpRequest $request, $id)
    {
    $request->validate([
        'status' => 'required|in:paid,pending,failed'
    ]);

    $transaction = Payment::findOrFail($id);

    $transaction->update([
        'status' => $request->status
    ]);

     return redirect()->route('admin.dashboard')
            ->with('success', 'Updated status successfully');
}
}
