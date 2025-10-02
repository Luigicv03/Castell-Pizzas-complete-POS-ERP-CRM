<!-- Layout Visual de Mesas por Zona -->
<div class="space-y-6" x-data="{ selectedZone: 'Salón Principal' }">
    <!-- Selector de Zonas -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">Distribución de Mesas</h3>
            <p class="text-sm text-gray-500">Layout visual del restaurante - Haz clic en una mesa para ver su orden</p>
            
            <!-- Zone Tabs -->
            <div class="mt-3 flex space-x-2 overflow-x-auto scrollbar-thin pb-2">
                @foreach($tablesByZone as $zone => $zoneTables)
                <button @click="selectedZone = '{{ $zone }}'" 
                        :class="selectedZone === '{{ $zone }}' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors">
                    {{ $zone }} ({{ $zoneTables->count() }})
                </button>
                @endforeach
            </div>
        </div>
        <div class="card-body bg-gray-50">
            @foreach($tablesByZone as $zone => $zoneTables)
            <div x-show="selectedZone === '{{ $zone }}'" class="p-4 lg:p-6">
                @if($zone === 'Salón Principal')
                    <!-- Layout Salón Principal - Mesa 3 arriba de Mesa 2 -->
                    <div class="grid grid-cols-6 gap-3 lg:gap-4 max-w-4xl mx-auto">
                        @foreach($zoneTables as $table)
                            @if($table->name === 'Mesa 1')
                                <div class="col-start-1 row-start-2">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 2')
                                <div class="col-start-2 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 3')
                                <div class="col-start-2 row-start-2">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 4')
                                <div class="col-start-3 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 5')
                                <div class="col-start-3 row-start-2">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif($zone === 'Salón Inferior')
                    <!-- Layout Salón Inferior - Mesa 6 sola, 7-8-9 en línea, 10-11, 12-13 -->
                    <div class="grid grid-cols-7 gap-3 lg:gap-4 max-w-5xl mx-auto">
                        @foreach($zoneTables as $table)
                            @if($table->name === 'Mesa 6')
                                <div class="col-start-4 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 7')
                                <div class="col-start-5 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 8')
                                <div class="col-start-6 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 9')
                                <div class="col-start-7 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 10')
                                <div class="col-start-5 row-start-2">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 11')
                                <div class="col-start-5 row-start-3">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 12')
                                <div class="col-start-4 row-start-3">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 13')
                                <div class="col-start-3 row-start-3">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif($zone === 'Terraza')
                    <!-- Layout Terraza - 4 mesas -->
                    <div class="grid grid-cols-4 gap-3 lg:gap-4 max-w-2xl mx-auto">
                        @foreach($zoneTables as $table)
                            @if($table->name === 'Mesa 14')
                                <div class="col-start-2 row-start-2">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 15')
                                <div class="col-start-1 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 16')
                                <div class="col-start-3 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($table->name === 'Mesa 17')
                                <div class="col-start-4 row-start-1">
                                    <div class="table-card {{ $table->getStatusColorClass() }} cursor-pointer hover:shadow-lg transition-all aspect-square"
                                         onclick="openTableOrder({{ $table->id }}, '{{ $table->status }}')">
                                        <div class="text-center">
                                            <div class="font-bold text-xs lg:text-sm">{{ $table->name }}</div>
                                            <div class="text-xs mt-0.5 lg:mt-1">{{ $table->capacity }}p</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

