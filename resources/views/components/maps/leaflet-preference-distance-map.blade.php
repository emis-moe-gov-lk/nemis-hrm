@props([
    'origin' => [],
    'schools' => [],
    'height' => 'h-[28rem]',
])

<div
    x-data="{
        map: null,
        loading: false,
        loadingLabel: 'Resolving address...',
        statusMessage: '',
        origin: @js($origin),
        schools: @js($schools),
        schoolSummaries: [],
        colorPalette: ['#2563eb', '#7c3aed', '#0f766e', '#ea580c', '#dc2626', '#0891b2', '#65a30d'],
        async initMap() {
            await this.ensureLeaflet();
            await this.setupMap();
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
        normalizeCoords(lat, lng) {
            if (lat === null || lng === null || lat === '' || lng === '') {
                return null;
            }

            let latitude = Number.parseFloat(lat);
            let longitude = Number.parseFloat(lng);

            if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                return null;
            }

            if (latitude > 50 && longitude < 20) {
                [latitude, longitude] = [longitude, latitude];
            }

            if (latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) {
                return null;
            }

            return { lat: latitude, lng: longitude };
        },
        colorFor(index) {
            return this.colorPalette[index % this.colorPalette.length];
        },
        schoolIcon(order, color) {
            return L.divIcon({
                className: '',
                html: `<div style='width:34px;height:34px;border-radius:9999px;background:${color};border:3px solid rgba(255,255,255,0.95);box-shadow:0 10px 25px rgba(15,23,42,0.18);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:800;'>${order}</div>`,
                iconSize: [34, 34],
                iconAnchor: [17, 17],
            });
        },
        homeIcon() {
            return L.divIcon({
                className: '',
                html: '<div style=\'width:38px;height:38px;border-radius:9999px;background:#111827;border:3px solid rgba(255,255,255,0.95);box-shadow:0 10px 25px rgba(15,23,42,0.22);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;\'>&#8962;</div>',
                iconSize: [38, 38],
                iconAnchor: [19, 19],
            });
        },
        async resolveOrigin() {
            const savedOrigin = this.normalizeCoords(this.origin.lat, this.origin.lng);
            const queries = Array.isArray(this.origin.query_candidates)
                ? this.origin.query_candidates.filter(Boolean)
                : [];

            if (savedOrigin) {
                this.statusMessage = 'Distances are calculated from the saved application coordinates.';

                return {
                    ...savedOrigin,
                    label: this.origin.label,
                    source: 'saved',
                };
            }

            if (queries.length) {
                this.loading = true;

                try {
                    for (const query of queries) {
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=${encodeURIComponent(query)}`, {
                            headers: {
                                Accept: 'application/json',
                            },
                        });

                        if (!response.ok) {
                            continue;
                        }

                        const data = await response.json();
                        if (!Array.isArray(data) || !data[0]) {
                            continue;
                        }

                        const resolved = this.normalizeCoords(data[0].lat, data[0].lon);
                        if (!resolved) {
                            continue;
                        }

                        this.statusMessage = `Distances are estimated from the teacher's typed permanent address.`;

                        return {
                            ...resolved,
                            label: this.origin.label,
                            source: 'estimated',
                        };
                    }
                } catch (error) {
                    console.error(error);
                } finally {
                    this.loading = false;
                }
            }

            this.statusMessage = 'Permanent-address coordinates could not be resolved automatically. Showing preference schools and any saved distance values only.';

            return null;
        },
        haversineDistanceKm(start, end) {
            const toRadians = (value) => (value * Math.PI) / 180;
            const earthRadiusKm = 6371;
            const deltaLat = toRadians(end.lat - start.lat);
            const deltaLng = toRadians(end.lng - start.lng);

            const a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2)
                + Math.cos(toRadians(start.lat)) * Math.cos(toRadians(end.lat))
                * Math.sin(deltaLng / 2) * Math.sin(deltaLng / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return earthRadiusKm * c;
        },
        formatDistance(distanceKm, savedDistanceLabel = null) {
            if (distanceKm !== null && distanceKm !== undefined && Number.isFinite(distanceKm)) {
                return `${distanceKm.toFixed(2)} km`;
            }

            if (savedDistanceLabel) {
                return `Saved: ${savedDistanceLabel}`;
            }

            return 'Unavailable';
        },
        sourceDescription(originSource) {
            return originSource === 'estimated'
                ? `teacher's typed permanent address`
                : 'saved application coordinates';
        },
        async fetchRoadRoute(start, end) {
            const controller = new AbortController();
            const timeout = window.setTimeout(() => controller.abort(), 10000);

            try {
                const response = await fetch(
                    `https://router.project-osrm.org/route/v1/driving/${start.lng},${start.lat};${end.lng},${end.lat}?overview=full&geometries=geojson&steps=false`,
                    {
                        headers: {
                            Accept: 'application/json',
                        },
                        signal: controller.signal,
                    }
                );

                if (!response.ok) {
                    return null;
                }

                const payload = await response.json();
                const route = Array.isArray(payload?.routes) ? payload.routes[0] : null;
                const geometry = Array.isArray(route?.geometry?.coordinates)
                    ? route.geometry.coordinates
                    : [];

                if (!route || !Number.isFinite(route.distance) || geometry.length < 2) {
                    return null;
                }

                return {
                    distanceKm: route.distance / 1000,
                    lineCoordinates: geometry
                        .map((point) => {
                            if (!Array.isArray(point) || point.length < 2) {
                                return null;
                            }

                            return [Number.parseFloat(point[1]), Number.parseFloat(point[0])];
                        })
                        .filter((point) => Array.isArray(point) && Number.isFinite(point[0]) && Number.isFinite(point[1])),
                };
            } catch (error) {
                console.error(error);

                return null;
            } finally {
                window.clearTimeout(timeout);
            }
        },
        buildRouteStatusMessage(origin, routeableCount, routeSuccessCount) {
            const source = this.sourceDescription(origin.source);

            if (routeableCount === 0) {
                return this.statusMessage;
            }

            if (routeSuccessCount === routeableCount) {
                return `Road distances are calculated from the ${source}.`;
            }

            if (routeSuccessCount > 0) {
                return `Road distances are calculated from the ${source} where routes are available. ${routeableCount - routeSuccessCount} school(s) are using straight-line fallback distances.`;
            }

            return `Road routes could not be resolved right now. Showing straight-line fallback distances from the ${source}.`;
        },
        async setupMap() {
            this.map = L.map(this.$refs.map, {
                scrollWheelZoom: false,
            }).setView([7.8731, 80.7718], 7);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(this.map);

            const origin = await this.resolveOrigin();
            const bounds = [];

            if (origin) {
                const homeMarker = L.marker([origin.lat, origin.lng], {
                    icon: this.homeIcon(),
                }).addTo(this.map);

                homeMarker.bindPopup(
                    `<strong>${origin.label ?? 'Permanent Address'}</strong><br>${this.origin.address ?? ''}`
                );

                bounds.push([origin.lat, origin.lng]);
            }

            let routeableCount = 0;
            let routeSuccessCount = 0;

            if (origin) {
                this.loading = true;
                this.loadingLabel = 'Calculating road routes...';
            }

            const schoolSummaries = [];

            for (let index = 0; index < this.schools.length; index += 1) {
                const school = this.schools[index];
                const coordinates = this.normalizeCoords(school.lat, school.lng);
                const color = this.colorFor(index);
                let computedDistance = null;
                let routeCoordinates = [];
                let routeSource = null;

                if (origin && coordinates) {
                    routeableCount += 1;

                    const route = await this.fetchRoadRoute(origin, coordinates);

                    if (route) {
                        routeSuccessCount += 1;
                        computedDistance = route.distanceKm;
                        routeCoordinates = route.lineCoordinates;
                        routeSource = 'road';
                    } else {
                        computedDistance = this.haversineDistanceKm(origin, coordinates);
                        routeCoordinates = [
                            [origin.lat, origin.lng],
                            [coordinates.lat, coordinates.lng],
                        ];
                        routeSource = 'fallback';
                    }
                }

                schoolSummaries.push({
                    ...school,
                    color,
                    coordinates,
                    distance_km: computedDistance,
                    distance_label: this.formatDistance(computedDistance, school.saved_distance_label),
                    route_coordinates: routeCoordinates,
                    route_source: routeSource,
                });
            }

            this.schoolSummaries = schoolSummaries;

            if (origin) {
                this.statusMessage = this.buildRouteStatusMessage(origin, routeableCount, routeSuccessCount);
                this.loading = false;
            }

            this.schoolSummaries.forEach((school) => {
                if (!school.coordinates) {
                    return;
                }

                const marker = L.marker([school.coordinates.lat, school.coordinates.lng], {
                    icon: this.schoolIcon(school.order, school.color),
                }).addTo(this.map);

                marker.bindPopup(
                    `<strong>${school.name}</strong><br>${school.zone}<br><span style='font-weight:600;'>Distance:</span> ${school.distance_label}`
                );

                if (origin && Array.isArray(school.route_coordinates) && school.route_coordinates.length >= 2) {
                    L.polyline(
                        school.route_coordinates,
                        {
                            color: school.color,
                            weight: school.route_source === 'road' ? 4 : 3,
                            opacity: school.route_source === 'road' ? 0.8 : 0.7,
                            dashArray: school.route_source === 'road' ? null : '8 8',
                        }
                    ).addTo(this.map);
                }

                bounds.push([school.coordinates.lat, school.coordinates.lng]);
            });

            if (bounds.length > 1) {
                this.map.fitBounds(bounds, {
                    padding: [30, 30],
                });
            } else if (bounds.length === 1) {
                this.map.setView(bounds[0], 12);
            }

            const observer = new ResizeObserver(() => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            });

            observer.observe(this.$refs.map);

            setTimeout(() => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            }, 300);
        },
    }"
    x-init="initMap()"
    class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_20rem]"
>
    <div class="space-y-4">
        <div class="rounded-2xl border border-slate-300 bg-slate-50/70 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Distance Map') }}</p>
                    <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                        {{ __('Permanent-address location compared against all preferred schools on one map. Distances are shown in kilometres using the road route where available and may differ from any manually entered values in the original application.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-500 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                    <span x-show="!loading">{{ __('Preference Schools') }}: <span x-text="schoolSummaries.length"></span></span>
                    <span x-show="loading" style="display: none;" x-text="loadingLabel"></span>
                </div>
            </div>

            <template x-if="statusMessage">
                <p class="mt-3 text-xs font-medium text-slate-500 dark:text-zinc-400" x-text="statusMessage"></p>
            </template>
        </div>

        <div wire:ignore x-ref="map" class="{{ $height }} w-full rounded-[1.5rem] border border-slate-300 dark:border-zinc-700 relative z-10 overflow-hidden shadow-sm"></div>
    </div>

    <div class="rounded-[1.5rem] border border-slate-300 bg-slate-50/60 p-4 dark:border-zinc-700 dark:bg-zinc-800/25">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ __('Distance Summary') }}</p>
                <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">{{ __('Each school is listed with the current road-distance km value shown on the map.') }}</p>
            </div>
        </div>

        <div class="mt-4 space-y-3 max-h-[28rem] overflow-y-auto pr-1">
            <template x-for="school in schoolSummaries" :key="school.institution_workplace_id || school.order">
                <div class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/80">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-black text-white"
                                :style="`background:${school.color}`"
                                x-text="school.order"
                            ></div>
                            <div>
                                <p class="text-sm font-black text-slate-900 dark:text-white" x-text="school.name"></p>
                                <p class="mt-1 text-xs font-medium text-slate-500 dark:text-zinc-400" x-text="school.zone"></p>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">{{ __('Km') }}</p>
                            <p class="mt-1 text-sm font-black text-slate-900 dark:text-white" x-text="school.distance_label"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
