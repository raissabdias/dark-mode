<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColumnistResource\Pages;
use App\Filament\Resources\ColumnistResource\RelationManagers;
use App\Models\Columnist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;

class ColumnistResource extends Resource
{
    protected static ?string $model = Columnist::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Colunistas';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Perfil')
                    ->description('Configure os dados públicos do colunista.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome do Colunista')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($set, $state) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('URL Amigável (Slug)')
                            ->required()
                            ->unique(Columnist::class, 'slug', ignoreRecord: true),

                        Select::make('user_id')
                            ->label('Usuário Vinculado')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->helperText('Opcional. Vincule se este colunista tiver acesso ao painel.'),

                        Textarea::make('bio')
                            ->label('Biografia / Descrição')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Mídias e Status')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Foto de Perfil')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->avatar()
                            ->disk('supabase') 
                            ->directory('avatars')
                            ->visibility('public')
                            ->maxSize(1024),

                        Toggle::make('is_active')
                            ->label('Perfil Ativo')
                            ->default(true)
                            ->onColor('success'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Foto')
                    ->circular()
                    ->disk('supabase'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i'),
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
            'index' => Pages\ListColumnists::route('/'),
            'create' => Pages\CreateColumnist::route('/create'),
            'edit' => Pages\EditColumnist::route('/{record}/edit'),
        ];
    }
}
