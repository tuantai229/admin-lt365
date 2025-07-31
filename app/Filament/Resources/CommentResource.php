<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use App\Models\User;
use App\Models\Document;
use App\Models\News;
use App\Models\School;
use App\Models\Center;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Helpers\FormatHelper;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\DocumentResource;
use App\Filament\Resources\NewsResource;
use App\Filament\Resources\SchoolResource;
use App\Filament\Resources\CenterResource;
use App\Filament\Resources\TeacherResource;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Tương tác';
    protected static ?string $navigationLabel = 'Bình luận';
    protected static ?string $modelLabel = 'Bình luận';
    protected static ?string $pluralModelLabel = 'Bình luận';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Người dùng')
                ->relationship('user', 'full_name')
                ->searchable()
                ->required(),
            
            Forms\Components\Select::make('type')
                ->label('Loại đối tượng')
                ->options([
                    'document' => 'Tài liệu',
                    'news' => 'Tin tức',
                    'school' => 'Trường học',
                    'center' => 'Trung tâm',
                    'teacher' => 'Giáo viên',
                ])
                ->required()
                ->reactive()
                ->afterStateUpdated(fn (callable $set) => $set('type_id', null)),
            
            Forms\Components\Select::make('type_id')
                ->label('Đối tượng')
                ->options(function (callable $get) {
                    $type = $get('type');
                    return match ($type) {
                        'document' => Document::pluck('name', 'id'),
                        'news' => News::pluck('title', 'id'),
                        'school' => School::pluck('name', 'id'),
                        'center' => Center::pluck('name', 'id'),
                        'teacher' => Teacher::pluck('name', 'id'),
                        default => [],
                    };
                })
                ->searchable()
                ->required(),
            
            Forms\Components\Select::make('parent_id')
                ->label('Bình luận cha')
                ->relationship('parent', 'content')
                ->searchable()
                ->placeholder('Chọn bình luận cha (nếu là reply)')
                ->helperText('Để trống nếu đây là bình luận gốc'),
            
            Forms\Components\Textarea::make('content')
                ->label('Nội dung')
                ->required()
                ->maxLength(65535)
                ->rows(4),
            
            Forms\Components\Select::make('status')
                ->label('Trạng thái')
                ->options([
                    0 => 'Chờ duyệt',
                    1 => 'Đã duyệt',
                    2 => 'Đã ẩn',
                ])
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user']))
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Người dùng')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->user]))
                    ->color('primary')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại đối tượng')
                    ->formatStateUsing(function ($record): string {
                        $typeName = match ($record->type) {
                            'document' => 'Tài liệu',
                            'news' => 'Tin tức',
                            'school' => 'Trường học',
                            'center' => 'Trung tâm',
                            'teacher' => 'Giáo viên',
                            default => $record->type,
                        };
                        
                        $objectName = 'N/A';
                        try {
                            $objectName = match ($record->type) {
                                'document' => $record->type === 'document' ? Document::find($record->type_id)?->name ?? 'N/A' : 'N/A',
                                'news' => $record->type === 'news' ? News::find($record->type_id)?->title ?? 'N/A' : 'N/A',
                                'school' => $record->type === 'school' ? School::find($record->type_id)?->name ?? 'N/A' : 'N/A',
                                'center' => $record->type === 'center' ? Center::find($record->type_id)?->name ?? 'N/A' : 'N/A',
                                'teacher' => $record->type === 'teacher' ? Teacher::find($record->type_id)?->name ?? 'N/A' : 'N/A',
                                default => 'N/A',
                            };
                        } catch (\Exception $e) {
                            $objectName = 'N/A';
                        }
                        
                        return $typeName . ': ' . $objectName;
                    })
                    ->url(function ($record) {
                        try {
                            return match ($record->type) {
                                'document' => $record->type === 'document' && $record->type_id ? DocumentResource::getUrl('view', ['record' => $record->type_id]) : null,
                                'news' => $record->type === 'news' && $record->type_id ? NewsResource::getUrl('view', ['record' => $record->type_id]) : null,
                                'school' => $record->type === 'school' && $record->type_id ? SchoolResource::getUrl('view', ['record' => $record->type_id]) : null,
                                'center' => $record->type === 'center' && $record->type_id ? CenterResource::getUrl('view', ['record' => $record->type_id]) : null,
                                'teacher' => $record->type === 'teacher' && $record->type_id ? TeacherResource::getUrl('view', ['record' => $record->type_id]) : null,
                                default => null,
                            };
                        } catch (\Exception $e) {
                            return null;
                        }
                    })
                    ->color('primary')
                    ->weight('medium')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('content')
                    ->label('Nội dung')
                    ->limit(100)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('parent_id')
                    ->label('Loại')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? 'Trả lời' : 'Bình luận gốc')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'info' : 'success'),
                
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Trạng thái')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->tooltip(fn ($record) => $record->status ? 'Đã duyệt' : 'Chờ duyệt')
                    ->updateStateUsing(function ($record, $state) {
                        $record->update(['status' => $state ? 1 : 0]);
                        return $state;
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->formatStateUsing(fn ($state) => FormatHelper::datetime($state))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại đối tượng')
                    ->options([
                        'document' => 'Tài liệu',
                        'news' => 'Tin tức',
                        'school' => 'Trường học',
                        'center' => 'Trung tâm',
                        'teacher' => 'Giáo viên',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        0 => 'Chờ duyệt',
                        1 => 'Đã duyệt',
                        2 => 'Đã ẩn',
                    ]),
                
                Tables\Filters\Filter::make('parent_comments')
                    ->label('Bình luận gốc')
                    ->query(fn (Builder $query): Builder => $query->where('parent_id', 0)),
                
                Tables\Filters\Filter::make('reply_comments')
                    ->label('Bình luận trả lời')
                    ->query(fn (Builder $query): Builder => $query->where('parent_id', '>', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
