<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Formulario de selección -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            {{ $this->form }}
        </div>

        <!-- Lista de estudiantes (solo si hay matrícula seleccionada) -->
        @if($matriculaId && !empty($estudiantes))
            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Lista de Estudiantes
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Total de estudiantes: {{ count($estudiantes) }}
                    </p>
                </div>

                <div class="p-6">
                    <!-- Acciones rápidas -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button
                            type="button"
                            onclick="marcarTodos('presente')"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-800 dark:focus:ring-offset-gray-800"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Todos Presentes
                        </button>

                        <button
                            type="button"
                            onclick="marcarTodos('falta')"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Todos Falta
                        </button>

                        <button
                            type="button"
                            onclick="marcarTodos('tardanza')"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-white bg-yellow-600 border border-transparent rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-gray-800"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Todos Tardanza
                        </button>
                    </div>

                    <!-- Tabla de estudiantes -->
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg dark:ring-opacity-10">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        DNI
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Nombre Completo
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Comentario
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Estado Registro
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach($estudiantes as $index => $estudiante)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                            {{ $estudiante['dni'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                            {{ $estudiante['nombre_completo'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select
                                                wire:model="estudiantes.{{ $index }}.estado"
                                                class="block w-full border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400 sm:text-sm estado-select"
                                            >
                                                <option value="presente">Presente</option>
                                                <option value="tardanza">Tardanza</option>
                                                <option value="falta">Falta</option>
                                                <option value="justificado">Justificado</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input
                                                type="text"
                                                wire:model="estudiantes.{{ $index }}.comentario"
                                                placeholder="Comentario opcional..."
                                                class="block w-full border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400 sm:text-sm"
                                            />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($estudiante['ya_registrado'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Registrado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Pendiente
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Botón de guardar -->
                    <div class="flex justify-end mt-6">
                        <button
                            type="button"
                            wire:click="guardarAsistencias"
                            wire:confirm="¿Estás seguro de que quieres guardar todos los registros de asistencia?"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Todas las Asistencias
                        </button>
                    </div>
                </div>
            </div>
        @elseif($matriculaId && empty($estudiantes))
            <div class="p-4 border border-yellow-200 rounded-md bg-yellow-50 dark:bg-yellow-800 dark:border-yellow-700">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-400 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-100">
                            No hay estudiantes en esta matrícula
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>La matrícula seleccionada no tiene estudiantes asociados.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function marcarTodos(estado) {
            // Actualizar todos los selects de estado
            const selects = document.querySelectorAll('.estado-select');
            selects.forEach(select => {
                select.value = estado;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                // Trigger Livewire update
                select.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }
    </script>
</x-filament-panels::page>
