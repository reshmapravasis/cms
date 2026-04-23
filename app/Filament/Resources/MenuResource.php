<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationGroup = 'Site Management';
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('location')
                    ->options([
                        'header' => 'Header Navigation',
                        'footer' => 'Footer Quick Links',
                    ])
                    ->required()
                    ->default('header')
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        $set('order_column', (Menu::where('location', $state)->max('order_column') ?? 0) + 1);
                    }),
                Forms\Components\Select::make('page_id')
                    ->label('Select Page')
                    ->options(function (Forms\Get $get) {
                        $location = $get('location') ?? 'header';
                        $pages = Page::all();
                        $options = [];
                        foreach ($pages as $page) {
                            $url = ($page->slug === 'home') ? '/' : '/' . $page->slug;
                            $menu = Menu::where('location', $location)->where('url', $url)->first();
                            $label = $page->title;
                            if ($menu) {
                                $label .= " (Current Position: {$menu->order_column})";
                            }
                            $options[$page->id] = $label;
                        }
                        return $options;
                    })
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateHydrated(function (Forms\Set $set, $state, $record) {
                        if ($record && $record->url) {
                            $slug = ltrim($record->url, '/');
                            if ($slug === '') $slug = 'home';
                            $page = Page::where('slug', $slug)->first();
                            if ($page) {
                                $set('page_id', $page->id);
                            }
                        }
                    })
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                        if ($state) {
                            $page = Page::find($state);
                            if ($page) {
                                $set('label', $page->title);
                                $url = ($page->slug === 'home') ? '/' : '/' . $page->slug;
                                $set('url', $url);

                                // Check if this page already exists in the selected location
                                $location = $get('location') ?? 'header';
                                $existingMenu = Menu::where('location', $location)->where('url', $url)->first();
                                
                                if ($existingMenu) {
                                    // Use existing order if it already exists
                                    $set('order_column', $existingMenu->order_column);
                                } else {
                                    // Otherwise use next available number
                                    $set('order_column', (Menu::where('location', $location)->max('order_column') ?? 0) + 1);
                                }
                            }
                        }
                    })
                    ->dehydrated(false),
                Forms\Components\TextInput::make('order_column')
                    ->numeric()
                    ->default(fn () => (Menu::where('location', 'header')->max('order_column') ?? 0) + 1)
                    ->label('Display Order'),
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Hidden::make('url')
                    ->required(),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Item')
                    ->options(function (Forms\Get $get) {
                        return Menu::where('location', $get('location'))
                            ->whereNull('parent_id')
                            ->pluck('label', 'id');
                    })
                    ->placeholder('None (Top Level)')
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'header' => 'success',
                        'footer' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('label')->searchable(),
                Tables\Columns\TextColumn::make('parent.label')->label('Parent'),
                Tables\Columns\TextColumn::make('order_column')->label('Order')->sortable(),
            ])
            ->defaultSort('order_column')
            ->filters([
                Tables\Filters\SelectFilter::make('location')
                    ->options([
                        'header' => 'Header',
                        'footer' => 'Footer',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
