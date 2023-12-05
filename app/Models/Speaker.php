<?php

namespace App\Models;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Speaker extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array'
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public static function getForm() {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            MarkdownEditor::make('bio')
                ->required()
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->required()
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->columnSpanFull()
                ->searchable()
                ->bulkToggleable()
                ->options([
                    'business-leader' => 'Business Leader',
                    'charisma' => 'Charismatic Speaker',
                    'humanitarian' => 'Humanitarian',
                    'open-source' => 'Open source creator',
                    'laracasts-contributer'=> 'Laracasts Contributor',
                    'youtube-influencer' => 'Youtube Influencer',
                    'twitter-influencer' => 'Twitter Influencer',
                    'lead-developer' => 'Lead Developer',
                    'unique-perspective' => 'Unique Perspective',
                    'ceo' => 'CEO',
                    'hometown-hero' => 'Hometown Hero',
                    'first-time' => 'First time speaker'
                ])
                ->descriptions([
                    'business-leader' => 'Cream of the crop',
                    'charisma' => 'Motivational',
                ])
                ->columns(3),
        ];
    }
}
