<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

test('admin can view the logs page', function () {
    Log::info('admin.logs.test', ['message' => 'hello-from-test']);

    $role = Role::findOrCreate('admin');
    $user = User::factory()->create();
    $user->assignRole($role);

    $response = $this
        ->actingAs($user)
        ->get('/admin/logs');

    $response->assertOk();
    $response->assertSee('admin.logs.test');
});
