<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Project Filter -->
        <div class="mb-6">
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $projectId ? $projects->firstWhere('id', $projectId)->name : 'Pilih Project' }}
                    </h2>
                    
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input.select
                                wire:model.live="projectId"
                            >
                                <option value="">Pilih Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Timeline Table -->
        <div class="bg-white rounded-xl shadow">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h2 class="text-lg font-medium">Project Timeline</h2>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Timeline view showing ticket duration from start to due date
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed">
                    <thead>
                        <tr>
                            <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200" style="width: 50%;">
                                Ticket
                            </th>
                            @foreach($this->getMonthHeaders() as $month)
                                <th class="px-2 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200" 
                                    style="width: {{ 50 / count($this->getMonthHeaders()) }}%;">
                                    {{ $month }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($this->getTimelineData()['tasks'] as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 border-r border-gray-200">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900">{{ $task['title'] }}</div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                            <span class="font-medium">{{ $task['ticket_id'] }}</span>
                                            <span class="text-gray-400">|</span>
                                            <span>Due: {{ $task['end_date'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                @foreach($this->getMonthHeaders() as $monthIndex => $monthLabel)
                                    <td class="p-0 h-14 relative border-r border-gray-200">
                                        @if(isset($task['bar_spans'][$monthIndex]))
                                            @php
                                                $span = $task['bar_spans'][$monthIndex];
                                                $left = $span['start_position'];
                                                $width = $span['width_percentage'];
                                                
                                                // Determine styling based on days remaining
                                                $backgroundColor = $task['color'];
                                                $borderStyle = '';
                                                $opacity = '1';
                                                
                                                if ($task['is_overdue']) {
                                                    $borderStyle = 'border: 2px dashed rgba(255,255,255,0.7);';
                                                    $opacity = '0.8';
                                                } elseif ($task['remaining_days'] <= 3) {
                                                    $borderStyle = 'border: 2px solid rgba(255,255,255,0.7);';
                                                }
                                                
                                                // Format days text for display
                                                $daysText = $task['is_overdue'] ? 'Overdue' : 
                                                    $task['remaining_days'] . 'd';
                                                    
                                                // Check if this is a month with enough width to show text
                                                $showText = $width >= 20;
                                            @endphp
                                            
                                            <div 
                                                class="absolute top-0 h-full flex items-center justify-center text-xs font-medium text-white rounded-sm overflow-hidden whitespace-nowrap group cursor-default"
                                                style="
                                                    background-color: {{ $backgroundColor }}; 
                                                    left: {{ $left }}%; 
                                                    width: {{ $width }}%;
                                                    {{ $borderStyle }}
                                                    opacity: {{ $opacity }};">
                                                
                                                <!-- Tooltip on hover -->
                                                <div class="absolute hidden group-hover:block bottom-full left-1/2 transform -translate-x-1/2 mb-1 px-2 py-1 bg-gray-900 text-white text-xs rounded whitespace-nowrap z-10">
                                                    <div>{{ $task['title'] }}</div>
                                                    <div>Due: {{ $task['end_date'] }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        
                        @if(count($this->getTimelineData()['tasks']) === 0)
                            <tr>
                                <td colspan="{{ count($this->getMonthHeaders()) + 1 }}" class="px-6 py-10 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>No tickets found with due dates</span>
                                        <span class="text-xs mt-1">Select a different project or add tickets with due dates</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>