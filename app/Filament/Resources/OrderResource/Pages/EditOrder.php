<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Events\OrderStatusChanged;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    /** @var array<string> */
    private static array $dispatchedEvents = [];

    /**
     * 
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * 
     *
     * @throws Throwable
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            try {
                $oldStatus = $record->status;

                // Atualiza pedido via Eloquent para consistência
                $record->update([
                    'customer_name' => $data['customer_name'],
                    'status'        => $data['status'],
                ]);

                $newStatus = $record->status;

                $this->maybeDispatchStatusChange($record, $oldStatus, $newStatus);

                Log::info('EditOrder@handleRecordUpdate: Order updated', [
                    'order_id'   => $record->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);

                return $record;
            } catch (Throwable $e) {
                Log::error('EditOrder@handleRecordUpdate: Error updating order', [
                    'order_id' => $record->id ?? null,
                    'error'    => $e->getMessage(),
                    'trace'    => $e->getTraceAsString(),
                ]);

                Notification::make()
                    ->title('Error')
                    ->body("An error occurred while updating the order: {$e->getMessage()}")
                    ->danger()
                    ->send();

                throw $e;
            }
        });
    }

    /**
     * 
     */
    private function maybeDispatchStatusChange(Model $order, string $oldStatus, string $newStatus): void
    {
        if ($oldStatus === $newStatus) {
            return;
        }

       
        if ($oldStatus === 'pending' && $newStatus === 'paid') {
            $eventKey = "{$order->id}_{$oldStatus}_{$newStatus}";

            if (!in_array($eventKey, self::$dispatchedEvents, true)) {
                self::$dispatchedEvents[] = $eventKey;

                OrderStatusChanged::dispatch($order, $oldStatus, $newStatus);

                Log::info('EditOrder@maybeDispatchStatusChange: Event dispatched', [
                    'order_id'   => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
            }
        }
    }

    public function __destruct()
    {
       
        self::$dispatchedEvents = [];
    }
}
