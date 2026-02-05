<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdResource\Pages;
use App\Models\Ad;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Anúncios';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes do Anúncio')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Cliente / Campanha')
                            ->required(),

                        Forms\Components\TextInput::make('link')
                            ->label('Link de Destino')
                            ->url()
                            ->required()
                            ->prefixIcon('heroicon-m-link'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Início do Contrato')
                            ->required(),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fim do Contrato')
                            ->required()
                            ->afterOrEqual('start_date'),
                    ])->columns(2),

                Forms\Components\Section::make('Banner')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem do Banner')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('150')
                            ->disk('supabase')
                            ->directory('ads-images')
                            ->visibility('public')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo?')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Banner'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('start_date')->date('d/m/Y')->label('Início'),
                Tables\Columns\TextColumn::make('end_date')->date('d/m/Y')->label('Fim'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ]);
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}
