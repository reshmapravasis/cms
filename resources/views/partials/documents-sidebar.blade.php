{{-- Documents / Resources Card --}}
@if(!empty($data['documents']))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center gap-2 mb-4">
            <svg class="h-5 w-5 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="font-bold text-gray-800 text-base">{{ $data['heading'] ?? 'Resources' }}</h3>
        </div>
        <div class="flex flex-col gap-2">
            @foreach($data['documents'] as $doc)
                @if(!empty($doc['file']))
                    <a href="{{ Storage::url($doc['file']) }}"
                       target="_blank"
                       class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 transition-all group">
                        {{-- PDF / Doc icon --}}
                        <div class="flex-shrink-0 w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center border border-red-100">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                                <path d="M14 2v6h6"/>
                                <path fill="white" d="M8 13h8M8 17h4" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700 transition-colors leading-tight flex-1">
                            {{ $doc['name'] ?? 'Document' }}
                        </span>
                        <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@endif

{{-- Contact / Help Card --}}
@if(!empty($data['show_contact_card']) && !empty($data['contact_link']))
    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-blue-600 to-blue-800 p-5 text-white shadow-lg shadow-blue-600/25">
        <h3 class="font-bold text-lg mb-1">{{ $data['contact_title'] ?? 'Need Help?' }}</h3>
        <p class="text-blue-100 text-sm mb-5 leading-relaxed">
            {{ $data['contact_text'] ?? 'Contact our experts for customized IT solutions.' }}
        </p>
        <a href="{{ $data['contact_link'] }}"
           class="block w-full bg-white text-blue-700 font-bold text-center py-2.5 rounded-xl hover:bg-blue-50 transition-colors text-sm">
            {{ $data['contact_btn_text'] ?? 'Contact Us' }}
        </a>
    </div>
@endif
