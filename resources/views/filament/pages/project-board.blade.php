<x-filament-panels::page>
    {{-- Project Selector --}}
    <div class="mb-6">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ $selectedProject ? $selectedProject->name : 'Pilih Project' }}
                </h2>
                
                <div>
                    <x-filament::input.wrapper>
                        <x-filament::input.select
                            wire:model.live="selectedProject"
                            wire:change="selectProject($event.target.value)"
                        >
                            <option value="">Pilih Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $selectedProject && $selectedProject->id == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>
    </div>

    @if($selectedProject)
        <div
            x-data="dragDropHandler()"
            x-init="init()"
            @ticket-moved.window="init()"
            @ticket-updated.window="init()"
            @refresh-board.window="init()"
            class="overflow-x-auto pb-6"
            id="board-container"
        >
            <div class="flex gap-4 min-w-full">
                @foreach ($ticketStatuses as $status)
                    <div 
                        class="status-column flex-1 min-w-[300px] rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50 dark:bg-gray-900"
                        data-status-id="{{ $status->id }}"
                    >
                        <div class="px-4 py-3 rounded-t-xl bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-medium text-gray-900 dark:text-white flex items-center justify-between">
                                <span>{{ $status->name }}</span>
                                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $status->tickets->count() }}</span>
                            </h3>
                        </div>
                        
                        <div class="p-3 flex flex-col gap-3 h-[calc(100vh-20rem)] overflow-y-auto">
                            @foreach ($status->tickets as $ticket)
                                <div 
                                    class="ticket-card bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 cursor-move"
                                    data-ticket-id="{{ $ticket->id }}"
                                >
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400 px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">
                                            {{ $ticket->uuid }}
                                        </span>
                                        @if ($ticket->due_date)
                                            <span class="text-xs px-1.5 py-0.5 rounded {{ $ticket->due_date->isPast() ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }}">
                                                {{ $ticket->due_date->format('M d') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $ticket->name }}</h4>
                                    
                                    @if ($ticket->description)
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 line-clamp-2">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($ticket->description), 100) }}
                                        </p>
                                    @endif
                                    
                                    <div class="flex justify-between items-center mt-2">
                                        @if ($ticket->assignee)
                                            <div class="inline-flex items-center px-2 py-1 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 gap-2">
                                                <span class="w-4 h-4 rounded-full bg-primary-500 flex items-center justify-center text-xs text-white mr-1.5">
                                                    {{ substr($ticket->assignee->name, 0, 1) }}
                                                </span>
                                                <span class="text-xs font-medium">{{ $ticket->assignee->name }}</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-gray-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-xs font-medium">Unassigned</span>
                                            </div>
                                        @endif
                                        
                                        <button
                                            type="button" 
                                            wire:click="showTicketDetails({{ $ticket->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                                        >
                                            <x-heroicon-m-eye class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if ($status->tickets->isEmpty())
                                <div class="flex items-center justify-center h-24 text-gray-500 dark:text-gray-400 text-sm italic border border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                                    No tickets
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                
                @if ($ticketStatuses->isEmpty())
                    <div class="w-full flex items-center justify-center h-40 text-gray-500 dark:text-gray-400">
                        No status columns found for this project
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="flex items-center justify-center h-40 text-gray-500 dark:text-gray-400">
            Please select a project to view the board
        </div>
    @endif
    
    {{-- Ticket Detail Modal --}}
    @if ($selectedTicket)
        <div
            x-data="{}"
            x-on:keydown.escape.window="$wire.closeTicketDetails()"
            class="fixed inset-0 z-40 flex items-center justify-center"
        >
            {{-- Modal Backdrop --}}
            <div
                class="fixed inset-0 bg-gray-950/50 dark:bg-gray-950/75"
                wire:click="closeTicketDetails"
            ></div>
            
            {{-- Modal Content --}}
            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 w-full max-w-2xl mx-auto max-h-[90vh] overflow-y-auto">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <span class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-mono rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            {{ $selectedTicket->uuid }}
                        </span>
                        {{ $selectedTicket->name }}
                    </h2>
                    
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="editTicket({{ $selectedTicket->id }})"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400"
                        >
                            <x-heroicon-m-pencil-square class="w-5 h-5" />
                        </button>
                        
                        <button
                            type="button"
                            wire:click="closeTicketDetails"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <x-heroicon-m-x-mark class="w-5 h-5" />
                        </button>
                    </div>
                </div>
                
                {{-- Modal Body --}}
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ match($selectedTicket->status->name) {
                                        'To Do' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'Review' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300',
                                        'Done' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    } }}"
                                >
                                    {{ $selectedTicket->status->name }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Assignee</h3>
                            <div class="mt-1">
                                @if ($selectedTicket->assignee)
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-xs font-medium text-primary-700 dark:text-primary-300 mr-2">
                                            {{ substr($selectedTicket->assignee->name, 0, 1) }}
                                        </span>
                                        <span class="text-gray-900 dark:text-white">{{ $selectedTicket->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Unassigned</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</h3>
                            <div class="mt-1 text-gray-900 dark:text-white">
                                @if ($selectedTicket->due_date)
                                    <span class="{{ $selectedTicket->due_date->isPast() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ $selectedTicket->due_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">No due date</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</h3>
                            <div class="mt-1 text-gray-900 dark:text-white">
                                {{ $selectedTicket->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                        @if ($selectedTicket->description)
                            <div class="prose dark:prose-invert max-w-none">
                                {!! $selectedTicket->description !!}
                            </div>
                        @else
                            <div class="text-gray-500 dark:text-gray-400 italic">No description provided</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Drag and Drop Handler Script --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dragDropHandler', () => ({
                draggingTicket: null,
                
                init() {
                    this.$nextTick(() => {
                        this.removeAllEventListeners();
                        this.attachAllEventListeners();
                    });
                },
                
                removeAllEventListeners() {
                    const tickets = document.querySelectorAll('.ticket-card');
                    tickets.forEach(ticket => {
                        ticket.removeAttribute('draggable');
                        const newTicket = ticket.cloneNode(true);
                        ticket.parentNode.replaceChild(newTicket, ticket);
                    });
                    
                    const columns = document.querySelectorAll('.status-column');
                    columns.forEach(column => {
                        const newColumn = column.cloneNode(false);
                        
                        while (column.firstChild) {
                            newColumn.appendChild(column.firstChild);
                        }
                        
                        if (column.parentNode) {
                            column.parentNode.replaceChild(newColumn, column);
                        }
                    });
                },
                
                attachAllEventListeners() {
                    const tickets = document.querySelectorAll('.ticket-card');
                    tickets.forEach(ticket => {
                        ticket.setAttribute('draggable', true);
                        
                        ticket.addEventListener('dragstart', (e) => {
                            this.draggingTicket = ticket.getAttribute('data-ticket-id');
                            ticket.classList.add('opacity-50');
                            e.dataTransfer.effectAllowed = 'move';
                        });
                        
                        ticket.addEventListener('dragend', () => {
                            ticket.classList.remove('opacity-50');
                            this.draggingTicket = null;
                        });
                        
                        const detailsButton = ticket.querySelector('button');
                        if (detailsButton) {
                            const ticketId = ticket.getAttribute('data-ticket-id');
                            detailsButton.addEventListener('click', () => {
                                const componentId = document.querySelector('[wire\\:id]').getAttribute('wire:id');
                                if (componentId) {
                                    Livewire.find(componentId).showTicketDetails(ticketId);
                                }
                            });
                        }
                    });
                    
                    const columns = document.querySelectorAll('.status-column');
                    columns.forEach(column => {
                        column.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            e.dataTransfer.dropEffect = 'move';
                            column.classList.add('bg-primary-50', 'dark:bg-primary-950');
                        });
                        
                        column.addEventListener('dragleave', () => {
                            column.classList.remove('bg-primary-50', 'dark:bg-primary-950');
                        });
                        
                        column.addEventListener('drop', (e) => {
                            e.preventDefault();
                            column.classList.remove('bg-primary-50', 'dark:bg-primary-950');
                            
                            if (this.draggingTicket) {
                                const statusId = column.getAttribute('data-status-id');
                                const ticketId = this.draggingTicket;
                                this.draggingTicket = null;
                                
                                const componentId = document.querySelector('[wire\\:id]').getAttribute('wire:id');
                                if (componentId) {
                                    Livewire.find(componentId).moveTicket(
                                        parseInt(ticketId), 
                                        parseInt(statusId)
                                    );
                                }
                            }
                        });
                    });
                }
            }));
        });
    </script>
</x-filament-panels::page>