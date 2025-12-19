<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MaterialStatus: string implements HasLabel, HasColor
{
    case Scheduled = 'scheduled';
    case Arrived = 'arrived';
    case LabReceived = 'lab_received';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Arrived => 'Arrived',
            self::LabReceived => 'Lab Received',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Rejected => 'Rejected',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Scheduled => 'gray',
            self::Arrived => 'info',
            self::LabReceived => 'warning',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Rejected => 'danger',
        };
    }
}
