<div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 h-full">
    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        {{ $block['data']['heading'] ?? 'Resources' }}
    </h3>
    <div class="space-y-3">
        @foreach($block['data']['documents'] ?? [] as $doc)
            @if(!empty($doc['file']))
                <a href="{{ Storage::url($doc['file']) }}" target="_blank" class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
                    <span class="font-medium text-gray-700 group-hover:text-blue-600 truncate mr-2">{{ $doc['name'] }}</span>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
            @endif
        @endforeach
    </div>
    @if(!empty($block['data']['show_contact_card']))
        <div class="mt-8 p-6 bg-blue-600 rounded-2xl text-white">
            <h4 class="font-bold mb-2">{{ $block['data']['contact_title'] ?? 'Need Help?' }}</h4>
            <p class="text-xs text-blue-100 mb-4 leading-relaxed">{{ $block['data']['contact_text'] ?? '' }}</p>
            <a href="{{ $block['data']['contact_link'] ?? '/contact-us' }}" class="block text-center px-4 py-3 bg-white text-blue-600 rounded-xl text-sm font-bold shadow-lg shadow-blue-800/20 active:scale-95 transition">
                {{ $block['data']['contact_btn_text'] ?? 'Contact Us' }}
            </a>
        </div>
    @endif
</div>
