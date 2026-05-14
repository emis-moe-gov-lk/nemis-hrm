@props(['lat', 'lng', 'height' => 'h-64'])

<!-- OpenStreetMap Display Component -->
<div x-data="{
    map: null,
    marker: null,
    initMap() {
        if (!window.L) {
            let link = document.createElement('link'); link.rel = 'stylesheet'; link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'; document.head.appendChild(link);
            let script = document.createElement('script'); script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => { this.setupMap(); };
            document.head.appendChild(script);
        } else {
            this.setupMap();
        }
    },
    setupMap() {
        let latRaw = '{{ $lat }}';
        let lngRaw = '{{ $lng }}';
        
        // Default to Sri Lanka if no coordinates available
        let latVal = latRaw ? parseFloat(latRaw) : 7.8731;
        let lngVal = lngRaw ? parseFloat(lngRaw) : 80.7718;

        // Auto-correct accidentally swapped Sri Lanka coordinates
        if (latVal > 50 && lngVal < 20) {
            let temp = latVal;
            latVal = lngVal;
            lngVal = temp;
        }
        
        this.map = L.map(this.$refs.map, {
            scrollWheelZoom: false // disable scroll zoom for display map
        }).setView([latVal, lngVal], latRaw ? 15 : 7);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(this.map);
        
        if (latRaw && lngRaw) {
            this.marker = L.marker([latVal, lngVal]).addTo(this.map);
        }

        let observer = new ResizeObserver(() => {
            if (this.map) this.map.invalidateSize();
        });
        observer.observe(this.$refs.map);

        setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 300);
    }
}" x-init="initMap()">
    <div wire:ignore x-ref="map" class="{{ $height }} w-full rounded-md border border-gray-300 dark:border-gray-600 relative z-10"></div>
    @if(!$lat || !$lng)
    <p class="text-xs text-yellow-600 dark:text-yellow-500 mt-2">Location coordinates are missing. Please edit the profile to set the precise location on the map.</p>
    @endif
</div>