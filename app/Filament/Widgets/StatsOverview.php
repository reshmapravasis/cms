<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\Page;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $newInquiriesCount = Inquiry::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $totalInquiries = Inquiry::count();
        $publishedPages = Page::where('is_published', true)->count();
        $unpublishedPages = Page::where('is_published', false)->count();

        // Calculate total images and videos ONLY from Gallery pages
        $totalImages = 0;
        $totalVideos = 0;

        // Filter pages that are likely gallery pages based on slug
        $galleryPages = Page::where('slug', 'like', '%gallery%')->get();
        
        foreach ($galleryPages as $page) {
            $content = $page->content ?: [];
            foreach ($content as $block) {
                $data = $block['data'] ?? [];
                
                // Count gallery images
                if ($block['type'] === 'gallery' && !empty($data['images'])) {
                    $totalImages += count($data['images']);
                }

                // Count videos
                if ($block['type'] === 'video' && !empty($data['url'])) {
                    $totalVideos++;
                }
                if ($block['type'] === 'video_gallery' && !empty($data['videos'])) {
                    $totalVideos += count($data['videos']);
                }
            }
        }

        return [
            Stat::make('Total Pages', Page::count())
                ->description($publishedPages . ' published | ' . $unpublishedPages . ' hidden')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
            Stat::make('Blog Posts', Page::where('type', 'post')->count())
                ->description('Active articles')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('info'),
            Stat::make('Total Inquiries', $totalInquiries)
                ->description($newInquiriesCount > 0 ? $newInquiriesCount . ' new this week' : 'Manage your leads')
                ->descriptionIcon('heroicon-m-envelope')
                ->chart([7, 3, 4, 5, 6, 3, $newInquiriesCount])
                ->color($newInquiriesCount > 0 ? 'warning' : 'primary'),
            Stat::make('Media Count', $totalImages + $totalVideos)
                ->description($totalImages . ' images / ' . $totalVideos . ' videos')
                ->descriptionIcon('heroicon-m-photo')
                ->chart([2, 5, 3, 8, 4, 9, $totalImages])
                ->color('success'),
        ];
    }
}
