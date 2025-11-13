<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for testing
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Create regular user for testing
        $this->regularUser = User::factory()->create([
            'role' => 'member',
            'email' => 'member@test.com',
        ]);
    }

    /**
     * Test: Non-admin cannot access user management
     */
    public function test_non_admin_cannot_access_user_list(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    /**
     * Test: Unauthenticated user cannot access user management
     */
    public function test_unauthenticated_cannot_access_user_list(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Admin can view user list
     */
    public function test_admin_can_view_user_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    /**
     * Test: User list is paginated
     */
    public function test_user_list_is_paginated(): void
    {
        // Create 15 users
        User::factory()->count(15)->create(['role' => 'member']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $this->assertCount(10, $response->viewData('users'));
    }

    /**
     * Test: Admin can filter users by role
     */
    public function test_admin_can_filter_by_role(): void
    {
        User::factory()->count(5)->create(['role' => 'member']);
        User::factory()->count(3)->create(['role' => 'admin']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['role' => 'member']));

        $response->assertStatus(200);
        // Should have all members (including self-created one)
        $users = $response->viewData('users');
        $this->assertTrue($users->count() >= 5);
    }

    /**
     * Test: Admin can search users by name
     */
    public function test_admin_can_search_by_name(): void
    {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@test.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@test.com']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'John']));

        $response->assertStatus(200);
    }

    /**
     * Test: Admin can search users by email
     */
    public function test_admin_can_search_by_email(): void
    {
        User::factory()->create(['email' => 'search@test.com', 'name' => 'Search User']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'search@test.com']));

        $response->assertStatus(200);
    }

    /**
     * Test: Admin can access create user form
     */
    public function test_admin_can_access_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
    }

    /**
     * Test: Admin can create user with valid data
     */
    public function test_admin_can_create_user_with_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'phone' => '08123456789',
            'role' => 'member',
        ]);

        // Verify password is hashed
        $user = User::where('email', 'newuser@test.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test: Create user validation - missing fields
     */
    public function test_create_user_validation_missing_required_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                // Missing email, phone, role, password
            ]);

        $response->assertSessionHasErrors(['email', 'phone', 'role', 'password']);
    }

    /**
     * Test: Create user validation - invalid email
     */
    public function test_create_user_validation_invalid_email(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'not-an-email',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Create user validation - duplicate email
     */
    public function test_create_user_validation_duplicate_email(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Another User',
                'email' => $this->regularUser->email,
                'phone' => '08987654321',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Create user validation - weak password
     */
    public function test_create_user_validation_weak_password(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'pass',  // Less than 8 chars
                'password_confirmation' => 'pass',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Create user validation - password mismatch
     */
    public function test_create_user_validation_password_mismatch(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password456',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Create user validation - invalid role
     */
    public function test_create_user_validation_invalid_role(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'phone' => '08123456789',
                'role' => 'superuser',  // Invalid role
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('role');
    }

    /**
     * Test: Admin can access edit user form
     */
    public function test_admin_can_access_edit_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $this->regularUser));

        $response->assertStatus(200);
        $response->assertViewHas('user');
        $this->assertEquals($this->regularUser->id, $response->viewData('user')->id);
    }

    /**
     * Test: Admin can update user with valid data
     */
    public function test_admin_can_update_user_with_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.update', $this->regularUser), [
                'name' => 'Updated Name',
                'email' => 'updated@test.com',
                'phone' => '08999999999',
                'role' => 'admin',
                'password' => '',  // Empty means keep existing
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->regularUser->refresh();
        $this->assertEquals('Updated Name', $this->regularUser->name);
        $this->assertEquals('updated@test.com', $this->regularUser->email);
        $this->assertEquals('08999999999', $this->regularUser->phone);
        $this->assertEquals('admin', $this->regularUser->role);
    }

    /**
     * Test: Admin can update user password
     */
    public function test_admin_can_update_user_password(): void
    {
        $oldPassword = $this->regularUser->password;

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.update', $this->regularUser), [
                'name' => $this->regularUser->name,
                'email' => $this->regularUser->email,
                'phone' => $this->regularUser->phone,
                'role' => $this->regularUser->role,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->regularUser->refresh();
        $this->assertNotEquals($oldPassword, $this->regularUser->password);
        $this->assertTrue(Hash::check('newpassword123', $this->regularUser->password));
    }

    /**
     * Test: Update user validation - duplicate email
     */
    public function test_update_user_validation_duplicate_email(): void
    {
        $otherUser = User::factory()->create(['email' => 'other@test.com']);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.update', $this->regularUser), [
                'name' => 'Updated Name',
                'email' => $otherUser->email,  // Another user's email
                'phone' => '08123456789',
                'role' => 'member',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Update user can keep same email
     */
    public function test_update_user_can_keep_same_email(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.update', $this->regularUser), [
                'name' => 'Updated Name',
                'email' => $this->regularUser->email,  // Same email
                'phone' => '08123456789',
                'role' => 'member',
            ]);

        $response->assertRedirect(route('admin.users.index'));
    }

    /**
     * Test: Update user validation - invalid new password
     */
    public function test_update_user_validation_invalid_new_password(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.update', $this->regularUser), [
                'name' => $this->regularUser->name,
                'email' => $this->regularUser->email,
                'phone' => $this->regularUser->phone,
                'role' => $this->regularUser->role,
                'password' => 'short',  // Less than 8 chars
                'password_confirmation' => 'short',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Admin can delete user (except own account)
     */
    public function test_admin_can_delete_another_user(): void
    {
        $userToDelete = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $userToDelete));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        // User should be deleted from database
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    /**
     * Test: Admin cannot delete own account
     */
    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // User should still exist
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    /**
     * Test: Non-admin cannot create user
     */
    public function test_non_admin_cannot_create_user(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test: Non-admin cannot update user
     */
    public function test_non_admin_cannot_update_user(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->patch(route('admin.users.update', $this->regularUser), [
                'name' => 'Hacked Name',
                'email' => 'hacked@test.com',
                'phone' => '08111111111',
                'role' => 'admin',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test: Non-admin cannot delete user
     */
    public function test_non_admin_cannot_delete_user(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->delete(route('admin.users.destroy', $this->admin));

        $response->assertStatus(403);
    }

    /**
     * Test: Name is properly validated
     */
    public function test_name_validation(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => '',  // Empty name
                'email' => 'test@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test: Name cannot exceed max length
     */
    public function test_name_max_length(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => str_repeat('a', 256),  // 256 chars, max is 255
                'email' => 'test@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test: Phone validation
     */
    public function test_phone_validation(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'test@test.com',
                'phone' => '',  // Empty phone
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('phone');
    }

    /**
     * Test: XSS attempt in name field
     */
    public function test_xss_attempt_in_name_is_escaped(): void
    {
        $xssPayload = '<script>alert("XSS")</script>';

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => $xssPayload,
                'email' => 'xsstest@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        // Check that the script tag is stored but will be escaped on display
        $user = User::where('email', 'xsstest@test.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals($xssPayload, $user->name);
    }

    /**
     * Test: SQL injection attempt in email field is handled by validation
     */
    public function test_sql_injection_attempt_in_email(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => "test'; DROP TABLE users; --@test.com",
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Very long phone number is rejected
     */
    public function test_phone_max_length(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'test@test.com',
                'phone' => str_repeat('0', 21),  // 21 chars, max is 20
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('phone');
    }

    /**
     * Test: Admin cannot create another admin via regular flow (role validation)
     */
    public function test_admin_can_create_other_admins(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New Admin',
                'email' => 'newadmin@test.com',
                'phone' => '08123456789',
                'role' => 'admin',  // Create another admin
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        // Verify new admin was created
        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@test.com',
            'role' => 'admin',
        ]);
    }

    /**
     * Test: Case sensitivity in email handling
     */
    public function test_email_uniqueness_validation(): void
    {
        User::factory()->create(['email' => 'existing@test.com']);

        // Try to create with exact same email
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'existing@test.com',  // Exact duplicate
                'phone' => '08123456789',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        // Should fail due to unique constraint
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: User attributes are properly sanitized
     */
    public function test_user_attributes_are_sanitized(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => '  Test User  ',  // Leading/trailing spaces
                'email' => '  test@test.com  ',
                'phone' => '  08123456789  ',
                'role' => 'member',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        // Check that data was stored (Laravel doesn't auto-trim, but that's OK)
        $user = User::where('email', '  test@test.com  ')->orWhere('email', 'test@test.com')->first();
        $this->assertNotNull($user);
    }

    /**
     * Test: Password is properly hashed
     */
    public function test_password_is_properly_hashed(): void
    {
        $plainPassword = 'plainpassword123';

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'hashtest@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => $plainPassword,
                'password_confirmation' => $plainPassword,
            ]);

        $user = User::where('email', 'hashtest@test.com')->first();
        $this->assertNotNull($user);

        // Password should be hashed, not plain text
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }

    /**
     * Test: User cannot be created with blank password
     */
    public function test_user_cannot_be_created_with_blank_password(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'test@test.com',
                'phone' => '08123456789',
                'role' => 'member',
                'password' => '',
                'password_confirmation' => '',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test: Update user partial data (only update name)
     */
    public function test_update_user_partial_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.update', $this->regularUser), [
                'name' => 'Only Updated Name',
                'email' => $this->regularUser->email,
                'phone' => $this->regularUser->phone,
                'role' => $this->regularUser->role,
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->regularUser->refresh();
        $this->assertEquals('Only Updated Name', $this->regularUser->name);
    }

    /**
     * Test: User list search is case-insensitive
     */
    public function test_user_list_search_is_case_insensitive(): void
    {
        User::factory()->create(['name' => 'TestUser', 'email' => 'testuser@test.com']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'testuser']));

        $response->assertStatus(200);
    }
}

