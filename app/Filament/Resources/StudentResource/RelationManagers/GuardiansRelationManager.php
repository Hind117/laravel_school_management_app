<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Guardian;

class GuardiansRelationManager extends RelationManager
{
    protected static string $relationship = 'guardians';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('contact_number')
                    ->required()->numeric()
                    ->maxLength(255),
                    Forms\Components\Select::make('relation_type')
                    ->required()
                    ->options(Guardian::RELATION_TYPES)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([

                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('contact_number'),
                Tables\Columns\TextColumn::make('relation_type'),
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
