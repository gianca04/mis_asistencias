<x-filament-panels::page>
    {{-- Flowbite CSS --}}
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
        <style>
            .pulse-dot {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            @keyframes pulse {
                0%, 100% {
                    opacity: 1;
                }
                50% {
                    opacity: .5;
                }
            }

            .event-container {
                max-height: 450px;
                overflow-y: auto;
            }

            .event-container::-webkit-scrollbar {
                width: 8px;
            }

            .event-container::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }

            .event-container::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
                border: 2px solid #f1f5f9;
            }

            .event-container::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            .fade-in {
                animation: fadeIn 0.4s ease-out;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-15px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .event-filter.active {
                background-color: #2563eb !important;
                color: white !important;
            }

            .event-item {
                transition: all 0.2s ease-in-out;
            }

            .event-item:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .stats-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }

            .stats-card-success {
                background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            }

            .stats-card-warning {
                background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            }

            .stats-card-info {
                background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            }

            .connection-indicator {
                position: relative;
                display: inline-block;
            }

            .connection-indicator::after {
                content: '';
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                border-radius: 50%;
                border: 2px solid currentColor;
                opacity: 0.3;
                animation: connectionPing 2s infinite;
            }

            @keyframes connectionPing {
                0% {
                    transform: scale(1);
                    opacity: 0.3;
                }
                50% {
                    transform: scale(1.2);
                    opacity: 0.1;
                }
                100% {
                    transform: scale(1.4);
                    opacity: 0;
                }
            }
        </style>
    @endpush

    <div class="space-y-6">
        {{-- Estado de conexión --}}
        <x-filament::section>
            <x-slot name="heading">
                Estado de Conexión
            </x-slot>

            <div id="connection-status" class="flex items-center gap-3 p-4 border border-red-200 rounded-lg bg-red-50">
                <div class="relative">
                    <div class="w-4 h-4 bg-red-500 rounded-full pulse-dot"></div>
                    <div class="absolute top-0 left-0 w-4 h-4 bg-red-500 rounded-full animate-ping opacity-75"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-red-700">🔴 Desconectado del WebSocket</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Offline
                        </span>
                    </div>
                    <p class="text-sm text-red-600 mt-1">Intentando reconectar...</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Grid principal --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Estado del Sistema --}}
            <x-filament::section>
                <x-slot name="heading">
                    📊 Estado del Sistema
                </x-slot>

                <div id="system-info" class="space-y-3">
                    <div class="text-gray-500">Esperando datos del sistema...</div>
                </div>

                <div class="mt-4">
                    <x-filament::button
                        onclick="solicitarEstado()"
                        size="sm"
                        icon="heroicon-m-arrow-path"
                    >
                        Actualizar Estado
                    </x-filament::button>
                </div>
            </x-filament::section>

            {{-- Salones Activos --}}
            <x-filament::section>
                <x-slot name="heading">
                    🏫 Salones Activos
                </x-slot>

                <div id="salones-info" class="space-y-3">
                    <div class="text-gray-500">Esperando datos de salones...</div>
                </div>
            </x-filament::section>
        </div>

        {{-- Eventos en Tiempo Real --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between">
                    <span>📝 Eventos en Tiempo Real</span>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Live
                        </span>
                    </div>
                </div>
            </x-slot>

            <div class="space-y-4">
                <!-- Filtros de eventos -->
                <div class="flex flex-wrap gap-2 p-3 bg-gray-50 rounded-lg border">
                    <span class="text-sm font-medium text-gray-700">Filtrar por tipo:</span>
                    <button type="button" onclick="filterEvents('all')" class="event-filter active inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Todos
                    </button>
                    <button type="button" onclick="filterEvents('success')" class="event-filter inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 border border-green-300 rounded-full hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Éxito
                    </button>
                    <button type="button" onclick="filterEvents('detection')" class="event-filter inline-flex items-center px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 border border-purple-300 rounded-full hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Detecciones
                    </button>
                    <button type="button" onclick="filterEvents('error')" class="event-filter inline-flex items-center px-3 py-1 text-xs font-medium text-red-700 bg-red-100 border border-red-300 rounded-full hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Errores
                    </button>
                </div>

                <div id="events" class="p-4 space-y-3 border border-gray-200 rounded-lg event-container bg-gray-50">
                    <div class="flex items-center justify-center py-8 text-gray-500">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm">No hay eventos para mostrar</p>
                            <p class="text-xs text-gray-400">Los eventos aparecerán aquí en tiempo real</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-filament::button
                        onclick="limpiarEventos()"
                        size="sm"
                        color="danger"
                        icon="heroicon-m-trash"
                    >
                        Limpiar Eventos
                    </x-filament::button>

                    <x-filament::button
                        onclick="exportarEventos()"
                        size="sm"
                        color="gray"
                        icon="heroicon-m-arrow-down-tray"
                    >
                        Exportar
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
        <script>
            let socket;
            const eventsDiv = document.getElementById('events');
            const statusDiv = document.getElementById('connection-status');
            const systemInfoDiv = document.getElementById('system-info');
            const salonesInfoDiv = document.getElementById('salones-info');

            // Inicializar conexión WebSocket
            function initWebSocket() {
                const websocketUrl = '{{ $websocket_url }}';
                socket = io(websocketUrl);

                setupSocketEvents();
            }

            function setupSocketEvents() {
                socket.on('connect', function() {
                    statusDiv.innerHTML = `
                        <div class="relative connection-indicator">
                            <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-green-700">🟢 Conectado al WebSocket</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Online
                                </span>
                            </div>
                            <p class="text-sm text-green-600 mt-1">Conexión establecida correctamente</p>
                        </div>
                    `;
                    statusDiv.className = 'flex items-center gap-3 p-4 rounded-lg bg-green-50 border border-green-200';
                    addEvent('🔌 Conectado al servidor WebSocket', 'success');
                });

                socket.on('disconnect', function() {
                    statusDiv.innerHTML = `
                        <div class="relative">
                            <div class="w-4 h-4 bg-red-500 rounded-full pulse-dot"></div>
                            <div class="absolute top-0 left-0 w-4 h-4 bg-red-500 rounded-full animate-ping opacity-75"></div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-red-700">🔴 Desconectado del WebSocket</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Offline
                                </span>
                            </div>
                            <p class="text-sm text-red-600 mt-1">Intentando reconectar...</p>
                        </div>
                    `;
                    statusDiv.className = 'flex items-center gap-3 p-4 rounded-lg bg-red-50 border border-red-200';
                    addEvent('🔌 Desconectado del servidor WebSocket', 'error');
                });

                socket.on('conexion_establecida', function(data) {
                    addEvent(`✅ ${data.mensaje} - Total clientes: ${data.clientes_total}`, 'success');
                });

                socket.on('sistema_estado', function(data) {
                    updateSystemInfo(data);
                    updateSalonesInfo(data.salones);
                    addEvent(`📊 Estado del sistema actualizado - ${data.resumen.mensaje}`, 'info');
                });

                socket.on('deteccion_rostro', function(data) {
                    const salon = data.salon;
                    addEvent(`👤 ${data.mensaje} (Frame #${salon.frame_numero})`, 'detection');
                });

                socket.on('salon_update', function(data) {
                    const salon = data.salon;
                    addEvent(`🏫 Salón ${salon.codigo_matricula}: ${salon.estado_visual}`, 'info');
                });

                socket.on('log_evento', function(data) {
                    const log = data.log;
                    addEvent(`${log.icono} ${log.mensaje}`, log.tipo);
                });

                socket.on('cache_update', function(data) {
                    const cache = data.cache;
                    addEvent(`${cache.mensaje} (Salón ${cache.matricula_id})`, 'info');
                });

                socket.on('error', function(data) {
                    addEvent(`❌ Error: ${data.mensaje}`, 'error');
                });
            }

            function updateSystemInfo(data) {
                const sistema = data.sistema;
                systemInfoDiv.innerHTML = `
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">Versión:</span>
                                <span class="text-gray-600">${sistema.version}</span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">Auto-sync:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${sistema.auto_sync_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${sistema.auto_sync_activo ? '✅ Activo' : '❌ Inactivo'}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">Salones totales:</span>
                                <span class="text-gray-600">${sistema.salones_totales}</span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">Monitoreando:</span>
                                <span class="text-gray-600">${sistema.salones_monitoreando}</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-3 mt-3 border-t border-gray-200">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Estado:</span>
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                                ${data.resumen.estado_general}
                            </span>
                        </div>
                    </div>
                `;
            }

            function updateSalonesInfo(salones) {
                if (!salones || salones.length === 0) {
                    salonesInfoDiv.innerHTML = `
                        <div class="flex items-center justify-center py-8 text-gray-500">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-sm">No hay salones registrados</p>
                                <p class="text-xs text-gray-400">Los salones aparecerán aquí cuando estén disponibles</p>
                            </div>
                        </div>
                    `;
                    return;
                }

                let salonesHtml = '';
                salones.forEach((salon, index) => {
                    const isActive = salon.monitoreando;
                    const delay = index * 100; // Stagger animation
                    salonesHtml += `
                        <div class="p-4 border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 ${isActive ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-200' : 'bg-white'}" style="animation-delay: ${delay}ms;">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-gray-900">${salon.codigo_matricula}</h3>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                            ${isActive ? '🟢 Activo' : '🔴 Inactivo'}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500">ID: ${salon.matricula_id}</p>
                                </div>
                                <div class="flex items-center">
                                    ${isActive ?
                                        '<div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>' :
                                        '<div class="w-2 h-2 bg-gray-300 rounded-full"></div>'
                                    }
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <div class="text-2xl font-bold text-blue-600">${salon.rostros_cargados}</div>
                                    <div class="text-xs text-blue-700 font-medium">Rostros</div>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg border border-purple-100">
                                    <div class="text-2xl font-bold text-purple-600">${salon.detecciones_hoy}</div>
                                    <div class="text-xs text-purple-700 font-medium">Detecciones</div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                salonesInfoDiv.innerHTML = salonesHtml;
            }

            function addEvent(message, type = 'info') {
                const timestamp = new Date().toLocaleTimeString();
                const eventDiv = document.createElement('div');

                let bgColor = 'bg-blue-50 border-blue-200';
                let textColor = 'text-blue-800';
                let icon = '📄';
                let badgeColor = 'bg-blue-100 text-blue-800';

                switch (type) {
                    case 'success':
                        bgColor = 'bg-green-50 border-green-200';
                        textColor = 'text-green-800';
                        icon = '✅';
                        badgeColor = 'bg-green-100 text-green-800';
                        break;
                    case 'error':
                        bgColor = 'bg-red-50 border-red-200';
                        textColor = 'text-red-800';
                        icon = '❌';
                        badgeColor = 'bg-red-100 text-red-800';
                        break;
                    case 'detection':
                        bgColor = 'bg-purple-50 border-purple-200';
                        textColor = 'text-purple-800';
                        icon = '👤';
                        badgeColor = 'bg-purple-100 text-purple-800';
                        break;
                    case 'info':
                        icon = 'ℹ️';
                        break;
                }

                eventDiv.className = `event-item p-4 border rounded-xl ${bgColor} fade-in`;
                eventDiv.setAttribute('data-event-type', type);
                eventDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full ${badgeColor}">
                                <span class="text-sm">${icon}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <p class="${textColor} text-sm font-medium">${message}</p>
                                <div class="flex-shrink-0 ml-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${badgeColor}">
                                        ${timestamp}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Verificar si hay un placeholder y eliminarlo
                const placeholder = eventsDiv.querySelector('.text-center');
                if (placeholder) {
                    placeholder.parentElement.remove();
                }

                eventsDiv.insertBefore(eventDiv, eventsDiv.firstChild);

                // Limitar a 50 eventos
                while (eventsDiv.children.length > 50) {
                    eventsDiv.removeChild(eventsDiv.lastChild);
                }

                // Auto-scroll si el usuario está cerca del top
                if (eventsDiv.scrollTop < 100) {
                    eventsDiv.scrollTop = 0;
                }
            }

            function solicitarEstado() {
                if (socket && socket.connected) {
                    socket.emit('solicitar_estado');
                    addEvent('📡 Solicitando estado del sistema...', 'info');
                } else {
                    addEvent('❌ No hay conexión WebSocket disponible', 'error');
                }
            }

            function limpiarEventos() {
                eventsDiv.innerHTML = `
                    <div class="flex items-center justify-center py-8 text-gray-500">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm">No hay eventos para mostrar</p>
                            <p class="text-xs text-gray-400">Los eventos aparecerán aquí en tiempo real</p>
                        </div>
                    </div>
                `;
                addEvent('🗑️ Eventos limpiados', 'info');
            }

            // Nueva función para filtrar eventos
            function filterEvents(type) {
                const events = eventsDiv.querySelectorAll('.event-item');
                const filters = document.querySelectorAll('.event-filter');

                // Actualizar botones de filtro
                filters.forEach(filter => filter.classList.remove('active'));
                event.target.classList.add('active');

                // Mostrar/ocultar eventos
                events.forEach(eventEl => {
                    const eventType = eventEl.getAttribute('data-event-type');
                    if (type === 'all' || eventType === type) {
                        eventEl.style.display = 'block';
                        eventEl.classList.add('fade-in');
                    } else {
                        eventEl.style.display = 'none';
                    }
                });
            }

            // Nueva función para exportar eventos
            function exportarEventos() {
                const events = eventsDiv.querySelectorAll('.event-item');
                const eventData = [];

                events.forEach(eventEl => {
                    const type = eventEl.getAttribute('data-event-type');
                    const message = eventEl.querySelector('.font-medium').textContent;
                    const timestamp = eventEl.querySelector('.rounded-full').textContent;

                    eventData.push({
                        timestamp,
                        type,
                        message
                    });
                });

                // Crear archivo CSV
                const csvContent = "data:text/csv;charset=utf-8,"
                    + "Timestamp,Type,Message\n"
                    + eventData.map(e => `"${e.timestamp}","${e.type}","${e.message}"`).join("\n");

                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", `websocket-events-${new Date().toISOString().split('T')[0]}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                addEvent('📁 Eventos exportados exitosamente', 'success');
            }

            // Inicializar cuando se carga la página
            document.addEventListener('DOMContentLoaded', function() {
                initWebSocket();

                // Solicitar estado inicial después de un breve retraso
                setTimeout(() => {
                    solicitarEstado();
                }, 1000);
            });
        </script>
    @endpush
</x-filament-panels::page>
