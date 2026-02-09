<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationLabel = 'Comentários';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('news.title')
                    ->label('Notícia')
                    ->limit(30)
                    ->url(fn ($record): string => url('/noticia/' . $record->news->slug))
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Autor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Comentário')
                    ->limit(50),

                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Aprovado'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i'),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('Lido')
                    ->boolean()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('mark_read')
                    ->label(fn(Comment $record) => $record->is_read ? 'Marcar não lido' : 'Marcar lido')
                    ->icon(fn(Comment $record) => $record->is_read ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn(Comment $record) => $record->is_read ? 'gray' : 'success')
                    ->action(function (Comment $record) {
                        $record->update(['is_read' => !$record->is_read]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_read')
                        ->label('Marcar como Lidos')
                        ->icon('heroicon-o-eye')
                        ->action(function (Collection $records) {
                            $records->each->update(['is_read' => true]);
                        }),
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_read', false)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('is_read', false)->count() > 0 ? 'danger' : 'gray';
    }
}
