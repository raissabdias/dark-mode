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

    protected static ?int $navigationSort = 1;

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

                        Forms\Components\Select::make('category_id')
                            ->label('Categoria')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome da Categoria')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->readOnly(),

                                Forms\Components\ColorPicker::make('text_color')
                                    ->label('Cor do Texto')
                                    ->default('#FFF'),
                                
                                Forms\Components\ColorPicker::make('bg_color')
                                    ->label('Cor do Fundo')
                                    ->default('#a855f7'),
                            ]),

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
                            ->fileAttachmentsDisk('supabase')
                            ->fileAttachmentsDirectory('news-content')
                            ->fileAttachmentsVisibility('public')
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
                    ->label('Capa')
                    ->disk('supabase')
                    ->visibility('public'),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
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
            ->defaultSort('created_at', 'desc')
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