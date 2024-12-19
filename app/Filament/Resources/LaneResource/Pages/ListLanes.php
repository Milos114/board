<?php

namespace App\Filament\Resources\LaneResource\Pages;

use App\Filament\Resources\LaneResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLanes extends ListRecords
{
    protected static string $resource = LaneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
