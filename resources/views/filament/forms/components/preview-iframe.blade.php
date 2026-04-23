<div x-data="{ 
    url: '{{ $getRecord() && $getRecord()->slug ? ($getRecord()->type === 'post' ? route('blog.show', $getRecord()->slug) : route('page.show', $getRecord()->slug)) : '' }}'
}">
    <template x-if="url">
        <div class="w-full bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" style="height: 800px;">
            <div class="bg-gray-100 px-4 py-2 border-b flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                    </div>
                    <span class="text-xs text-gray-500 font-mono ml-2" x-text="url"></span>
                </div>
                <button type="button" @click="$el.closest('div').querySelector('iframe').contentWindow.location.reload()" class="p-1 hover:bg-gray-200 rounded transition">
                    <svg class="w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </button>
            </div>
            <iframe :src="url" class="w-full h-full" frameborder="0"></iframe>
        </div>
    </template>
    <template x-if="!url">
        <div class="flex flex-col items-center justify-center p-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 text-gray-500">
            <svg class="w-12 h-12 mb-4 opacity-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="font-medium">Save this page first to enable the live preview.</p>
            <p class="text-sm mt-1">Once saved, you can view the rendered page right here.</p>
        </div>
    </template>
</div>
