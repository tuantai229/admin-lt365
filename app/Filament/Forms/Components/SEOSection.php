<?php
namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class SEOSection
{
    public static function make(): Section
    {
        return Section::make('Meta SEO')
            ->description('Tùy chỉnh SEO. Để trống sẽ dùng template tự động.')
            ->schema([
                TextInput::make('metaSeo.meta_title')
                    ->label('Meta Title')
                    ->maxLength(255)
                    ->helperText('Tối đa 60-65 ký tự'),

                Textarea::make('metaSeo.meta_description')
                    ->label('Meta Description')
                    ->rows(3)
                    ->maxLength(320)
                    ->helperText('Tối đa 150-160 ký tự'),

                TextInput::make('metaSeo.meta_keywords')
                    ->label('Meta Keywords')
                    ->helperText('Các từ khóa cách nhau bằng dấu phẩy'),

                Select::make('metaSeo.meta_robots')
                    ->label('Meta Robots')
                    ->options([
                        'index,follow' => 'Index, Follow (Mặc định)',
                        'noindex,follow' => 'No Index, Follow',
                        'index,nofollow' => 'Index, No Follow',
                        'noindex,nofollow' => 'No Index, No Follow',
                    ])
                    ->default('index,follow'),
            ])
            ->collapsible()
            ->collapsed()
            ->relationship('metaSeo');
    }
}
