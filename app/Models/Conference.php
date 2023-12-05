<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm() {
        return [
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Conference Details')
                        ->schema([
                            TextInput::make('name')
                                ->columnSpanFull()
                                ->label('Conference Name')
                                ->default('My conference')
                                ->required()
                                ->maxLength(60),
                            MarkdownEditor::make('description')
                                ->columnSpanFull()
                                ->required(),
                            DatePicker::make('start_date')
                                ->native(false)
                                ->required(),
                            DateTimePicker::make('end_date')
                                ->native(false)
                                ->required(),
                            Fieldset::make('Status')
                                ->columns(1)
                                ->schema([
                                    Toggle::make('is_published')
                                        ->default( true),
                                    Select::make('status')
                                        ->options([
                                            'draft' => 'Draft',
                                            'published' => 'Published',
                                            'archived' => 'Archived'
                                        ])
                                        ->required(),
                                ]),
                            Fieldset::make('Speakers')
                                ->schema([
                                    CheckboxList::make('speakers')
                                        ->columnSpanFull()
                                        ->relationship('speakers', 'name')
                                        ->options(
                                            Speaker::all()->pluck('name', 'id')
                                        )
                                        ->required(),
                                ]),

                        ]),
                    Tab::make("Location")
                        ->schema([
                            Select::make('region')
                                ->live()
                                ->required()
                                ->enum(Region::class)
                                ->options(Region::class),
                            Select::make('venue_id')
                                ->searchable()
                                ->preload()
                                ->createOptionForm(Venue::getForm())
                                ->editOptionForm(Venue::getForm())
                                ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                                    return $query->where('region', $get('region'));
                                }),
                        ]),

                ]),
                Actions::make( [
                    Action::make('star')
                        ->label('Fill with factory data')
                        ->icon('heroicon-m-star')
                        ->action(function ($livewire) {
                            $data = Conference::factory()->make()->toArray();
                            unset($data['venue_id']);
                            $livewire->form->fill($data);
                        })
                ])
            //Section::make('Conference Details')
                //->collapsible()
                //->description('Provide basic information about a conference.')
                //->icon('heroicon-o-information-circle' )
                //->columns(2)
                //,//]),
            //Section::make('Location')
                //-//>columns(2)
                //->schema([

                //]),
        ];
    }
}
