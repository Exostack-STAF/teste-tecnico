<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPolicyComprehensiveTest extends TestCase
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

    public function test_pending_to_paid_transition_is_allowed()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $this->assertTrue($this->policy->transitionStatus($this->user, $order, 'paid'));
    }

    public function test_paid_to_shipped_transition_is_allowed()
    {
        $order = Order::factory()->create(['status' => 'paid']);
        $this->assertTrue($this->policy->transitionStatus($this->user, $order, 'shipped'));
    }

    public function test_shipped_to_any_transition_is_denied()
    {
        $order = Order::factory()->create(['status' => 'shipped']);
        $this->assertFalse($this->policy->transitionStatus($this->user, $order, 'paid'));
        $this->assertFalse($this->policy->transitionStatus($this->user, $order, 'pending'));
    }

    public function test_pending_to_shipped_transition_is_denied()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $this->assertFalse($this->policy->transitionStatus($this->user, $order, 'shipped'));
    }

    public function test_invalid_transitions_are_denied()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $this->assertFalse($this->policy->transitionStatus($this->user, $order, 'invalid_status'));
    }
    
    public function test_same_status_transition_is_allowed()
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
    
    public function test_user_can_view_any_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->viewAny($this->user));
    }
    
    public function test_user_can_view_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->view($this->user, $order));
    }
    
    public function test_user_can_create_order()
    {
        $this->assertTrue($this->policy->create($this->user));
    }
    
    public function test_user_can_update_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->update($this->user, $order));
    }
    
    public function test_user_can_delete_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->delete($this->user, $order));
    }

    public function test_user_can_update_status()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->updateStatus($this->user, $order));
    }

    public function test_user_can_restore_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->restore($this->user, $order));
    }

    public function test_user_can_force_delete_order()
    {
        $order = Order::factory()->create();
        $this->assertTrue($this->policy->forceDelete($this->user, $order));
    }
}