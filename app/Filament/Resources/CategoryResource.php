<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Markdown;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Shop';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                ->schema([
                    Section::make([
                        TextInput::make('name') 
                       ->required()
                       ->live(onBlur:true)
                       ->unique()
                       ->afterStateUpdated(function(string $operation , $state, Forms\Set $set){
                        if ($operation !== 'create'){
                            return;
                        }

                        $set('slug',Str::slug($state));
                       }),
                       TextInput::make('slug')
                       ->disabled()
                       ->dehydrated()
                       ->required()
                       ->unique(Product::class,'slug',ignoreRecord:true),

                       MarkdownEditor::make('description')
                       ->columnSpan('full'),
                    ])->columns(2)
                    ]),
                
                    Group::make()
                    ->schema([
                        Section::make('status')
                        ->schema([
                            Toggle::make('is_visible')
                            ->label('Visbility')
                            ->helperText('Enable r disable category visiblity')
                            ->default(true),

                            Select::make('parent_id')
                            ->relationship('parent','name')
                        ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->sortable()
                ->searchable(),

                TextColumn::make('parent.name')
                ->label('Parent')
                ->searchable()
                ->sortable(),

                IconColumn::make('is_visible')
                ->label('Visibility')
                ->boolean()
                ->sortable(),

                TextColumn::make('updated_at')
                ->date()
                ->label('Updated Date')
                ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            ProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
