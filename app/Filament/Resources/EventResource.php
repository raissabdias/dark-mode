<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?string $navigationLabel = 'Agenda';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados do Evento')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Nome do Evento / Banda')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('event_date')
                            ->label('Data e Hora')
                            ->required(),

                        Forms\Components\TextInput::make('location')
                            ->label('Local')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->required(),

                        Forms\Components\TextInput::make('ticket_url')
                            ->label('Link dos Ingressos')
                            ->url()
                            ->suffixIcon('heroicon-m-ticket'),
                    ])->columns(2),

                Forms\Components\Section::make('Flyer')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem do Evento')
                            ->image()
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('200')
                            ->imageResizeTargetHeight('200')
                            ->imageResizeUpscale(false)
                            ->disk('public') // Importante!
                            ->directory('events-images')
                            ->visibility('public')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Visível no site?')
                            ->default(true),
                    ])->columns(1),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Flyer'),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->icon('heroicon-o-map-pin'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->defaultSort('event_date', 'asc'); // Ordena do mais próximo para o mais distante
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
