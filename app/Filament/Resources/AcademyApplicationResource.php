<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademyApplicationResource\Pages;
use App\Models\AcademyApplication;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AcademyApplicationResource extends Resource
{
    protected static ?string $model = AcademyApplication::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Programs';
    }

    public static function getNavigationSort(): int
    {
        return 2; // Second in Programs
    }

    public static function getNavigationLabel(): string
    {
        return 'Academy Applications';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Application Status
                Forms\Components\Select::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'pending' => 'Pending',
                        'reviewing' => 'Reviewing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('submitted'),

                // Student Information
                Forms\Components\TextInput::make('student_first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('student_last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('student_date_of_birth')
                    ->required()
                    ->displayFormat('d/m/Y')
                    ->native(false),
                Forms\Components\TextInput::make('student_age')
                    ->numeric()
                    ->minValue(6)
                    ->maxValue(18),
                Forms\Components\Select::make('student_gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('student_school')
                    ->maxLength(255),
                Forms\Components\TextInput::make('student_grade')
                    ->maxLength(255)
                    ->label('Grade/Class'),

                // Parent/Guardian Information
                Forms\Components\TextInput::make('parent_first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('parent_last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('parent_email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('parent_phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('parent_relationship')
                    ->options([
                        'parent' => 'Parent',
                        'guardian' => 'Guardian',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('parent_address')
                    ->maxLength(500)
                    ->rows(2),

                // Emergency Contact
                Forms\Components\TextInput::make('emergency_contact_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('emergency_contact_phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('emergency_contact_relationship')
                    ->maxLength(255),

                // Additional Information
                Forms\Components\Textarea::make('medical_conditions')
                    ->maxLength(1000)
                    ->rows(3)
                    ->label('Medical Conditions / Allergies'),
                Forms\Components\Textarea::make('dietary_requirements')
                    ->maxLength(500)
                    ->rows(2),
                Forms\Components\Select::make('program_interest')
                    ->options([
                        'sports_camps' => 'Sports Camps',
                        'personal_growth' => 'Personal Growth',
                        'volunteer_trips' => 'Volunteer Trips',
                        'all' => 'All Programs',
                    ])
                    ->label('Program Interest'),
                Forms\Components\Textarea::make('previous_experience')
                    ->maxLength(2000)
                    ->rows(3),
                Forms\Components\Textarea::make('expectations')
                    ->maxLength(2000)
                    ->rows(3),

                // Agreements
                Forms\Components\Toggle::make('terms_agreed')
                    ->label('Terms and Conditions Agreed')
                    ->default(false),
                Forms\Components\Toggle::make('media_consent')
                    ->label('Media Consent')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('student_first_name')
                    ->label('Student First Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_last_name')
                    ->label('Student Last Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_age')
                    ->label('Age')
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent_email')
                    ->label('Parent Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent_phone')
                    ->label('Parent Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program_interest')
                    ->label('Program Interest')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : 'N/A')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'gray',
                        'pending' => 'warning',
                        'reviewing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Submitted'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'pending' => 'Pending',
                        'reviewing' => 'Reviewing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('program_interest')
                    ->options([
                        'sports_camps' => 'Sports Camps',
                        'personal_growth' => 'Personal Growth',
                        'volunteer_trips' => 'Volunteer Trips',
                        'all' => 'All Programs',
                    ])
                    ->label('Program Interest'),
                Tables\Filters\SelectFilter::make('student_gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->label('Gender'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAcademyApplications::route('/'),
            'view' => Pages\ViewAcademyApplication::route('/{record}'),
            'edit' => Pages\EditAcademyApplication::route('/{record}/edit'),
        ];
    }
}

