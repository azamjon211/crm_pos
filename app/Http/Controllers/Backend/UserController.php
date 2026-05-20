<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();

        $users = User::with('shop')
            ->when(!$isSuperAdmin, fn($q) => $q->where('shop_id', session('shop_id') ?? auth()->user()->shop_id))
            ->orderBy('name')
            ->paginate(20);

        return view('backend.users.index', compact('users', 'isSuperAdmin'));
    }

    public function create(Request $request): View
    {
        $shops = auth()->user()->isSuperAdmin()
            ? Shop::orderBy('name')->get()
            : collect();
        $roles          = $this->availableRoles();
        $selectedShopId = (int) $request->query('shop_id');

        return view('backend.users.create', compact('shops', 'roles', 'selectedShopId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $shopId = $isSuperAdmin
            ? $request->input('shop_id')
            : (session('shop_id') ?? auth()->user()->shop_id);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')],
            'password' => 'required|string|min:6',
            'role'     => ['required', Rule::in($this->availableRoles())],
            'shop_id'  => $isSuperAdmin ? 'required|exists:shops,id' : 'nullable',
            'is_active' => 'boolean',
        ]);

        $data['shop_id']   = $shopId;
        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active');

        User::create($data);

        return redirect()->route('backend.users.index')->with('success', 'Foydalanuvchi qo\'shildi.');
    }

    public function edit(User $user): View
    {
        $shops = auth()->user()->isSuperAdmin()
            ? Shop::orderBy('name')->get()
            : collect();
        $roles = $this->availableRoles();

        return view('backend.users.edit', compact('user', 'shops', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role'     => ['required', Rule::in($this->availableRoles())],
            'shop_id'  => $isSuperAdmin ? 'required|exists:shops,id' : 'nullable',
            'is_active' => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($isSuperAdmin) {
            $data['shop_id'] = $request->input('shop_id');
        }

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        return redirect()->route('backend.users.index')->with('success', 'Foydalanuvchi yangilandi.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'O\'zingizni o\'chira olmaysiz.');
        }

        $user->delete();

        return redirect()->route('backend.users.index')->with('success', 'Foydalanuvchi o\'chirildi.');
    }

    private function availableRoles(): array
    {
        if (auth()->user()->isSuperAdmin()) {
            return [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_CASHIER];
        }
        return [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_CASHIER];
    }
}
