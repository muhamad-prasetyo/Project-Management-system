<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterCreate(): void
    {
        $createDefaultStatuses = $this->data['create_default_statuses'] ?? true;
        
        if ($createDefaultStatuses) {
            $defaultStatuses = [
                ['name' => 'To Do', 'color' => '#F59E0B'],
                ['name' => 'In Progress', 'color' => '#3B82F6'],
                ['name' => 'Review', 'color' => '#8B5CF6'],
                ['name' => 'Done', 'color' => '#10B981'],
            ];

            foreach ($defaultStatuses as $status) {
                $this->record->ticketStatuses()->create($status);
            }
        }
    }
}