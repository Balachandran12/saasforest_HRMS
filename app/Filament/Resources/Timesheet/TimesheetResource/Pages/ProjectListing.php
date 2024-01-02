<?php

namespace App\Filament\Resources\Timesheet\TimesheetResource\Pages;

use App\Filament\Resources\Timesheet\TimesheetResource;
use Filament\Resources\Pages\Page;

class ProjectListing extends Page
{
    protected static string $resource = TimesheetResource::class;

    protected static string $view = 'filament.resources.timesheet.timesheet-resource.pages.project-listing';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }   
}
