<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use App\Policies\NotificationPolicy;
use Tests\TestCase;

class NotificationPolicyTest extends TestCase
{
    public function test_user_can_view_any_notification()
    {
        $user = new User();
        $policy = new NotificationPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    public function test_user_can_view_notification()
    {
        $user = new User();
        $notification = new Notification();
        $policy = new NotificationPolicy();

        $this->assertTrue($policy->view($user, $notification));
    }

    public function test_user_cannot_create_notification()
    {
        $user = new User();
        $policy = new NotificationPolicy();

        $this->assertFalse($policy->create($user));
    }

    public function test_user_cannot_update_notification()
    {
        $user = new User();
        $notification = new Notification();
        $policy = new NotificationPolicy();

        $this->assertFalse($policy->update($user, $notification));
    }

    public function test_user_cannot_delete_notification()
    {
        $user = new User();
        $notification = new Notification();
        $policy = new NotificationPolicy();

        $this->assertFalse($policy->delete($user, $notification));
    }
}