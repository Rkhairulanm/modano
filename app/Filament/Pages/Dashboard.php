<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    //     public function filtersForm(Form $form): Form
    // {
    //     return $form->schema([
    //         TextInput::make('name'),
    //         DatePicker::make('name'),
    //     ]);
    // }
}
