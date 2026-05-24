{{-- PAGE LOADING SPINNER --}}
{{-- Show on initial full page load AND on Livewire SPA navigation --}}
<div
    id="page-spinner"
    style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center"
    class="bg-white/60 dark:bg-zinc-900/60 backdrop-blur-sm transition-opacity duration-150">
    <div class="flex flex-col items-center gap-3">
        <div class="relative w-12 h-12">
            <div class="absolute inset-0 rounded-full border-4 border-indigo-100 dark:border-zinc-700"></div>
            <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-indigo-600 animate-spin"></div>
        </div>
        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-400 dark:text-indigo-300">Loading…</span>
    </div>
</div>

<script>
    (function () {
        var spinner = document.getElementById('page-spinner');

        // Hide spinner once the page has fully loaded
        function hideSpinner() {
            if (!spinner) return;
            spinner.style.opacity = '0';
            setTimeout(function () { spinner.style.display = 'none'; }, 150);
        }

        // Show spinner (e.g. during Livewire navigate)
        function showSpinner() {
            if (!spinner) return;
            spinner.style.display = 'flex';
            requestAnimationFrame(function () { spinner.style.opacity = '1'; });
        }

        // Hide after full page load
        if (document.readyState === 'complete') {
            hideSpinner();
        } else {
            window.addEventListener('load', hideSpinner);
        }

        // Livewire SPA navigation events
        window.addEventListener('livewire:navigate-start', showSpinner);
        window.addEventListener('livewire:navigated', hideSpinner);
    })();
</script>
