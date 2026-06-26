<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $items = User::query()
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->map(fn (User $u): array => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->getRoleNames()->first(),
                'created' => $u->created_at?->format('d.m.Y'),
            ]);

        return Inertia::render('admin/users/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/users/form', ['roles' => ['admin', 'editor']]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/form', [
            'roles' => ['admin', 'editor'],
            'item' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
            ],
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);
        $user->email_verified_at = now();
        $user->save();

        $user->syncRoles([$data['role']]);

        return to_route('admin.users.index')->with('success', 'Пользователь создан');
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $user->syncRoles([$data['role']]);

        return to_route('admin.users.index')->with('success', 'Сохранено');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Нельзя удалить себя');
        }

        $user->delete();

        return to_route('admin.users.index')->with('success', 'Удалено');
    }
}
