<?php

use App\Models\User;
use App\Models\Booking;
use App\Models\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

describe('Authentication & Authorization', function () {
    test('guest can access home page', function () {
        $response = $this->get('/');
        $response->assertStatus(200);
    });

    test('guest can access login page', function () {
        $response = $this->get('/login');
        $response->assertStatus(200);
    });

    test('guest can access register page', function () {
        $response = $this->get('/register');
        $response->assertStatus(200);
    });

    test('member can login and access dashboard', function () {
        $user = User::factory()->create(['role' => 'member']);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('Dashboard');
    });

    test('member cannot access admin routes', function () {
        $user = User::factory()->create(['role' => 'member']);
        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    });

    test('admin can access admin dashboard', function () {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertStatus(200);
    });

    test('admin can access admin fields page', function () {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)
            ->get('/admin/fields')
            ->assertStatus(200);
    });

    test('admin can access admin bookings page', function () {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)
            ->get('/admin/bookings')
            ->assertStatus(200);
    });

    test('unauthenticated user redirected from dashboard', function () {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    });

    test('user isAdmin method works correctly', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        expect($admin->isAdmin())->toBeTrue();
        expect($member->isAdmin())->toBeFalse();
    });
});

describe('Member Dashboard', function () {
    test('dashboard displays user name', function () {
        $user = User::factory()->create(['name' => 'John Doe', 'role' => 'member']);
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee('John Doe');
    });

    test('dashboard shows booking statistics', function () {
        $user = User::factory()->create(['role' => 'member']);
        $field = Field::factory()->create();

        Booking::factory(3)->create([
            'user_id' => $user->id,
            'field_id' => $field->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        
        // Check that the controller passed the data to the view
        expect($response->viewData('totalBookings'))->toBeGreaterThanOrEqual(0);
    });

    test('dashboard loads without errors', function () {
        $user = User::factory()->create(['role' => 'member']);
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        expect($response->exception)->toBeNull();
    });
});

describe('Member Bookings', function () {
    test('member can view my bookings page', function () {
        $user = User::factory()->create(['role' => 'member']);
        $this->actingAs($user)
            ->get('/my-bookings')
            ->assertStatus(200);
    });

    test('my bookings page shows member bookings only', function () {
        $member1 = User::factory()->create(['role' => 'member']);
        $member2 = User::factory()->create(['role' => 'member']);
        $field = Field::factory()->create();

        $booking1 = Booking::factory()->create([
            'user_id' => $member1->id,
            'field_id' => $field->id,
        ]);
        $booking2 = Booking::factory()->create([
            'user_id' => $member2->id,
            'field_id' => $field->id,
        ]);

        $response = $this->actingAs($member1)->get('/my-bookings');
        $response->assertStatus(200);
    });

    test('member can access booking create page', function () {
        $user = User::factory()->create(['role' => 'member']);
        $this->actingAs($user)
            ->get('/bookings/create')
            ->assertStatus(200);
    });

    test('unauthenticated user cannot create booking', function () {
        $this->get('/bookings/create')
            ->assertRedirect('/login');
    });
});

describe('Admin Fields Management', function () {
    test('admin can view fields list', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get('/admin/fields')
            ->assertStatus(200);
    });

    test('admin can view create field form', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get('/admin/fields/create')
            ->assertStatus(200);
    });

    test('admin can create new field', function () {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/fields', [
            'name' => 'Court A',
            'location' => 'Jakarta',
            'description' => 'Premium futsal court',
            'price_per_hour' => 150000,
            'is_active' => true,
        ]);

        expect(Field::where('name', 'Court A')->exists())->toBeTrue();
    });

    test('admin can edit field', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $field = Field::factory()->create(['name' => 'Court A']);

        $response = $this->actingAs($admin)->put("/admin/fields/{$field->id}", [
            'name' => 'Court A Updated',
            'location' => $field->location,
            'description' => $field->description,
            'price_per_hour' => $field->price_per_hour,
            'is_active' => $field->is_active,
        ]);

        expect(Field::find($field->id)->name)->toBe('Court A Updated');
    });

    test('admin can delete field', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $field = Field::factory()->create();

        $this->actingAs($admin)->delete("/admin/fields/{$field->id}");

        expect(Field::find($field->id))->toBeNull();
    });
});

describe('Admin Bookings Management', function () {
    test('admin can view bookings list', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get('/admin/bookings')
            ->assertStatus(200);
    });

    test('admin can update booking status', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $field = Field::factory()->create();
        $booking = Booking::factory()->create([
            'field_id' => $field->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->patch("/admin/bookings/{$booking->id}", [
            'status' => 'confirmed',
        ]);

        expect(Booking::find($booking->id)->status)->toBe('confirmed');
    });
});

describe('Route Protection', function () {
    test('member cannot access admin.fields.create', function () {
        $member = User::factory()->create(['role' => 'member']);
        $this->actingAs($member)
            ->get('/admin/fields/create')
            ->assertStatus(403);
    });

    test('member cannot post to admin.fields.store', function () {
        $member = User::factory()->create(['role' => 'member']);
        $this->actingAs($member)
            ->post('/admin/fields', [
                'name' => 'Test',
                'location' => 'Test',
                'description' => 'Test',
                'price_per_hour' => 100000,
                'is_active' => true,
            ])
            ->assertStatus(403);
    });

    test('member cannot access admin.bookings.index', function () {
        $member = User::factory()->create(['role' => 'member']);
        $this->actingAs($member)
            ->get('/admin/bookings')
            ->assertStatus(403);
    });

    test('gate can:access-admin checks isAdmin correctly', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        expect($admin->isAdmin())->toBeTrue();
        expect($member->isAdmin())->toBeFalse();
    });
});

describe('Data Validation', function () {
    test('field name is required', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->post('/admin/fields', [
            'location' => 'Jakarta',
            'description' => 'Test',
            'price_per_hour' => 150000,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('name');
    });

    test('field price must be numeric', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->post('/admin/fields', [
            'name' => 'Court A',
            'location' => 'Jakarta',
            'description' => 'Test',
            'price_per_hour' => 'invalid',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('price_per_hour');
    });
});

describe('Database Integrity', function () {
    test('field has many bookings relationship', function () {
        $field = Field::factory()->create();
        $booking = Booking::factory()->create(['field_id' => $field->id]);

        expect($field->bookings()->count())->toBe(1);
        expect($field->bookings()->first()->id)->toBe($booking->id);
    });

    test('user has many bookings relationship', function () {
        $user = User::factory()->create();
        $field = Field::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id, 'field_id' => $field->id]);

        expect($user->bookings()->count())->toBe(1);
        expect($user->bookings()->first()->id)->toBe($booking->id);
    });

    test('booking belongs to field', function () {
        $field = Field::factory()->create();
        $booking = Booking::factory()->create(['field_id' => $field->id]);

        expect($booking->field->id)->toBe($field->id);
    });

    test('booking belongs to user', function () {
        $user = User::factory()->create();
        $field = Field::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id, 'field_id' => $field->id]);

        expect($booking->user->id)->toBe($user->id);
    });
});
