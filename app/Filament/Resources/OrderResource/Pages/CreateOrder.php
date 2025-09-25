<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * 
     *
     * @param  array  $data
     * @return Model
     *
     * @throws Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            try {
                $order = Order::create([
                    'customer_name' => $data['customer_name'],
                    'status'        => $data['status'] ?? Order::STATUS_PENDING,
                ]);

                $orderItemsData = $data['order_items'] ?? [];

                foreach ($orderItemsData as $itemData) {
                    $this->createOrderItem($order, $itemData);
                }

                $this->updateProductStock($order);

                Notification::make()
                    ->title('Order Created')
                    ->body("The order #{$order->id} has been created successfully.")
                    ->success()
                    ->send();

                return $order;
            } catch (Throwable $e) {
                Notification::make()
                    ->title('Error')
                    ->body("Failed to create order: {$e->getMessage()}")
                    ->danger()
                    ->send();

                throw $e;
            }
        });
    }

    /**
     * 
     */
    private function createOrderItem(Order $order, array $itemData): void
    {
        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $itemData['product_id'],
            'quantity'   => $itemData['quantity'],
            'price'      => $itemData['price'],
        ]);
    }

    /**
     * 
     *
     * @throws \Exception
     */
    private function updateProductStock(Order $order): void
    {
        foreach ($order->orderItems as $item) {
            /** @var Product|null $product */
            $product = Product::lockForUpdate()->find($item->product_id);

            if (!$product) {
                throw new \Exception("Product ID {$item->product_id} not found.");
            }

            if ($product->stock_quantity < $item->quantity) {
                throw new \Exception(
                    "Not enough stock for product '{$product->name}'. " .
                    "Available: {$product->stock_quantity}, Requested: {$item->quantity}"
                );
            }

            $product->decrement('stock_quantity', $item->quantity);
        }
    }
}
