<?php

namespace Tests\Feature\Residences;

use Tests\TestCase;
use App\Models\User;
use App\Models\Residence;
use App\Models\Lot;
use App\Models\Tenant;
use App\Models\ResidenceAccessCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

class AccessCodeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenant = Tenant::create(['name' => 'Syndic Corp']);
        $this->syndic = User::factory()->create(['tenant_id' => $this->tenant->id]);
        
        if (!Role::where('name', 'syndic')->exists()) {
            Role::create(['name' => 'syndic']);
        }
        $this->syndic->assignRole('syndic');

        $this->residence = Residence::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Atlantic Tower',
            'syndic_id' => $this->syndic->id,
        ]);

        $this->lot = Lot::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'numero' => 'B7',
        ]);
    }

    public function test_syndic_can_generate_access_code()
    {
        Sanctum::actingAs($this->syndic);

        $response = $this->postJson("/api/residences/{$this->residence->id}/lots/{$this->lot->id}/access-code");

        $response->assertStatus(201)
            ->assertJsonStructure(['code', 'expires_at']);
        
        $this->assertDatabaseHas('residence_access_codes', [
            'residence_id' => $this->residence->id,
            'lot_id' => $this->lot->id,
        ]);
    }

    public function test_syndic_can_list_access_codes()
    {
        Sanctum::actingAs($this->syndic);

        ResidenceAccessCode::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'lot_id' => $this->lot->id,
            'code' => 'TEST12',
            'created_by' => $this->syndic->id,
        ]);

        $response = $this->getJson("/api/residences/{$this->residence->id}/access-codes");

        $response->assertStatus(200)
            ->assertJsonFragment(['code' => 'TEST12']);
    }

    public function test_syndic_can_revoke_unused_code()
    {
        Sanctum::actingAs($this->syndic);

        $code = ResidenceAccessCode::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'lot_id' => $this->lot->id,
            'code' => 'REVOKE',
            'created_by' => $this->syndic->id,
        ]);

        $response = $this->deleteJson("/api/access-codes/{$code->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('residence_access_codes', ['id' => $code->id]);
    }

    public function test_syndic_cannot_revoke_used_code()
    {
        Sanctum::actingAs($this->syndic);

        $user = User::factory()->create();
        $code = ResidenceAccessCode::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'lot_id' => $this->lot->id,
            'code' => 'USED12',
            'created_by' => $this->syndic->id,
            'used_by' => $user->id,
            'used_at' => now(),
        ]);

        $response = $this->deleteJson("/api/access-codes/{$code->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('residence_access_codes', ['id' => $code->id]);
    }
}
