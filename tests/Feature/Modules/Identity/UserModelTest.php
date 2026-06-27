<?php

use App\Models\User;
use App\Modules\Identity\Enums\UserStatus;
use App\Modules\Identity\Models\Activity;
use App\Modules\Identity\Models\UserProfile;
use App\Modules\Media\Models\Media;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

it('generates a secondary uuid and defaults to active status', function () {
    $user = User::factory()->create();

    expect($user->uuid)->toBeString()
        ->and(strlen($user->uuid))->toBe(36)
        ->and($user->status)->toBe(UserStatus::Active);
});

it('soft deletes users', function () {
    $user = User::factory()->create();

    $user->delete();

    $this->assertSoftDeleted($user);
});

it('is verifiable and can issue Sanctum tokens', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(MustVerifyEmail::class)
        ->and(in_array(HasApiTokens::class, class_uses_recursive($user), true))->toBeTrue();

    $token = $user->createToken('test');
    expect($token->plainTextToken)->not->toBe('');
});

it('relates to its profile, avatar and activities', function () {
    $user = User::factory()->create();
    UserProfile::create(['user_id' => $user->id, 'bio' => 'About me', 'meta' => ['twitter' => '@x']]);
    Activity::factory()->create(['user_id' => $user->id]);

    $media = Media::factory()->create();
    $user->avatar_media_id = $media->id;
    $user->save();

    $user->refresh()->load(['profile', 'avatar', 'activities']);

    expect($user->profile->bio)->toBe('About me')
        ->and($user->profile->meta)->toBe(['twitter' => '@x'])
        ->and($user->avatar->is($media))->toBeTrue()
        ->and($user->activities)->toHaveCount(1);
});
