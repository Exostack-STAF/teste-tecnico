<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $policy;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new OrderPolicy();
        $this->user = User::factory()->create();
    }

    public function it_allows_pending_to_paid_transition()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $this->assertTrue($this->policy->transitionStatus($this->user, $order, 'paid'));
    }

    public function it_allows_paid_to_shipped_transition()
    {
        $order = Order::factory()->create(['status' => 'paid']);
        $this->assertTrue($this->policy->transitionStatus($this->user, $order, 'shipped'));
    }

    public function it_denies_shipped_to_any_transition()
    {
        $order = Order::factory()->create(['status' => 'shipped']);
        $this->assertFalse($this->policy->transitionStatus($this->user, $order, 'paid'));
        $this->assertFalse($this->policy->transitionStatus($this->user, $order, 'pending'));
    }

    public function it_denies_pending_to_shipped_transition()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $this->assertFalse($this->policy->transitionStatus($this->user, $order, 'shipped'));
    }

    public function it_denies_invalid_transitions()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $this->assertFalse($this->policy->transitionStatus($this->user, $order, 'invalid_status'));
    }
    
    public function it_allows_same_status_transition()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $this->assertTrue($this->policy->transitionStatus($this->user, $order, 'pending'));
        
        $order->status = 'paid';
        $order->save();
        $this->assertTrue($this->policy->transitionStatus($this->user, $order, 'paid'));
        
        $order->status = 'shipped';
        $order->save();
        $this->assertTrue($this->policy->transitionStatus($this->user, $order, 'shipped'));
    }
    
    public function it_allows_view_any_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->viewAny($this->user));
    }
    
    public function it_allows_view_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->view($this->user, $order));
    }
    
    public function it_allows_create_order()
    {
        $this->assertTrue($this->policy->create($this->user));
    }
    
    public function it_allows_update_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->update($this->user, $order));
    }
    
    public function it_allows_delete_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->delete($this->user, $order));
    }
}