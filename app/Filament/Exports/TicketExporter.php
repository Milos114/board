<?php

namespace App\Filament\Exports;

use App\Models\Ticket;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TicketExporter extends Exporter
{
    protected static ?string $model = Ticket::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.name')->label('User'),
            ExportColumn::make('assignedUser.name')->label('Assigned User'),
            ExportColumn::make('lane.name')->label('Lane'),
            ExportColumn::make('priority.name')->label('Priority'),
            ExportColumn::make('title')->label('Title'),
            ExportColumn::make('description')->label('Description'),
            ExportColumn::make('created_at')->label('Created At'),
            ExportColumn::make('updated_at')->label('Updated At'),
        ];
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Csv
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your ticket export has completed and ' . number_format($export->successful_rows) . ' ' . str(
                'row'
            )->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural(
                    $failedRowsCount
                ) . ' failed to export.';
        }

        return $body;
    }
}
