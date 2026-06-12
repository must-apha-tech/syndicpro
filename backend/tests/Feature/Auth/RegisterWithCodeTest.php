<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Residence;
use App\Models\Lot;
use App\Models\Tenant;
use App\Models\ResidenceAccessCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class RegisterWithCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup initial data
        $this->tenant = Tenant::create(['name' => 'Syndic Tenant']);
        $this->syndic = User::factory()->create(['tenant_id' => $this->tenant->id]);
        
        // Ensure roles exist
        if (!Role::where('name', 'syndic')->exists()) {
            Role::create(['name' => 'syndic']);
        }
        if (!Role::where('name', 'proprietaire')->exists()) {
            Role::create(['name' => 'proprietaire']);
        }
        
        $this->syndic->assignRole('syndic');

        $this->residence = Residence::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Belle Vue',
            'address' => '123 Main St',
            'city' => 'Casablanca',
            'syndic_id' => $this->syndic->id,
        ]);

        $this->lot = Lot::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'numero' => '42',
            'type' => 'Appartement',
            'surface' => 85,
            'quote_part' => 120,
        ]);

        $this->accessCode = ResidenceAccessCode::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'lot_id' => $this->lot->id,
            'code' => 'XYZ789',
            'created_by' => $this->syndic->id,
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function test_validate_code_returns_correct_data()
    {
        $response = $this->postJson('/api/auth/validate-code', [
            'code' => 'XYZ789',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'residence' => ['name' => 'Belle Vue'],
                'lot' => ['numero' => '42'],
            ]);
    }

    public function test_register_with_code_successfully()
    {
        $response = $this->postJson('/api/auth/register-with-code', [
            'code' => 'XYZ789',
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);
        
        $user = User::where('email', 'jean@example.com')->first();
        $this->assertTrue($user->hasRole('proprietaire'));
        $this->assertEquals($this->tenant->id, $user->tenant_id);

        // Verify lot association
        $this->lot->refresh();
        $this->assertEquals($user->id, $this->lot->proprietaire_id);

        // Verify code marked as used
        $this->accessCode->refresh();
        $this->assertTrue($this->accessCode->isUsed());
        $this->assertEquals($user->id, $this->accessCode->used_by);
    }

    public function test_register_fails_with_invalid_code()
    {
        $response = $this->postJson('/api/auth/register-with-code', [
            'code' => 'INVALID',
            'name' => 'Fail User',
            'email' => 'fail@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(400);
    }
}
