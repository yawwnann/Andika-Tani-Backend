<?php

namespace App\Filament\Resources;

// DIUBAH: Menggunakan Pages dari KategoriPupukResource
use App\Filament\Resources\KategoriPupukResource\Pages;
// DIUBAH: Menggunakan Model KategoriPupuk
use App\Models\KategoriPupuk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

// DIUBAH: Nama class
class KategoriPupukResource extends Resource
{
    // DIUBAH: Model yang digunakan
    protected static ?string $model = KategoriPupuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    // DIUBAH: Label di navigasi
    protected static ?string $modelLabel = 'Kategori Pupuk';
    protected static ?string $pluralModelLabel = 'Kategori Pupuk';
    protected static ?string $navigationGroup = 'Manajemen Produk'; // Grup di navigasi

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kategori')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true) // onBlur lebih efisien daripada debounce
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    // DIUBAH: Cek unique ke model KategoriPupuk
                    ->unique(KategoriPupuk::class, 'slug', ignoreRecord: true)
                    ->maxLength(120),

                Forms\Components\Textarea::make('deskripsi')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug'),

                // DIUBAH: Menghitung relasi 'pupuk'
                Tables\Columns\TextColumn::make('pupuk_count')
                    ->counts('pupuk')
                    ->label('Jumlah Produk')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        // DIUBAH: Mengarahkan ke class Page yang benar
        return [
            'index' => Pages\ListKategoriPupuk::route('/'),
            'create' => Pages\CreateKategoriPupuk::route('/create'),
            'edit' => Pages\EditKategoriPupuk::route('/{record}/edit'),
        ];
    }
}