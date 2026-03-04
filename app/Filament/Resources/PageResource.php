<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use FilamentTiptapEditor\TiptapEditor;
use FilamentTiptapEditor\Enums\TiptapOutput;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Page Editor')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(Page::class, 'slug', ignoreRecord: true),
                                Forms\Components\Toggle::make('is_published')
                                    ->default(true),
                                Forms\Components\Select::make('parent_id')
                                    ->label('Parent Page')
                                    ->relationship('parent', 'title')
                                    ->placeholder('Select a parent page (optional)')
                                    ->searchable(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Content')
                            ->schema([
                                Forms\Components\Builder::make('content')
                                    ->blocks([
                                        Forms\Components\Builder\Block::make('marquee')
                                            ->label('📢 News Ticker / Marquee')
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->label('Ticker Items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('text')
                                                            ->required()
                                                            ->placeholder('Enter news or update message'),
                                                        Forms\Components\TextInput::make('link')
                                                            ->placeholder('Link URL (optional)'),
                                                    ])
                                                    ->defaultItems(1)
                                                    ->reorderable()
                                                    ->collapsible(),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\ColorPicker::make('bg_color')
                                                            ->label('Background Color')
                                                            ->default('#1e40af'), 
                                                        Forms\Components\ColorPicker::make('text_color')
                                                            ->label('Text Color')
                                                            ->default('#ffffff'),
                                                        Forms\Components\Select::make('speed')
                                                            ->options([
                                                                'animate-marquee-slow' => 'Slow',
                                                                'animate-marquee-normal' => 'Normal',
                                                                'animate-marquee-fast' => 'Fast',
                                                            ])
                                                            ->default('animate-marquee-normal'),
                                                        Forms\Components\Select::make('font_size')
                                                            ->options([
                                                                'text-xs' => 'Smallest',
                                                                'text-sm' => 'Small',
                                                                'text-base' => 'Normal',
                                                                'text-lg' => 'Large',
                                                                'text-xl' => 'Extra Large',
                                                            ])
                                                            ->default('text-base'),
                                                        Forms\Components\Select::make('font_weight')
                                                            ->options([
                                                                'font-normal' => 'Normal',
                                                                'font-medium' => 'Medium',
                                                                'font-semibold' => 'Semibold',
                                                                'font-bold' => 'Bold',
                                                                'font-black' => 'Black',
                                                            ])
                                                            ->default('font-medium'),
                                                        Forms\Components\Select::make('text_effect')
                                                            ->options([
                                                                'none' => 'None',
                                                                'shadow' => 'Drop Shadow',
                                                                'glow' => 'Glow Effect',
                                                                'outline' => 'Text Outline',
                                                            ])
                                                            ->default('none'),
                                                    ]),
                                                Forms\Components\TextInput::make('separator')
                                                    ->label('Separator Symbol')
                                                    ->default('•')
                                                    ->maxLength(5),
                                            ]),
                                        Forms\Components\Builder\Block::make('rich_text')
                                            ->schema([
                                                TiptapEditor::make('content')
                                                    ->profile('default')
                                                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                                                    ->output(TiptapOutput::Html)
                                                    ->required(),
                                                Forms\Components\ColorPicker::make('text_color')
                                                    ->default('#111827'),
                                                Forms\Components\Select::make('text_size')
                                                    ->options([
                                                        'text-sm' => 'Small',
                                                        'text-base' => 'Normal',
                                                        'text-lg' => 'Medium',
                                                        'text-xl' => 'Large',
                                                        'text-2xl' => 'Extra Large',
                                                    ])
                                                    ->default('text-base'),
                                            ]),
                                        Forms\Components\Builder\Block::make('hero')
                                            ->schema([
                                                Forms\Components\TextInput::make('heading'),
                                                Forms\Components\ColorPicker::make('heading_color')
                                                    ->default('#ffffff'),
                                                Forms\Components\TextInput::make('subheading'),
                                                Forms\Components\ColorPicker::make('subheading_color')
                                                    ->default('#dbeafe'),
                                                Forms\Components\Select::make('text_size')
                                                    ->label('Subheading Size')
                                                    ->options([
                                                        'text-base' => 'Small',
                                                        'text-lg' => 'Normal',
                                                        'text-xl' => 'Medium',
                                                        'text-2xl' => 'Large',
                                                    ])
                                                    ->default('text-xl'),
                                                Forms\Components\FileUpload::make('background_image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('hero-images'),
                                            ]),
                                        Forms\Components\Builder\Block::make('image')
                                            ->schema([
                                                Forms\Components\FileUpload::make('image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->required(),
                                                Forms\Components\TextInput::make('alt'),
                                                Forms\Components\TextInput::make('caption'),
                                                Forms\Components\TextInput::make('image_width')
                                                    ->label('Image Width (px)')
                                                    ->numeric()
                                                    ->default(800)
                                                    ->minValue(10)
                                                    ->maxValue(1200)
                                                    ->step(1),
                                            ]),
                                        Forms\Components\Builder\Block::make('video')
                                            ->schema([
                                                Forms\Components\TextInput::make('url')
                                                    ->label('YouTube/Vimeo URL')
                                                    ->required(),
                                                Forms\Components\TextInput::make('title'),
                                            ]),
                                        Forms\Components\Builder\Block::make('gallery')
                                            ->label('🖼️ Photo Gallery')
                                            ->schema([
                                                Forms\Components\TextInput::make('heading')->placeholder('Gallery Heading (optional)'),
                                                Forms\Components\Repeater::make('images')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('gallery')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('label')
                                                            ->placeholder('Enter label (optional)'),
                                                    ])
                                                    ->grid(3)
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                                    ->required(),
                                                Forms\Components\Select::make('columns')
                                                    ->options([
                                                        '2' => '2 Columns',
                                                        '3' => '3 Columns',
                                                        '4' => '4 Columns',
                                                    ])
                                                    ->default('3'),
                                            ]),
                                        Forms\Components\Builder\Block::make('video_gallery')
                                            ->label('🎬 Video Gallery')
                                            ->schema([
                                                Forms\Components\TextInput::make('heading')->placeholder('Gallery Heading (optional)'),
                                                Forms\Components\Repeater::make('videos')
                                                    ->schema([
                                                        Forms\Components\Select::make('type')
                                                            ->options([
                                                                'url' => 'External URL (YouTube/Video)',
                                                                'file' => 'Local Video Upload',
                                                            ])
                                                            ->default('url')
                                                            ->live()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('url')
                                                            ->label('Video URL')
                                                            ->placeholder('Enter YouTube or Vimeo link')
                                                            ->visible(fn (Forms\Get $get) => $get('type') === 'url')
                                                            ->requiredIf('type', 'url'),
                                                        Forms\Components\FileUpload::make('file')
                                                            ->label('Upload Video')
                                                            ->disk('public')
                                                            ->directory('videos')
                                                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                                                            ->maxSize(102400) // 100MB
                                                            ->visible(fn (Forms\Get $get) => $get('type') === 'file')
                                                            ->requiredIf('type', 'file'),
                                                        Forms\Components\TextInput::make('title')
                                                            ->placeholder('Video Title (optional)'),
                                                    ])
                                                    ->columns(2)
                                                    ->defaultItems(1)
                                                    ->collapsible(),
                                                Forms\Components\Select::make('columns')
                                                    ->options([
                                                        '1' => '1 Column',
                                                        '2' => '2 Columns',
                                                        '3' => '3 Columns',
                                                    ])
                                                    ->default('2'),
                                            ]),
                                        Forms\Components\Builder\Block::make('split_content')
                                            ->label('Split Content (Text & Image)')
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('heading')
                                                            ->required()
                                                            ->columnSpan(2),
                                                         Forms\Components\TextInput::make("anchor_id")->label("Anchor ID (e.g. about-us)"),
                                                        Forms\Components\ColorPicker::make('heading_color')
                                                            ->default('#111827'),
                                                        TiptapEditor::make('content')
                                                            ->profile('default')
                                                            ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                                                            ->output(TiptapOutput::Html)
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\ColorPicker::make('text_color')
                                                            ->default('#374151'),
                                                        Forms\Components\Select::make('text_size')
                                                            ->options([
                                                                'text-xs' => 'Smallest',
                                                                'text-sm' => 'Small',
                                                                'text-base' => 'Normal',
                                                                'text-lg' => 'Large',
                                                            ])
                                                            ->default('text-base'),
                                                        Forms\Components\FileUpload::make('image')
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('content')
                                                            ->required(),
                                                        Forms\Components\Select::make('image_position')
                                                            ->options([
                                                                'left' => 'Image Left',
                                                                'right' => 'Image Right',
                                                            ])
                                                            ->default('right')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('image_width')
                                                            ->label('Image Width (px)')
                                                            ->numeric()
                                                            ->default(250)
                                                            ->minValue(10)
                                                            ->maxValue(800)
                                                            ->step(1),
                                                    ]),
                                            ]),
                                        Forms\Components\Builder\Block::make('services')
                                            ->label('Services Grid')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('anchor_id')->label('Anchor ID (e.g. services-section)'),
                                                        Forms\Components\TextInput::make('heading')->default('Our Services')->columnSpanFull(),
                                                        TiptapEditor::make('description')
                                                            ->label('Section Description')
                                                            ->profile('default')
                                                            ->output(TiptapOutput::Html),
                                                        Forms\Components\Select::make('text_size')
                                                            ->label('Description Size')
                                                            ->options([
                                                                'text-xs' => 'Small',
                                                                'text-sm' => 'Normal',
                                                                'text-base' => 'Medium',
                                                                'text-lg' => 'Large',
                                                            ])
                                                            ->default('text-sm'),
                                                        Forms\Components\FileUpload::make('image')
                                                            ->label('Section Image')
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('services'),
                                                        Forms\Components\TextInput::make('image_width')
                                                            ->label('Section Image Width (px)')
                                                            ->numeric()
                                                            ->default(120)
                                                            ->minValue(10)
                                                            ->maxValue(500)
                                                            ->step(1),
                                                        Forms\Components\Select::make('columns')
                                                            ->label('Items per Row (Desktop)')
                                                            ->options([
                                                                '2' => '2 Columns',
                                                                '3' => '3 Columns',
                                                                '4' => '4 Columns',
                                                                '5' => '5 Columns',
                                                                '6' => '6 Columns',
                                                            ])
                                                            ->default('3')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('view_all_link')
                                                            ->label('Section Button Link (e.g. /services)'),
                                                        Forms\Components\TextInput::make('view_all_text')
                                                            ->label('Section Button Text')
                                                            ->default('Explore All Services'),
                                                    ]),
                                                Forms\Components\Repeater::make('items')
                                                    ->label('Service Items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title')->required(),
                                                        TiptapEditor::make('description')
                                                            ->profile('default')
                                                            ->output(TiptapOutput::Html)
                                                            ->required(),
                                                        Forms\Components\FileUpload::make('icon')
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('services'),
                                                         Forms\Components\TextInput::make('link')->label('Item Read More Link'),
                                                    ])
                                                    ->grid(3)
                                                    ->collapsible(),

                                            ]),
                                        Forms\Components\Builder\Block::make('documents')
                                            ->label('📎 Documents / Resources Sidebar')
                                            ->schema([
                                                Forms\Components\TextInput::make('heading')
                                                    ->label('Section Heading')
                                                    ->default('Resources'),
                                                Forms\Components\Select::make('layout')
                                                    ->label('Display Style')
                                                    ->options([
                                                        'sidebar' => 'Sticky Sidebar (beside services)',
                                                        'section' => 'Full Section (standalone)',
                                                    ])
                                                    ->default('sidebar'),
                                                Forms\Components\Repeater::make('documents')
                                                    ->label('Documents')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Document Name')
                                                            ->required(),
                                                         Forms\Components\FileUpload::make('file')
                                                            ->label('Upload PDF / Brochure / Doc')
                                                            ->disk('public')
                                                            ->directory('documents')
                                                            ->acceptedFileTypes([
                                                                'application/pdf',
                                                                'application/msword',
                                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                                'application/vnd.ms-excel',
                                                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                                                'application/octet-stream', // fallback for some browsers
                                                            ])
                                                            ->maxSize(51200) // 50MB
                                                            ->required(),
                                                    ])
                                                    ->defaultItems(1)
                                                    ->collapsible(),
                                                Forms\Components\Toggle::make('show_contact_card')
                                                    ->label('Show Contact Card')
                                                    ->default(true),
                                                Forms\Components\TextInput::make('contact_title')
                                                    ->label('Contact Card Title')
                                                    ->default('Need Help?'),
                                                Forms\Components\TextInput::make('contact_text')
                                                    ->label('Contact Card Subtitle')
                                                    ->default('Contact our experts for customized IT solutions.'),
                                                Forms\Components\TextInput::make('contact_link')
                                                    ->label('Contact Card Button Link')
                                                    ->default('/contact-us'),
                                                Forms\Components\TextInput::make('contact_btn_text')
                                                    ->label('Contact Card Button Text')
                                                    ->default('Contact Us'),
                                            ]),
                                        Forms\Components\Builder\Block::make('testimonials')
                                            ->label('Testimonials Slider')
                                            ->schema([
                                                Forms\Components\TextInput::make('heading')->default('What Our Clients Say'),
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')->required(),
                                                        Forms\Components\TextInput::make('role'),
                                                        TiptapEditor::make('quote')
                                                            ->profile('default')
                                                            ->output(TiptapOutput::Html)
                                                            ->required(),
                                                        Forms\Components\FileUpload::make('avatar')
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('testimonials'),
                                                    ])
                                                    ->collapsible(),
                                            ]),
                                        Forms\Components\Builder\Block::make('contact_form')
                                            ->schema([
                                                Forms\Components\TextInput::make('heading')
                                                    ->default('Contact Us'),
                                                Forms\Components\TextInput::make('subheading'),
                                            ]),
                                        Forms\Components\Builder\Block::make('stats')
                                            ->label('📊 Statistics Counters')
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('number')
                                                            ->label('Number (e.g. 515)')
                                                            ->numeric()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('label')
                                                            ->label('Label (e.g. Clients)')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('suffix')
                                                            ->label('Suffix (e.g. + or %)')
                                                            ->placeholder('+'),
                                                    ])
                                                    ->grid(4)
                                                    ->defaultItems(4)
                                                    ->collapsible(),
                                            ]),
                                        Forms\Components\Builder\Block::make('info_cards')
                                            ->label('📦 Info Cards (Mission/Vision)')
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title')
                                                            ->required(),
                                                        Forms\Components\Textarea::make('description')
                                                            ->rows(3)
                                                            ->required(),
                                                        Forms\Components\ColorPicker::make('bg_color')
                                                            ->label('Background Color')
                                                            ->default('#001a72'),
                                                        Forms\Components\ColorPicker::make('text_color')
                                                            ->label('Text Color')
                                                            ->default('#ffffff'),
                                                        Forms\Components\Select::make('text_size')
                                                            ->options([
                                                                'text-sm' => 'Small',
                                                                'text-base' => 'Normal',
                                                                'text-lg' => 'Medium',
                                                                'text-xl' => 'Large',
                                                            ])
                                                            ->default('text-lg'),
                                                    ])
                                                    ->grid(2)
                                                    ->defaultItems(2)
                                                    ->collapsible(),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->cloneable(),
                            ])->columnSpanFull(),
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('seo_title'),
                                Forms\Components\Textarea::make('seo_description'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('parent.title')->label('Parent Page'),
                Tables\Columns\TextColumn::make('slug')->searchable(),
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
            'index' => Pages\ManagePages::route('/'),
        ];
    }
}
