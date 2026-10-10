<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_profile_page()
    {
        $admin = User::factory()->create([
            'name' => 'Worthy Acosta (Admin)',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($admin)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Worthy Acosta');
        $response->assertSee('Account Profile');
    }

    public function test_user_can_update_profile_info_and_avatar()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@acostainventory.com',
            'contact_number' => '09123456789',
            'role' => 'staff',
            'password' => Hash::make('oldpassword'),
        ]);

        $avatarFile = UploadedFile::fake()->create('test_avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'John Acosta Doe',
            'email' => 'johndoe_new@acostainventory.com',
            'contact_number' => '09988776655',
            'avatar' => $avatarFile,
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('John Acosta Doe', $user->name);
        $this->assertEquals('johndoe_new@acostainventory.com', $user->email);
        $this->assertEquals('09988776655', $user->contact_number);
        $this->assertNotNull($user->avatar);
        $this->assertFileExists(public_path($user->avatar));

        // Clean up uploaded file
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            @unlink(public_path($user->avatar));
        }
    }

    public function test_user_can_change_password_with_valid_current_password()
    {
        $user = User::factory()->create([
            'name' => 'Staff Member',
            'role' => 'staff',
            'password' => Hash::make('currentpassword123'),
        ]);

        $response = $this->actingAs($user)->post('/profile', [
            'name' => 'Staff Member',
            'current_password' => 'currentpassword123',
            'new_password' => 'brandnewpassword456',
            'new_password_confirmation' => 'brandnewpassword456',
        ]);

        $response->assertRedirect('/profile');
        $user->refresh();
        $this->assertTrue(Hash::check('brandnewpassword456', $user->password));
    }
}
