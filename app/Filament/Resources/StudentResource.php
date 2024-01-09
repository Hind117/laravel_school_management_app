<?php

namespace App\Filament\Resources;

use App\Events\PromoteStudent;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Standard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Filament\GlobalSearch\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use GrahamCampbell\ResultType\Success;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->numeric()
                    ,
                    Forms\Components\TextInput::make('phone_number')
                    ->required()
                    ->numeric(),
                    Forms\Components\TextInput::make('password')
                    ->required()->maxLength(255),
                    ]),
                    Forms\Components\Wizard\Step::make('Additional Information')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),


                    Forms\Components\Select::make('standard_id')
                            ->required()
                            ->relationship('standard', 'name'),
                            Forms\Components\Repeater::make('vitals')
                            ->schema([
                                Forms\Components\Select::make('name')->options(config('sm_config.vitals'))->required(),
                                Forms\Components\TextInput::make('value')->required(),
                            ]),

                            Forms\Components\Repeater::make('certificates')->relationship()
                            ->schema([
                                Forms\Components\Select::make('certificate_id')
                                ->options(config('sm_config.certificates')),
                                Forms\Components\TextInput::make('description'),
                            ])
                    ])
                    ])


                    ->skippable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('student_id')

                ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')

                ->searchable(),
                Tables\Columns\TextColumn::make('standard.name')->searchable(),


            ])
            ->filters([
                Tables\Filters\SelectFilter::make('All Standards')
                ->relationship('standard', 'name')


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('Promote')->action(function(Student $record){
                    event(new PromoteStudent($record));
                })->requiresConfirmation()->color('success'),
                Tables\Actions\Action::make('Demote')->action(function(Student $record){
                    if ($record -> standard_id>1){
                    $record -> standard_id = $record-> standard_id - 1;
                    $record->save();}
                })->requiresConfirmation()->color('danger'),])
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
            RelationManagers\GuardiansRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultDetails(EloquentModel $record): array
    {
        return [
            'Name' => $record->name,
            'Standard' => $record->standard->name,
        ];
    }

    public static function getGlobalSearchResultActions(EloquentModel $record): array
{
    return [
        Action::make('edit')
            ->url(static::getUrl('edit', ['record' => $record])),
    ];
}
}
