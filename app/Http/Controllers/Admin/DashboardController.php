<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Product;
use App\Models\User;
use App\Models\WebhookEvent;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products'    => Product::count(),
            'published_products'=> Product::where('status', 'published')->count(),
            'total_members'     => User::where('role', 'member')->count(),
            'total_enrollments' => Enrollment::where('status', 'active')->count(),
            'webhook_events'    => WebhookEvent::count(),
            'pending_events'    => WebhookEvent::where('status', 'pending')->count(),
        ];

        $recentEnrollments = Enrollment::with(['user', 'product'])
            ->latest()->limit(10)->get();

        $recentEvents = WebhookEvent::latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentEnrollments', 'recentEvents'));
    }
}
