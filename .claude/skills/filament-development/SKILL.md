---
name: filament-development
description: "Develops admin and member panels with Filament 5. Activates when creating resources, pages, widgets, tables, forms, actions, or navigation; working with Filament Shield permissions; building RelationManagers; or when the user mentions Filament, resource, panel, admin panel, or member panel."
license: MIT
metadata:
  author: movimentocasa
---

# Filament 5 Development

## When to Apply

Activate this skill when:

- Creating or modifying Filament resources (CRUD)
- Building tables, forms, or infolists
- Creating custom pages or widgets
- Working with Filament Shield (permissions)
- Configuring navigation, groups, or menus
- Building RelationManagers for nested resources
- Creating dashboard widgets (stats, calendar, charts)

## Project Panels

This project has two Filament panels:

### Admin Panel (`/admin`)

- Path: `app/Filament/Admin/`
- Purpose: Staff/coordinator management
- Access: Controlled by roles via Shield

### Member Panel (`/member`)

- Path: `app/Filament/Member/`
- Purpose: Public-facing member interface
- Features: Profile, Formations, Certificates, Events, Notices, Contributions
- Auth: Custom login, register, email verification pages

## Resource Structure

### Creating Resources

```bash
php artisan make:filament-resource ModelName --panel=admin
php artisan make:filament-resource ModelName --panel=member
```

### Resource Pattern Used in This Project

Resources in this project follow this pattern:

```php
namespace App\Filament\Member\Resources\Formations;

use Filament\Resources\Resource;

class FormationResource extends Resource
{
    protected static ?string $model = Formation::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Group Name';
    protected static ?string $navigationLabel = 'Label em PT-BR';
    protected static ?string $modelLabel = 'Label Singular';
    protected static ?string $pluralModelLabel = 'Label Plural';

    public static function form(Form $form): Form
    {
        return $form->schema([...]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([...])->filters([...])->actions([...]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormations::route('/'),
            'create' => Pages\CreateFormation::route('/create'),
            'edit' => Pages\EditFormation::route('/{record}/edit'),
            'view' => Pages\ViewFormation::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Always scope queries for member panel
        return parent::getEloquentQuery()
            ->where('status', FormationStatus::Published);
    }
}
```

### Table/Form Separation

For complex resources, extract table and form configuration into dedicated classes:

```php
class FormationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([...])
            ->filters([...])
            ->actions([...]);
    }
}
```

## Forms

### Common Form Components

```php
use Filament\Forms\Components\{TextInput, Select, Textarea, Toggle, DatePicker, FileUpload, RichEditor};

TextInput::make('title')->required()->maxLength(255),
Select::make('status')->options(FormationStatus::class)->required(),
Textarea::make('description')->rows(3),
Toggle::make('is_active')->default(true),
DatePicker::make('published_at')->native(false),
FileUpload::make('cover_image_path')->image()->directory('covers'),
RichEditor::make('full_description')->columnSpanFull(),
```

### Using Enums in Filament

This project uses PHP Enums extensively. Filament auto-resolves enums with `label()`:

```php
Select::make('status')
    ->options(FormationStatus::class)  // Uses label() method
    ->required(),

TextColumn::make('status')
    ->badge()
    ->color(fn (FormationStatus $state) => $state->color())
    ->icon(fn (FormationStatus $state) => $state->icon()),
```

## Tables

### Common Table Columns

```php
use Filament\Tables\Columns\{TextColumn, IconColumn, BadgeColumn, ImageColumn};

TextColumn::make('title')->searchable()->sortable(),
TextColumn::make('status')->badge(),
TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable(),
IconColumn::make('is_active')->boolean(),
ImageColumn::make('cover_image_path')->circular(),
TextColumn::make('ministry.name')->label('Ministerio'),
```

## Widgets

### Dashboard Widgets

```php
use Filament\Widgets\StatsOverviewWidget;

class MemberJourneyOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Formacoes', $count)->description('Concluidas'),
        ];
    }
}
```

### Calendar Widget (Guava Calendar)

```php
use Guava\Calendar\Widgets\CalendarWidget;

class MemberAgendaCalendarWidget extends CalendarWidget
{
    // Configure events, click handlers, views
}
```

## Shield Permissions

### Generating Permissions

```bash
php artisan shield:generate --all
```

### Role Names (from RoleName enum)

- `system_admin` — Full access
- `general_coordinator` — Manage all members and ministries
- `ministry_coordinator` — Manage specific ministry
- `financial_coordinator` — Manage finances
- `member` — Basic member access

## Navigation

### Icons

Use Heroicons (outline variant preferred): `heroicon-o-*`

### Groups

Navigation items are organized in groups:

- **Minha area** — Personal member resources (Profile, Certificates)
- **Formacao** — Learning resources (Formations)
- **Comunidade** — Community features (Events, Notices)
- **Financeiro** — Financial features (Contributions)

## Common Pitfalls

- Forgetting to scope `getEloquentQuery()` in member panel resources
- Not using `->native(false)` on date pickers (breaks with Livewire)
- Missing `cascadeOnDelete` in migrations for related resources
- Not running `shield:generate --all` after creating new resources
- Hardcoding labels instead of using Enum `label()` methods
- Forgetting to eager load relationships in `getEloquentQuery()`
