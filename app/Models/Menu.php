<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'location',
        'label',
        'url',
        'order_column',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order_column');
    }

    public static function getHeader()
    {
        return static::where('location', 'header')
            ->whereNull('parent_id')
            ->orderBy('order_column')
            ->with('children')
            ->get();
    }

    public static function getFooter()
    {
        return static::where('location', 'footer')
            ->whereNull('parent_id')
            ->orderBy('order_column')
            ->with('children')
            ->get();
    }
}
