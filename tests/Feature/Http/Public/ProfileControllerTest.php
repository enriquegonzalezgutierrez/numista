<?php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function profile_page_is_displayed(): void
    {
        $this->actingAs($this->user)
            ->get(route('my-account.profile.edit'))
            ->assertStatus(200);
    }

    #[Test]
    public function user_can_update_their_profile_information(): void
    {
        $this->actingAs($this->user)
            ->patch(route('my-account.profile.update'), [
                'name' => 'Test User Updated',
                'email' => 'test.updated@example.com',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('my-account.profile.edit'));

        $this->user->refresh();

        $this->assertSame('Test User Updated', $this->user->name);
        $this->assertSame('test.updated@example.com', $this->user->email);
        $this->assertNull($this->user->email_verified_at);
    }

    #[Test]
    public function user_must_provide_a_valid_email_to_update(): void
    {
        $otherUser = User::factory()->create(['email' => 'other@example.com']);

        $this->actingAs($this->user)
            ->patch(route('my-account.profile.update'), [
                'name' => 'Test User',
                'email' => 'other@example.com', // Already taken
            ])
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function user_can_update_their_password(): void
    {
        $this->actingAs($this->user)
            ->put(route('my-account.password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertTrue(Hash::check('new-password', $this->user->refresh()->password));
    }

    #[Test]
    public function user_cannot_update_password_with_incorrect_current_password(): void
    {
        $this->actingAs($this->user)
            ->put(route('my-account.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasErrors('current_password');
    }
}
