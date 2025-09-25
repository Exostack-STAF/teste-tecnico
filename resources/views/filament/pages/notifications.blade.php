<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold tracking-tight">
                Notifications
            </h2>
        </div>

        <div class="grid gap-6">
            @forelse (\App\Models\Notification::latest()->get() as $notification)
                <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-900 dark:text-white">
                                {{ $notification->message }}
                            </p>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Order: {{ $notification->order->customer_name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400 text-center">
                        No notifications found.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>