<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->withCount('enrollments')
            ->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $enrollments = $user->enrollments()->with('product')->get();
        $products    = Product::published()->get();
        return view('admin.users.show', compact('user', 'enrollments', 'products'));
    }

    public function grantAccess(Request $request, User $user)
    {
        $data = $request->validate(['product_id' => 'required|exists:products,id']);

        Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $data['product_id']],
            ['source' => 'manual', 'status' => 'active']
        );

        return back()->with('success', 'Acesso liberado!');
    }

    public function revokeAccess(Request $request, User $user)
    {
        $data = $request->validate(['product_id' => 'required|exists:products,id']);

        Enrollment::where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->update(['status' => 'cancelled']);

        return back()->with('success', 'Acesso revogado.');
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate(['role' => 'required|in:admin,member']);
        $user->update($data);
        return back()->with('success', 'Função atualizada!');
    }
}
