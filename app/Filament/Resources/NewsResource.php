<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    
    protected static ?string $navigationLabel = 'Notícias';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Card Principal (Lado Esquerdo)
                Forms\Components\Section::make('Detalhes da Notícia')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('category')
                            ->label('Categoria')
                            ->options([
                                'Dark Metal' => 'Dark Metal',
                                'Synth Wave' => 'Synth Wave',
                                'Dark' => 'Dark',
                                'Post-Punk' => 'Post-Punk',
                                'Gothic Rock' => 'Gothic Rock',
                                'Industrial' => 'Industrial',
                                'New Wave' => 'New Wave',
                                'Doom Metal' => 'Doom Metal',
                                'Diversos' => 'Diversos',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('author')
                            ->label('Autor')
                            ->default('Raissa')
                            ->required(),
                            
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Resumo')
                            ->rows(3)
                            ->columnSpanFull()
                            ->required(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Conteúdo Completo')
                            ->columnSpanFull()
                            ->required(),
                    ])->columns(2),

                // Card Lateral (Imagem e Status)
                Forms\Components\Section::make('Mídia e Publicação')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem de Capa')
                            ->image()
                            ->disk('supabase')
                            ->directory('news-images')
                            ->visibility('public'),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Data de Publicação')
                            ->default(now()),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo?')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                                ->label('Destaque no Carrossel?')
                                ->onColor('warning')
                                ->default(false),
                    ])->columns(1),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Capa'),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge() // Deixa bonitinho com cor de fundo
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Carrossel')
                    ->boolean()
                    ->trueIcon('heroicon-o-star') // Ícone de estrelinha
                    ->falseIcon('heroicon-o-minus')
                    ->color(fn (string $state): string => $state ? 'warning' : 'gray'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}