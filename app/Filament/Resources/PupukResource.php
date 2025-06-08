<?php

namespace App\Filament\Resources;

// DIUBAH: Gunakan Pages dari PupukResource
use App\Filament\Resources\PupukResource\Pages;
// DIUBAH: Gunakan Model Pupuk
use App\Models\Pupuk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

// DIUBAH: Nama Class
class PupukResource extends Resource
{
    // DIUBAH: Model yang digunakan
    protected static ?string $model = Pupuk::class;

    // DIUBAH: Ikon, label, dan grup navigasi
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $modelLabel = 'Pupuk';
    protected static ?string $pluralModelLabel = 'Daftar Pupuk';
    protected static ?string $navigationGroup = 'Manajemen Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // DIUBAH: Relasi ke KategoriPupuk
                Forms\Components\Select::make('kategori_pupuk_id')
                    ->relationship('kategoriPupuk', 'nama_kategori')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Kategori Pupuk'),

                // DIUBAH: Nama field
                Forms\Components\TextInput::make('nama_pupuk')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    // DIUBAH: Cek unique ke model Pupuk
                    ->unique(Pupuk::class, 'slug', ignoreRecord: true)
                    ->maxLength(170),

                Forms\Components\RichEditor::make('deskripsi')
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),

                Forms\Components\Select::make('status_ketersediaan')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Habis' => 'Habis',
                    ])
                    ->required()
                    ->default('Tersedia'),

                Forms\Components\FileUpload::make('gambar_utama')
                    ->label('Gambar Utama')
                    ->image()
                    ->disk('cloudinary')
                    // DIUBAH: Direktori upload di Cloudinary
                    ->directory('pupuk-images')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\ImageColumn::make('gambar_utama')
                    ->label('Gambar')
                    ->disk('cloudinary')
                    ->width(80)->height(60)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                // DIUBAH: Nama field dan relasi
                Tables\Columns\TextColumn::make('nama_pupuk')
                    ->searchable()->sortable(),

                // DIUBAH: Relasi ke kategoriPupuk
                Tables\Columns\TextColumn::make('kategoriPupuk.nama_kategori')
                    ->label('Kategori')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('harga')
                    ->money('IDR')->sortable(),

                Tables\Columns\TextColumn::make('stok')
                    ->numeric()->sortable(),

                Tables\Columns\TextColumn::make('status_ketersediaan')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Tersedia' => 'success',
                        'Habis' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                // DIUBAH: Relasi dan field untuk filter
                Tables\Filters\SelectFilter::make('kategori_pupuk_id')
                    ->relationship('kategoriPupuk', 'nama_kategori')
                    ->label('Filter Kategori'),

                Tables\Filters\SelectFilter::make('status_ketersediaan')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Habis' => 'Habis',
                    ])
                    ->label('Filter Status'),
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

    // DIUBAH: Eager loading menggunakan relasi 'kategoriPupuk'
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['kategoriPupuk']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        // DIUBAH: Arahkan ke class Page yang benar
        return [
            'index' => Pages\ListPupuk::route('/'),
            'create' => Pages\CreatePupuk::route('/create'),
            'edit' => Pages\EditPupuk::route('/{record}/edit'),
        ];
    }
}