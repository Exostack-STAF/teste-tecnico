<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Order $order): bool
    {
        return true;
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return true;
    }

    public function transitionStatus(User $user, Order $order, string $newStatus): bool
    {
        if ($order->status === $newStatus) {
            return true;
        }

        $allowedTransitions = [
            'pending' => ['paid'],
            'paid' => ['shipped'],
            'shipped' => []
        ];

        if ($newStatus === 'shipped') {
            return $order->status === 'paid';
        }

        return in_array($newStatus, $allowedTransitions[$order->status] ?? []);
    }

    public function delete(User $user, Order $order): bool
    {
        return true;
    }

    public function restore(User $user, Order $order): bool
    {
        return true;
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return true;
    }
}