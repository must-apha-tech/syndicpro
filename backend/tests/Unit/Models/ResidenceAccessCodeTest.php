<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ResidenceAccessCode;
use App\Models\User;
use App\Models\Residence;
use App\Models\Lot;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ResidenceAccessCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenant = Tenant::create(['name' => 'Test Tenant']);
        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->residence = Residence::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Residence Test',
            'syndic_id' => $this->user->id,
        ]);
        $this->lot = Lot::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'numero' => '101',
        ]);
    }

    public function test_is_valid_returns_true_for_unused_non_expired_codes()
    {
        $code = ResidenceAccessCode::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'lot_id' => $this->lot->id,
            'code' => 'ABC123',
            'created_by' => $this->user->id,
            'expires_at' => now()->addDays(30),
        ]);

        $this->assertTrue($code->isValid());
    }

    public function test_is_expired_works_correctly()
    {
        $codeNoExpiry = new ResidenceAccessCode(['expires_at' => null]);
        $this->assertFalse($codeNoExpiry->isExpired());

        $codeFuture = new ResidenceAccessCode(['expires_at' => now()->addDay()]);
        $this->assertFalse($codeFuture->isExpired());

        $codePast = new ResidenceAccessCode(['expires_at' => now()->subDay()]);
        $this->assertTrue($codePast->isExpired());
    }

    public function test_is_used_works_correctly()
    {
        $code = new ResidenceAccessCode(['used_by' => null]);
        $this->assertFalse($code->isUsed());

        $code->used_by = 1;
        $this->assertTrue($code->isUsed());
    }

    public function test_mark_as_used_sets_user_and_timestamp()
    {
        $code = ResidenceAccessCode::create([
            'tenant_id' => $this->tenant->id,
            'residence_id' => $this->residence->id,
            'lot_id' => $this->lot->id,
            'code' => 'ABC123',
            'created_by' => $this->user->id,
        ]);

        $newUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        
        $code->markAsUsed($newUser);

        $this->assertEquals($newUser->id, $code->used_by);
        $this->assertNotNull($code->used_at);
        $this->assertTrue($code->isUsed());
    }
}
