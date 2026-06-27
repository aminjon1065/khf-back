<?php

use App\Models\User;
use App\Modules\Identity\Contracts\AuthenticationManagerInterface;
use App\Modules\Identity\Contracts\AuthenticationProviderInterface;
use App\Modules\Identity\Exceptions\IdentityException;

it('registers and resolves external authentication providers (extension point)', function () {
    $manager = app(AuthenticationManagerInterface::class);

    $manager->extend(new class implements AuthenticationProviderInterface
    {
        public function name(): string
        {
            return 'ldap';
        }

        public function authenticate(array $credentials): ?User
        {
            return null;
        }
    });

    expect($manager->has('ldap'))->toBeTrue()
        ->and($manager->provider('ldap')->name())->toBe('ldap')
        ->and($manager->names())->toContain('ldap');
});

it('throws for an unregistered provider', function () {
    app(AuthenticationManagerInterface::class)->provider('nonexistent');
})->throws(IdentityException::class);
