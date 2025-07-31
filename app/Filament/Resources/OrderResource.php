<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\UserResource;
use App\Helpers\FormatHelper;
use App\Models\Document;
use App\Models\Order;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Bán hàng';

    protected static ?string $modelLabel = 'Đơn hàng';

    protected static ?int $navigationSort = 1;

    // Kiểm tra permission
    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_orders');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_orders');
    }

    public static function canCreate(): bool
    {
        return auth('admin')->user()->can('create_orders');
    }

    public static function canEdit($record): bool
    {
        return auth('admin')->user()->can('update_orders');
    }

    public static function canDelete($record): bool
    {
        return auth('admin')->user()->can('delete_orders');
    }

    public static function canView($record): bool
    {
        return auth('admin')->user()->can('view_orders');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Thông tin đơn hàng')->schema([
                    Select::make('user_id')
                        ->label('Khách hàng')
                        ->relationship('user', 'full_name')
                        ->searchable()
                        ->required(),
                    Select::make('status')
                        ->label('Trạng thái')
                        ->options([
                            'pending' => 'Chờ xử lý',
                            'paid' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                        ])
                        ->default('pending')
                        ->required(),
                ])->columns(2),
                Wizard\Step::make('Chi tiết đơn hàng')->schema([
                    static::getItemsRepeater(),
                ]),
            ])->columnSpanFull(),

            Section::make('Thanh toán')
                ->schema([
                    TextInput::make('total_amount')
                        ->label('Tổng tiền')
                        ->numeric()
                        ->readOnly()
                        ->dehydrated()
                        ->prefix('VND'),
                    Select::make('payment_method')
                        ->label('Phương thức thanh toán')
                        ->options([
                            'cod' => 'Thanh toán khi nhận hàng (COD)',
                            'bank_transfer' => 'Chuyển khoản ngân hàng',
                            'vnpay' => 'VNPAY',
                        ])
                        ->default('cod'),
                    Select::make('payment_status')
                        ->label('Trạng thái thanh toán')
                        ->options([
                            'pending' => 'Chờ thanh toán',
                            'paid' => 'Đã thanh toán',
                            'failed' => 'Thanh toán thất bại',
                        ])
                        ->default('pending')
                        ->required(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.full_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Order $record): string => UserResource::getUrl('view', ['record' => $record->user_id])),
                TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ xử lý',
                        'paid' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    }),
                TextColumn::make('payment_method')
                    ->label('PTTT')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cod' => 'COD',
                        'bank_transfer' => 'Chuyển khoản',
                        'vnpay' => 'VNPAY',
                        default => $state,
                    })
                    ->searchable(),
                BadgeColumn::make('payment_status')
                    ->label('TT Thanh toán')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ thanh toán',
                        'paid' => 'Đã thanh toán',
                        'failed' => 'Thất bại',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'paid' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Trạng thái thanh toán')
                    ->options([
                        'pending' => 'Chờ thanh toán',
                        'paid' => 'Đã thanh toán',
                        'failed' => 'Thanh toán thất bại',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('update_status_to_paid')
                        ->label('Chuyển sang "Hoàn thành"')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'paid']))
                        ->icon('heroicon-o-check-circle'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfolistSection::make('Thông tin đơn hàng')
                ->schema([
                    TextEntry::make('id')->label('ID'),
                    TextEntry::make('user.full_name')
                        ->label('Khách hàng')
                        ->url(fn (Order $record): string => UserResource::getUrl('view', ['record' => $record->user_id]))
                        ->openUrlInNewTab(),
                    TextEntry::make('created_at')->label('Ngày tạo')->dateTime(),
                    TextEntry::make('status')
                        ->label('Trạng thái')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'processing' => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pending' => 'Chờ xử lý',
                            'paid' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                            default => $state,
                        }),
                ])->columns(2),

            InfolistSection::make('Thông tin thanh toán')
                ->schema([
                    TextEntry::make('total_amount')->label('Tổng tiền')->money('VND'),
                    TextEntry::make('payment_method')->label('Phương thức thanh toán')
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'cod' => 'Thanh toán khi nhận hàng (COD)',
                            'bank_transfer' => 'Chuyển khoản ngân hàng',
                            'vnpay' => 'VNPAY',
                            default => $state,
                        }),
                    TextEntry::make('payment_status')
                        ->label('Trạng thái thanh toán')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'paid' => 'success',
                            'failed' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pending' => 'Chờ thanh toán',
                            'paid' => 'Đã thanh toán',
                            'failed' => 'Thất bại',
                            default => $state,
                        }),
                ])->columns(2),

            InfolistSection::make('Chi tiết đơn hàng')
                ->schema([
                    \Filament\Infolists\Components\RepeatableEntry::make('orderItems')
                        ->label('')
                        ->schema([
                            TextEntry::make('document.title')->label('Sản phẩm'),
                            TextEntry::make('price')->label('Đơn giá')->money('VND'),
                        ])
                        ->columns(2)
                        ->grid(2),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('orderItems')
            ->label('Sản phẩm')
            ->relationship()
            ->schema([
                Select::make('document_id')
                    ->label('Sản phẩm')
                    ->relationship('document', 'title')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $document = Document::find($state);
                        if ($document) {
                            $set('price', $document->price ?? 0);
                        }
                    })
                    ->columnSpan([
                        'md' => 7,
                    ]),
                TextInput::make('price')
                    ->label('Đơn giá')
                    ->numeric()
                    ->required()
                    ->dehydrated()
                    ->columnSpan([
                        'md' => 3,
                    ]),
            ])
            ->live()
            ->afterStateUpdated(function (Get $get, Set $set) {
                self::updateTotals($get, $set);
            })
            ->deleteAction(
                fn (Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
            )
            ->columns([
                'md' => 10,
            ])
            ->columnSpanFull();
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $items = $get('orderItems');
        $total = 0;

        if (is_array($items)) {
            foreach ($items as $item) {
                $total += $item['price'] ?? 0;
            }
        }

        $set('total_amount', $total);
    }
}
