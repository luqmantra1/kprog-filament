<?php

namespace App\Filament\Resources;

use App\Enums\ProductTypeEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\onBlur;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Ternary;
use Symfony\Contracts\Service\Attribute\Required;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel ='Products';

    protected static ?string $navigationGroup ='Shop';

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 20;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
{
    return ['name','slug','description'];
}
   
public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Brand' => optional($record->brand)->name];
}

public static function getGlobalSearchEloquentQuery(): Builder
{
    return parent::getGlobalSearchEloquentQuery()->with(['brand']);
}
    


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                 ->schema([
                    Section::make()
                    ->schema([
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
                    ])->columns(2),

                    Section::make('Pricing & Inventory')
                    ->schema([
                       TextInput::make('sku')
                       ->label("SKU (Stock Keeping Unit)")
                       ->unique()
                       ->required(),
                       TextInput::make('price')
                       ->numeric()
                       ->rules(['regex: /^\d{1,6}(\.\d{0,2})?$/'])
                       ->required(),
                       TextInput::make('quantity')
                       ->numeric()
                       ->minValue(0)
                       ->maxValue(100),
                       Select::make('type')
                       ->options([
                        'downloadable'=> ProductTypeEnum::DOWNLOADABLE->value,
                        'deliverable'=> ProductTypeEnum::DELIVERABLE->value,
                       ])->required()
                    ])->columns(2),
                    ]),
                    

                Group::make()
                ->schema([
                    Section::make('Status')
                    ->schema([
                       Toggle::make('is_visible')
                       ->label('Visibility')
                       ->helperText('Enable or Disable product visibility')
                       ->default(true),
                       Toggle::make('is_featured')
                       ->label('Featured')
                       ->helperText('Enable or disable products featured status'),
                       DatePicker::make('published_at')
                       ->label('Availability')
                       ->default(now()),
                    ]),

                    Section::make('Image')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->directory('form-attachments') // Optional: Custom upload directory
                            ->preserveFilenames()
                            ->imageEditor()
                    ])->collapsible(),

                Section::make('Associations')
                    ->schema([
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload() // Preloads options for better UX
                            ->required(),

                            Select::make('catagories')
                            ->relationship('categories','name')
                            ->multiple()
                            ->required()
    ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name')
                ->searchable()
                ->sortable(),
                TextColumn::make('brand.name')
                ->searchable()
                ->sortable()
                ->toggleable(),
                IconColumn::make('is_visible')
                ->boolean()
                ->sortable()
                ->toggleable()
                ->label('Visibility'),
                TextColumn::make('price'),
                TextColumn::make('quantity'),
                TextColumn::make('published_at'),
                TextColumn::make('type'),
            ])
            ->filters([
                TernaryFilter::make('visiblity')
                ->label('Visibility')
                ->truelabel('Only Visible Products')
                ->falseLabel('Only Hidden Products')
                ->native(false),

                SelectFilter::make('brand')
                ->relationship('brand','name')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
