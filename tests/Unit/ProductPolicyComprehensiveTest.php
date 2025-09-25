<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Tests\TestCase;

class ProductPolicyComprehensiveTest extends TestCase
{
    public function test_user_can_view_any_product()
    {
        $user = new User();
        $policy = new ProductPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    public function test_user_can_view_product()
    {
        $user = new User();
        $product = new Product();
        $policy = new ProductPolicy();

        $this->assertTrue($policy->view($user, $product));
    }

    public function test_user_can_create_product()
    {
        $user = new User();
        $policy = new ProductPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_user_can_update_product()
    {
        $user = new User();
        $product = new Product();
        $policy = new ProductPolicy();

        $this->assertTrue($policy->update($user, $product));
    }

    public function test_user_can_delete_product()
    {
        $user = new User();
        $product = new Product();
        $policy = new ProductPolicy();

        $this->assertTrue($policy->delete($user, $product));
    }

    public function test_user_can_restore_product()
    {
        $user = new User();
        $product = new Product();
        $policy = new ProductPolicy();

        $this->assertTrue($policy->restore($user, $product));
    }

    public function test_user_can_force_delete_product()
    {
        $user = new User();
        $product = new Product();
        $policy = new ProductPolicy();

        $this->assertTrue($policy->forceDelete($user, $product));
    }
}