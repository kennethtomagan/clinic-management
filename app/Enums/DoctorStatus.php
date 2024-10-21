<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DoctorStatus: string implements HasColor, HasIcon, HasLabel
{
    case Active = 'active';

    case Leave = 'leave';

    case Resigned = 'resigned';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Leave => 'Leave',
            self::Resigned => 'Resigned',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Active => 'success',
            self::Leave => 'warning',
            self::Resigned => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Active => 'heroicon-m-check-badge',
            self::Leave => 'heroicon-m-sparkles',
            self::Resigned => 'heroicon-m-x-circle',
        };
    }
}

