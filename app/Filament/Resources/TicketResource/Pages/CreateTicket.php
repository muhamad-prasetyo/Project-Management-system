<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }
        
        return $data;
    }
    
    protected function handleRecordCreation(array $data): Model
    {
        if (!empty($data['user_id']) && !empty($data['project_id'])) {
            $project = Project::find($data['project_id']);
            $isMember = $project?->members()->where('users.id', $data['user_id'])->exists();
            
            if (!$isMember) {
                $data['user_id'] = null;
                
                $this->notify('warning', 'Selected assignee is not a member of this project. Assignee has been reset.');
            }
        }
        
        return parent::handleRecordCreation($data);
    }
    
    protected function getRedirectUrl(): string
    {
        $referer = request()->header('referer');
        
        if ($referer && str_contains($referer, 'project-board-page')) {
            return '/admin/project-board-page';
        }
        return $this->getResource()::getUrl('index');
    }
}