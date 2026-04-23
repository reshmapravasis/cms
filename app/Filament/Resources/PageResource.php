<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Filament\Forms\Components\Actions\Action;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Page Editor')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->required()
                                            ->live(onBlur: true),
                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'page' => 'Normal Page',
                                                'post' => 'Blog Post',
                                            ])
                                            ->default('page')
                                            ->required()
                                            ->live(),
                                        Forms\Components\Select::make('parent_id')
                                            ->label('Parent Page')
                                            ->relationship('parent', 'title')
                                            ->placeholder('Select a parent page (optional)')
                                            ->searchable(),
                                        Forms\Components\Toggle::make('is_published')
                                            ->label('Published')
                                            ->default(true),
                                        Forms\Components\FileUpload::make('featured_image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('pages')
                                            ->visible(fn (Forms\Get $get) => $get('type') === 'post'),
                                        Forms\Components\Textarea::make('excerpt')
                                            ->rows(2)
                                            ->visible(fn (Forms\Get $get) => $get('type') === 'post'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\Builder::make('content')
                                    ->blocks([
                                        Forms\Components\Builder\Block::make('marquee')
                                            ->label(fn (?array $state) => '📢 News Ticker' . ($state ? ': ' . \Illuminate\Support\Str::limit($state['items'][0]['text'] ?? '', 20) : ''))
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('text')->required(),
                                                        Forms\Components\TextInput::make('link'),
                                                    ])
                                                    ->minItems(1)
                                                    ->label('Marquee Items'),
                                                Forms\Components\TextInput::make('speed')
                                                    ->numeric()
                                                    ->default(40)
                                                    ->helperText('Lower is slower, higher is faster (default: 40)'),
                                                Forms\Components\TextInput::make('gap')
                                                    ->default('5rem')
                                                    ->helperText('Gap between items (e.g., 5rem, 50px)'),
                                            ]),
                                        Forms\Components\Builder\Block::make('rich_text')
                                            ->label(fn (?array $state) => '📝 Rich Text' . ($state ? ': ' . \Illuminate\Support\Str::limit(strip_tags($state['content'] ?? ''), 20) : ''))
                                            ->schema([
                                                TiptapEditor::make('content')->output(TiptapOutput::Html)
                                                    ->profile('default')
                                                    ->required(),
                                                Forms\Components\Select::make('text_size')
                                                    ->options([
                                                        'text-sm' => 'Small',
                                                        'text-base' => 'Normal',
                                                        'text-lg' => 'Medium',
                                                        'text-xl' => 'Large',
                                                    ])
                                                    ->default('text-base'),
                                            ]),
                                        Forms\Components\Builder\Block::make('hero')
                                            ->label(fn (?array $state) => '🚀 Hero Section' . ($state ? ': ' . ($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\TextInput::make('heading'),
                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                TiptapEditor::make('content')->output(TiptapOutput::Html),
                                                Forms\Components\TextInput::make('button_text'),
                                                Forms\Components\TextInput::make('button_link'),
                                                Forms\Components\FileUpload::make('image')->image()->disk('public')->directory('hero-images'),
                                            ]),

                                        Forms\Components\Builder\Block::make('image')
                                            ->label(fn (?array $state) => '🖼️ Image' . ($state ? ': ' . ($state['alt'] ?? ($state['caption'] ?? '')) : ''))
                                            ->schema([
                                                Forms\Components\FileUpload::make('image')->image()->disk('public')->required(),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('alt'),
                                                        Forms\Components\TextInput::make('caption'),
                                                        Forms\Components\Select::make('width_percent')
                                                            ->label('Display Width')
                                                            ->options([
                                                                '100' => 'Full Width',
                                                                '80' => 'Large (80%)',
                                                                '60' => 'Medium (60%)',
                                                                '50' => 'Half (50%)',
                                                                '40' => 'Compact (40%)',
                                                                '25' => 'Small (25%)',
                                                            ])
                                                            ->default('100'),
                                                    ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('video')
                                            ->label(fn (?array $state) => '🎥 Single Video' . ($state ? ': ' . ($state['title'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\TextInput::make('url')->label('YouTube/Vimeo URL')->required(),
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title'),
                                                        Forms\Components\Select::make('width_percent')
                                                            ->label('Display Width')
                                                            ->options([
                                                                '100' => 'Full Width',
                                                                '80' => 'Large (80%)',
                                                                '60' => 'Medium (60%)',
                                                                '50' => 'Half (50%)',
                                                                '40' => 'Compact (40%)',
                                                                '25' => 'Small (25%)',
                                                            ])
                                                            ->default('100'),
                                                    ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('split_content')
                                            ->label(fn (?array $state) => '↔️ Split: Text & Image' . ($state ? ': ' . ($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(3)->schema([
                                                    Forms\Components\TextInput::make('heading')->columnSpan(2),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                    TiptapEditor::make('content')->output(TiptapOutput::Html)->columnSpanFull(),
                                                    Forms\Components\ColorPicker::make('text_color')->default('#374151'),
                                                    Forms\Components\FileUpload::make('image')->image()->disk('public')->directory('split-images'),
                                                    Forms\Components\Select::make('image_position')->options(['left' => 'Left', 'right' => 'Right'])->default('right'),
                                                    Forms\Components\Select::make('image_width')
                                                        ->options([
                                                            'w-1/4' => 'Small (25%)',
                                                            'w-1/3' => 'Medium (33%)',
                                                            'w-1/2' => 'Half (50%)',
                                                            'w-2/3' => 'Large (66%)',
                                                            'w-3/4' => 'Huge (75%)',
                                                        ])
                                                        ->default('w-1/2'),
                                                    Forms\Components\TextInput::make('anchor_id')->label('Anchor ID (for jumping to section)')->placeholder('e.g. about-us'),
                                                ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('split_video_content')
                                            ->label(fn (?array $state) => '↔️ Split: Text & Video' . ($state ? ': ' . ($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(3)->schema([
                                                    Forms\Components\TextInput::make('heading')->columnSpan(2),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                    TiptapEditor::make('content')->output(TiptapOutput::Html)->columnSpanFull(),
                                                    Forms\Components\ColorPicker::make('text_color')->default('#374151'),
                                                    Forms\Components\Select::make('video_type')->options(['url' => 'URL', 'file' => 'Upload'])->default('url')->live(),
                                                    Forms\Components\TextInput::make('video_url')->visible(fn (Forms\Get $get) => $get('video_type') === 'url'),
                                                    Forms\Components\FileUpload::make('video_file')->visible(fn (Forms\Get $get) => $get('video_type') === 'file'),
                                                    Forms\Components\Select::make('video_position')->options(['left' => 'Left', 'right' => 'Right'])->default('right'),
                                                    Forms\Components\Select::make('video_width')
                                                        ->options([
                                                            'w-1/4' => 'Small (25%)',
                                                            'w-1/3' => 'Medium (33%)',
                                                            'w-1/2' => 'Half (50%)',
                                                            'w-2/3' => 'Large (66%)',
                                                            'w-3/4' => 'Huge (75%)',
                                                        ])
                                                        ->default('w-1/2'),
                                                    Forms\Components\TextInput::make('anchor_id'),
                                                ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('services')
                                            ->label(fn (?array $state) => '🛠️ Services Grid' . ($state ? ': ' . ($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(3)->schema([
                                                    Forms\Components\TextInput::make('heading')->default('Our Services'),
                                                    Forms\Components\Select::make('columns')
                                                        ->options([
                                                            '2' => '2 Columns',
                                                            '3' => '3 Columns',
                                                            '4' => '4 Columns',
                                                            '5' => '5 Columns',
                                                            '6' => '6 Columns',
                                                        ])->default('3'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                ]),
                                                TiptapEditor::make('description')->output(TiptapOutput::Html),
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title'),
                                                        TiptapEditor::make('description')->output(TiptapOutput::Html),
                                                        Forms\Components\FileUpload::make('icon'),
                                                    ])->grid(3),
                                                Forms\Components\TextInput::make('anchor_id'),
                                            ]),

                                        Forms\Components\Builder\Block::make('testimonials')
                                            ->label(fn (?array $state) => '💬 Testimonials' . ($state ? ': ' . ($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\TextInput::make('heading')->default('What They Say'),
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name'),
                                                        TiptapEditor::make('quote')->output(TiptapOutput::Html),
                                                    ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('contact_form')
                                            ->label(fn (?array $state) => '📧 Contact Form' . ($state ? ': ' . ($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\TextInput::make('heading')->default('Contact Us'),
                                            ]),

                                        Forms\Components\Builder\Block::make('gallery')
                                            ->label(fn (?array $state) => '🖼️ Photo Gallery' . ($state ? ': ' . ($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\TextInput::make('heading'),
                                                Forms\Components\Select::make('columns')
                                                    ->options([
                                                        '2' => '2 Columns',
                                                        '3' => '3 Columns',
                                                        '4' => '4 Columns',
                                                    ])->default('3'),
                                                Forms\Components\Repeater::make('images')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')->image()->disk('public')->directory('gallery'),
                                                        Forms\Components\TextInput::make('label'),
                                                    ])->grid(2),
                                            ]),

                                        Forms\Components\Builder\Block::make('stats')
                                            ->label(fn (?array $state) => '📊 Stats Overview' . ($state ? ': ' . count($state['items'] ?? []) . ' items' : ''))
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('label')->placeholder('e.g. Clients'),
                                                        Forms\Components\TextInput::make('number')->placeholder('e.g. 500+'),
                                                    ])->grid(2),
                                            ]),

                                        Forms\Components\Builder\Block::make('info_cards')
                                            ->label(fn (?array $state) => '🗂️ Info Cards' . ($state ? ': ' . count($state['items'] ?? []) . ' items' : ''))
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title'),
                                                        Forms\Components\Textarea::make('description'),
                                                        Forms\Components\ColorPicker::make('bg_color')->default('#001a72'),
                                                        Forms\Components\ColorPicker::make('text_color')->default('#ffffff'),
                                                    ])->grid(2),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->cloneable()
                                    ->blockNumbers(false)
                                    ->extraItemActions([
                                        Action::make('preview')
                                            ->icon('heroicon-m-eye')
                                            ->label('Preview')
                                            ->modalHeading('Section Preview')
                                            ->modalWidth('7xl')
                                            ->modalSubmitAction(false)
                                            ->modalCancelAction(false)
                                            ->modalContent(fn (array $state, array $arguments, Forms\Components\Builder $component): \Illuminate\Contracts\View\View => (function() use ($state, $arguments) {
                                                $itemKey = $arguments['item'];
                                                $selectedItem = $state[$itemKey] ?? null;
                                                
                                                if (! $selectedItem) {
                                                    return view('filament.forms.components.block-preview', ['blockType' => 'unknown']);
                                                }
                                                
                                                return view(
                                                    'filament.forms.components.block-preview',
                                                    array_merge(
                                                        ['blockType' => $selectedItem['type'] ?? 'unknown'],
                                                        $selectedItem['data'] ?? []
                                                    )
                                                );
                                            })()),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Forms\Components\TextInput::make('seo_title'),
                                Forms\Components\Textarea::make('seo_description'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'page' => 'gray',
                        'post' => 'success',
                    }),
                Tables\Columns\TextColumn::make('parent.title')->label('Parent Page'),
                Tables\Columns\ToggleColumn::make('is_published'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
