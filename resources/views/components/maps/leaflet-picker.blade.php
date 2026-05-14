@props([
    'lat' => 'latitude',
    'lng' => 'longitude',
    'height' => 'h-80',
    'helpText' => 'Click on the map, drag the marker, or use the locate button to set the location.',
])

<!-- OpenStreetMap Selector Engine Component -->
<div wire:ignore class="mt-4" x-data="{
    map: null,
    marker: null,
    locating: false,
    lat: @entangle($lat),
    lng: @entangle($lng),
    async initMap() {
        await this.ensureLeaflet();
        this.setupMap();
    },
    async ensureLeaflet() {
        if (window.L) {
            return;
        }

        if (!window.__cemisLeafletLoader) {
            window.__cemisLeafletLoader = new Promise((resolve, reject) => {
                if (!document.querySelector('link[data-cemis-leaflet]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    link.setAttribute('data-cemis-leaflet', 'true');
                    document.head.appendChild(link);
                }

                const existingScript = document.querySelector('script[data-cemis-leaflet]');
                if (existingScript) {
                    existingScript.addEventListener('load', () => resolve(), { once: true });
                    existingScript.addEventListener('error', () => reject(new Error('Unable to load Leaflet.')), { once: true });
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.setAttribute('data-cemis-leaflet', 'true');
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('Unable to load Leaflet.'));
                document.head.appendChild(script);
            });
        }

        await window.__cemisLeafletLoader;
    },
    setupMap() {
        // Fallback default coordinates if null (Sri Lanka)
        let initLat = this.lat ? parseFloat(this.lat) : 7.8731;
        let initLng = this.lng ? parseFloat(this.lng) : 80.7718;

        // Auto-correct accidentally swapped Sri Lanka coordinates
        if (initLat > 50 && initLng < 20) {
            let temp = initLat;
            initLat = initLng;
            initLng = temp;
            
            // Sync the corrected values back to Livewire
            this.lat = initLat.toFixed(6);
            this.lng = initLng.toFixed(6);
        }
        
        this.map = L.map(this.$refs.map).setView([initLat, initLng], this.lat ? 15 : 7);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(this.map);
        
        this.marker = L.marker([initLat, initLng], { draggable: true }).addTo(this.map);
        
        this.marker.on('dragend', (e) => {
            let pos = e.target.getLatLng();
            this.lat = pos.lat.toFixed(6);
            this.lng = pos.lng.toFixed(6);
        });
        
        this.map.on('click', (e) => {
            this.marker.setLatLng(e.latlng);
            this.lat = e.latlng.lat.toFixed(6);
            this.lng = e.latlng.lng.toFixed(6);
        });

        // This fixes the 'no display'/grey box issues by automatically 
        // resizing the map canvas whenever the container size changes (e.g., inside a flex modal).
        let observer = new ResizeObserver(() => {
            if (this.map) this.map.invalidateSize();
        });
        observer.observe(this.$refs.map);

        // Bi-directional watch: When Livewire inputs change the latitude, we move the map marker
        this.$watch('lat', (val) => {
            if(val && this.marker && parseFloat(val) !== this.marker.getLatLng().lat) {
                this.marker.setLatLng([this.lat, this.lng]);
                this.map.setView([this.lat, this.lng]);
            }
        });
        
        this.$watch('lng', (val) => {
            if(val && this.marker && parseFloat(val) !== this.marker.getLatLng().lng) {
                this.marker.setLatLng([this.lat, this.lng]);
                this.map.setView([this.lat, this.lng]);
            }
        });

        // Trigger an initial resize just in case to fix rendering
        setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 300);
    },
    getCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }
        
        // Optional tracking flag for UI loading state
        this.locating = true;
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                this.locating = false;
                let newLat = position.coords.latitude;
                let newLng = position.coords.longitude;
                
                this.lat = newLat.toFixed(6);
                this.lng = newLng.toFixed(6);
                
                // Map and marker will auto-update because of our $watchers!
            },
            (error) => {
                this.locating = false;
                alert('Unable to retrieve your location: ' + error.message);
            },
            { enableHighAccuracy: true }
        );
    }
}" x-init="initMap()">
    <div class="relative">
        <div x-ref="map" class="{{ $height }} w-full rounded-md border border-gray-300 dark:border-gray-600" style="z-index: 10;"></div>
        
        <!-- Get Current Location Button -->
        <button type="button" @click.prevent="getCurrentLocation()" 
            class="absolute top-3 right-3 bg-white dark:bg-slate-800 p-2 rounded shadow-md text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none transition-colors" 
            style="z-index: 400;" 
            title="Get my current location">
            
            <svg x-show="!locating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                <!-- Target icon -->
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" class="hidden"></path>
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 2v2m0 16v2m10-10h-2M4 12H2"></path>
            </svg>

            <!-- Loading Spinner -->
            <svg x-show="locating" class="animate-spin w-5 h-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
    </div>
    
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __($helpText) }}</p>
</div>
