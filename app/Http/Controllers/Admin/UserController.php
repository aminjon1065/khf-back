<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Modules\Identity\Actions\CreateUserAction;
use App\Modules\Identity\Actions\DeleteUserAction;
use App\Modules\Identity\Actions\UpdateUserAction;
use App\Modules\Identity\DTOs\CreateUserData;
use App\Modules\Identity\DTOs\UpdateUserData;
use App\Modules\Identity\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly CreateUserAction $createUser,
        private readonly UpdateUserAction $updateUser,
        private readonly DeleteUserAction $deleteUser,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

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
        $this->authorize('create', User::class);

        return Inertia::render('admin/users/form', ['roles' => $this->roleNames()]);
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('admin/users/form', [
            'roles' => $this->roleNames(),
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
        $this->authorize('create', User::class);

        $data = $request->validated();

        $this->createUser->handle(new CreateUserData(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            roles: [$data['role']],
        ));

        return to_route('admin.users.index')->with('success', 'Пользователь создан');
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        $this->updateUser->handle($user, new UpdateUserData(
            name: $data['name'],
            email: $data['email'],
            password: ! empty($data['password']) ? $data['password'] : null,
            roles: [$data['role']],
        ));

        return to_route('admin.users.index')->with('success', 'Сохранено');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->deleteUser->handle($user);

        return to_route('admin.users.index')->with('success', 'Удалено');
    }

    /**
     * @return list<string>
     */
    private function roleNames(): array
    {
        return array_values(array_map(strval(...), Role::query()->orderBy('name')->pluck('name')->all()));
    }
}
