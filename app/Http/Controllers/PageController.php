<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug = null)
    {
        if ($slug === null) {
            $page = \App\Models\Page::where('slug', 'home')
                ->where('is_published', true)
                ->first();
            if (!$page) {
                return view('welcome');
            }
        } else {
            $page = \App\Models\Page::where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();
        }

        return view('page', compact('page'));
    }

    public function storeInquiry(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        \App\Models\Inquiry::create($validated);

        return back()->with('success', 'Your inquiry has been submitted successfully! We will contact you soon.');
    }
}
