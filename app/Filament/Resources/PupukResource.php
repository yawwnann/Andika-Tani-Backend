<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PupukResource\Pages;
use App\Models\Pupuk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PupukResource extends Resource
{
    protected static ?string $model = Pupuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $modelLabel = 'Pupuk';
    protected static ?string $pluralModelLabel = 'Daftar Pupuk';
    protected static ?string $navigationGroup = 'Manajemen Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Kolom kategori_pupuk_id: diizinkan nullable di form
                // PASTIKAN KOLOM INI JUGA NULLABLE DI DATABASE MIGRASI ANDA!
                Forms\Components\Select::make('kategori_pupuk_id')
                    ->relationship('kategoriPupuk', 'nama_kategori')
                    ->nullable() // Memungkinkan kolom ini kosong di form
                    ->searchable()
                    ->preload()
                    ->label('Kategori Pupuk'),

                // Kolom nama_pupuk: wajib diisi
                Forms\Components\TextInput::make('nama_pupuk')
                    ->required() // Wajib diisi
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->label('Nama Pupuk'), // Tambahkan label eksplisit

                // Kolom slug: wajib diisi dan unik
                Forms\Components\TextInput::make('slug')
                    ->required() // Wajib diisi
                    ->unique(Pupuk::class, 'slug', ignoreRecord: true) // Pastikan slug unik
                    ->maxLength(170)
                    ->label('Slug'), // Tambahkan label eksplisit

                // Kolom deskripsi: opsional (nullable)
                Forms\Components\RichEditor::make('deskripsi')
                    ->nullable() // Memungkinkan kosong
                    ->columnSpanFull()
                    ->label('Deskripsi'), // Tambahkan label eksplisit

                // Kolom harga: wajib diisi, numerik, dengan prefix Rp
                Forms\Components\TextInput::make('harga')
                    ->required() // Wajib diisi
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Harga'), // Tambahkan label eksplisit

                // Kolom stok: wajib diisi, numerik, min 0, default 0
                Forms\Components\TextInput::make('stok')
                    ->required() // Wajib diisi
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->label('Stok'), // Tambahkan label eksplisit

                // Kolom status_ketersediaan: wajib diisi dengan pilihan tertentu
                Forms\Components\Select::make('status_ketersediaan')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Habis' => 'Habis',
                    ])
                    ->required() // Wajib diisi
                    ->default('Tersedia')
                    ->label('Status Ketersediaan'), // Tambahkan label eksplisit

                // Kolom gambar_utama: opsional, gambar, diupload ke Cloudinary
                Forms\Components\FileUpload::make('gambar_utama')
                    ->label('Gambar Utama')
                    ->image()
                    ->disk('cloudinary')
                    ->directory('pupuk-images')
                    ->nullable() // Memungkinkan kosong
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

                Tables\Columns\TextColumn::make('nama_pupuk')
                    ->searchable()->sortable(),

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
                Tables\Actions\DeleteAction::make(), // Menggunakan Tables\Actions\DeleteAction
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

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
        return [
            'index' => Pages\ListPupuk::route('/'),
            'create' => Pages\CreatePupuk::route('/create'),
            'edit' => Pages\EditPupuk::route('/{record}/edit'),
        ];
    }
}