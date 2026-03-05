<?php

namespace App\Filament\Resources\AnggotaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SocialMediaAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'socialMediaAccounts';

    protected static ?string $title = 'Social Media';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('social_media_id')
                    ->relationship('socialMedia', 'platform_name')
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('platform_name')
                            ->required()
                            ->maxLength(50),
                    ]),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->url()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('socialMedia.platform_name')
                    ->label('Platform'),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->url()
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
