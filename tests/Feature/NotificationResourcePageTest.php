<?php

namespace Tests\Feature;

use App\Filament\Resources\NotificationResource\Pages\ListNotifications;
use App\Filament\Resources\NotificationResource\Pages\ViewNotification;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationResourcePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_notifications_page_can_be_instantiated()
    {
        $page = new ListNotifications();
        $this->assertInstanceOf(ListNotifications::class, $page);
    }

    public function test_view_notification_page_can_be_instantiated()
    {
        $page = new ViewNotification();
        $this->assertInstanceOf(ViewNotification::class, $page);
    }
}