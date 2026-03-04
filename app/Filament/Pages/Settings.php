<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\Setting;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = [
            'site_name' => Setting::get('site_name', 'Pravasis IT Solution'),
            'logo' => Setting::get('logo'),
            'email' => Setting::get('email'),
            'phone' => Setting::get('phone'),
            'address' => Setting::get('address'),
            'working_hours' => Setting::get('working_hours'),
            'nav_link_color' => Setting::get('nav_link_color', '#4b5563'),
            'nav_link_hover_color' => Setting::get('nav_link_hover_color', '#001973'),
            'nav_link_active_color' => Setting::get('nav_link_active_color', '#001973'),
            'facebook' => Setting::get('facebook'),
            'twitter' => Setting::get('twitter'),
            'instagram' => Setting::get('instagram'),
            'linkedin' => Setting::get('linkedin'),
            'admin_btn_color' => Setting::get('admin_btn_color', '#2563eb'),
            'admin_btn_hover_color' => Setting::get('admin_btn_hover_color', '#1d4ed8'),
            'header_bg_color' => Setting::get('header_bg_color', '#ffffff'),
            'header_text_color' => Setting::get('header_text_color', '#111827'),
            'top_bar_bg_color' => Setting::get('top_bar_bg_color', '#111827'),
            'top_bar_text_color' => Setting::get('top_bar_text_color', '#ffffff'),
            'header_bg_image' => Setting::get('header_bg_image'),
        ];
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')
                    ->schema([
                        TextInput::make('site_name')->required(),
                        FileUpload::make('logo')->image()->disk('public')->directory('site'),
                        Section::make('Navigation Colors')
                            ->columns(3)
                            ->schema([
                                Forms\Components\ColorPicker::make('nav_link_color')
                                    ->label('Default Color'),
                                Forms\Components\ColorPicker::make('nav_link_hover_color')
                                    ->label('Hover Color'),
                                Forms\Components\ColorPicker::make('nav_link_active_color')
                                    ->label('Active Color'),
                            ]),
                        Section::make('Header Style')
                            ->columns(2)
                            ->schema([
                                Forms\Components\ColorPicker::make('header_bg_color')
                                    ->label('Header Background Color'),
                                Forms\Components\ColorPicker::make('header_text_color')
                                    ->label('Header Text Color (Logo/Links)'),
                                Forms\Components\ColorPicker::make('top_bar_bg_color')
                                    ->label('Top Bar Background Color'),
                                Forms\Components\ColorPicker::make('top_bar_text_color')
                                    ->label('Top Bar Text Color'),
                                Forms\Components\FileUpload::make('header_bg_image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('site')
                                    ->label('Header Background Image')
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Admin Button Colors')
                            ->columns(2)
                            ->schema([
                                Forms\Components\ColorPicker::make('admin_btn_color')
                                    ->label('Button Background'),
                                Forms\Components\ColorPicker::make('admin_btn_hover_color')
                                    ->label('Button Hover Background'),
                            ]),
                    ]),
                Section::make('Contact')
                    ->schema([
                        TextInput::make('email')->email(),
                        TextInput::make('phone'),
                        TextInput::make('address'),
                        TextInput::make('working_hours')
                            ->placeholder('Mon-Fri: 9:00 AM - 6:00 PM'),
                    ]),
                Section::make('Social Links')
                    ->schema([
                        TextInput::make('facebook')->url(),
                        TextInput::make('twitter')->url(),
                        TextInput::make('instagram')->url(),
                        TextInput::make('linkedin')->url(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }
}
