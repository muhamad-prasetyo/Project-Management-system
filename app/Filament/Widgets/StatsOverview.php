<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;
    protected static ?string $pollingInterval = '30s';
    
    protected function getStats(): array
    {
        // Total Projects
        $totalProjects = Project::count();
        
        // Total Tickets
        $totalTickets = Ticket::count();
        
        // Tickets created in the last 7 days
        $newTicketsLastWeek = Ticket::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        
        // Users count
        $usersCount = User::count();
        
        // Tickets without assignee
        $unassignedTickets = Ticket::whereNull('user_id')->count();
            
        return [
            Stat::make('Total Projects', $totalProjects)
                ->description('Active projects in the system')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),
                
            Stat::make('Total Tickets', $totalTickets)
                ->description('Tickets across all projects')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),
                
                
            Stat::make('New Tickets This Week', $newTicketsLastWeek)
                ->description('Created in the last 7 days')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('info'),
                
                
            Stat::make('Unassigned Tickets', $unassignedTickets)
                ->description('Tickets without an assignee')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($unassignedTickets > 0 ? 'danger' : 'success'),
                
            Stat::make('Team Members', $usersCount)
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}