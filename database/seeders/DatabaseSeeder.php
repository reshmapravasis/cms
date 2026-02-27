<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@pravasis.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
            ]
        );

        \App\Models\Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'is_published' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'heading' => 'Pravasis IT Solution',
                            'heading_color' => '#ffffff',
                            'subheading' => 'Your Partner in Digital Excellence',
                            'subheading_color' => '#dbeafe',
                        ]
                    ],
                    [
                        'type' => 'services',
                        'data' => [
                            'heading' => 'Our Core Services',
                            'items' => [
                                [
                                    'title' => 'Software Development',
                                    'description' => '<p>Custom solutions tailored to your business needs.</p>',
                                ],
                                [
                                    'title' => 'Mobile Apps',
                                    'description' => '<p>High-performance Android and iOS applications.</p>',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        \App\Models\Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'is_published' => true,
                'content' => [
                    [
                        'type' => 'split_content',
                        'data' => [
                            'heading' => 'Who We Are',
                            'heading_color' => '#111827',
                            'content' => '<p>Pravasis IT Solutions Pvt Ltd is a leading software company dedicated to innovation.</p>',
                            'text_color' => '#374151',
                            'image_position' => 'right',
                        ]
                    ]
                ]
            ]
        );

        \App\Models\Page::updateOrCreate(
            ['slug' => 'services'],
            [
                'title' => 'Services',
                'is_published' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'heading' => 'Our Expert Services',
                            'heading_color' => '#ffffff',
                            'subheading' => 'Comprehensive IT solutions for your growth.',
                        ]
                    ],
                    [
                        'type' => 'services',
                        'data' => [
                            'items' => [
                                ['title' => 'Web Development', 'description' => '<p>Responsive and modern websites.</p>'],
                                ['title' => 'UI/UX Design', 'description' => '<p>User-centered design for better engagement.</p>'],
                                ['title' => 'Cloud Solutions', 'description' => '<p>Scalable and secure cloud infrastructure.</p>'],
                            ]
                        ]
                    ]
                ]
            ]
        );
        \App\Models\Page::updateOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Us',
                'is_published' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'heading' => 'Get in Touch',
                            'heading_color' => '#ffffff',
                            'subheading' => 'We are here to help you with your digital journey.',
                        ]
                    ],
                    [
                        'type' => 'contact_form',
                        'data' => [
                            'heading' => 'Send us a Message',
                            'subheading' => 'Fill out the form below and we will get back to you within 24 hours.',
                        ]
                    ]
                ]
            ]
        );
    }
}
