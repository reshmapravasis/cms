<div class="hidden sm:flex items-center gap-2 px-3 py-1 bg-white dark:bg-white rounded-full border-2 border-primary-500/50 mx-4 shadow-sm">
    <div class="flex items-center gap-1.5">
        <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
        </span>
        <span class="text-[9px] font-bold text-gray-600 tracking-wider uppercase">Session</span>
    </div>
    <div class="h-4 w-[1px] bg-gray-200"></div>
    <span class="text-sm font-black text-primary-600 tabular-nums" id="header-session-timer">00:00:00</span>
</div>

<script>
    (function() {
        const updateTimer = () => {
            const display = document.getElementById('header-session-timer');
            if (!display) return;

            let startTime = sessionStorage.getItem('fi_admin_sess_start');
            if (!startTime) {
                startTime = Date.now();
                sessionStorage.setItem('fi_admin_sess_start', startTime);
            }

            const now = Date.now();
            const diff = Math.floor((now - startTime) / 1000);
            
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            
            const pad = (n) => n.toString().padStart(2, '0');
            const blink = s % 2 === 0 ? ':' : '<span class="opacity-10">:</span>'; 
            
            display.innerHTML = `${pad(h)}${blink}${pad(m)}${blink}${pad(s)}`;
        };

        // Initialize timer
        setInterval(updateTimer, 1000);
        updateTimer();
        
        // Ensure it restarts if Filament swaps content
        document.addEventListener('livewire:navigated', updateTimer);
    })();
</script>
