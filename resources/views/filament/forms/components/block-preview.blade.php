<div class="block-preview-container bg-white border border-gray-100 rounded-xl overflow-hidden" x-data="{}">
    @php
        $data = [];
        foreach(get_defined_vars() as $key => $val) {
            if(!in_array($key, ['__path', '__data', '__env', 'app', 'errors', 'blockType', 'data', 'key', 'val'])) {
                $data[$key] = $val;
            }
        }
        $content = view('partials.blocks_loop', ['content' => [['type' => $blockType, 'data' => $data]]])->render();
        // Escape for srcdoc
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.tailwindcss.com?plugins=typography'></script>
            <style>
                body { margin: 0; padding: 20px; font-family: sans-serif; }
                /* Fix for images in preview */
                img { max-width: 100%; height: auto; }
            </style>
        </head>
        <body>
            $content
        </body>
        </html>
        ";
    @endphp
    <iframe srcdoc="{{ $html }}" class="w-full min-h-[600px] border-0" onload="this.style.height=this.contentWindow.document.body.scrollHeight+'px';"></iframe>
</div>
