<div class="block-preview-container bg-white" x-data="{}">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <div class="preview-wrapper scale-[0.7] origin-top">
        @php
            $data = [];
            foreach(get_defined_vars() as $key => $val) {
                if(!in_array($key, ['__path', '__data', '__env', 'app', 'errors', 'blockType', 'data', 'key', 'val'])) {
                    $data[$key] = $val;
                }
            }
        @endphp
        @include('partials.blocks_loop', ['content' => [['type' => $blockType, 'data' => $data]]])
    </div>
</div>
