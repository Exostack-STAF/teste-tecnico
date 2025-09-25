<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;



class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Order Details')
                    ->schema([
                        TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255),

                        Select::make('status')
                            ->options(function (?Order $record) {
                                if (!$record || !$record->exists) {
                                    return [
                                        'pending' => 'Pending',
                                    ];
                                }
                                
                                $options = [
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'shipped' => 'Shipped',
                                ];
                                
                                $validTransitions = [
                                    'pending' => ['pending', 'paid'],
                                    'paid' => ['paid', 'shipped'],
                                    'shipped' => ['shipped'],
                                ];
                                
                                $allowedOptions = [];
                                $currentStatus = $record->status;
                                
                                foreach ($validTransitions[$currentStatus] ?? [] as $status) {
                                    $allowedOptions[$status] = $options[$status];
                                }
                                
                                return $allowedOptions;
                            })
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Order Items')
                    ->schema([
                        Repeater::make('order_items')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::all()->pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('price', $product->price);
                                            }
                                        }
                                    }),

                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->integer()
                                    ->default(1)
                                    ->helperText(function (callable $get) {
                                        $productId = $get('product_id');
                                        if ($productId) {
                                            $product = Product::find($productId);
                                            if ($product) {
                                                return "Available stock: {$product->stock_quantity}";
                                            }
                                        }
                                        return '';
                                    }),

                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->prefix('R$'),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add Item'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')->searchable()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'shipped' => 'info',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [ OrderItemsRelationManager::class,];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}