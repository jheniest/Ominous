# Atrocidades (Ominous) - AI Coding Instructions

## Project Overview
Laravel 12 news/media platform (PHP 8.2) with invite-only registration, content moderation, and PayWall-protected sensitive media. Uses SQLite database and TailwindCSS frontend.

## Architecture

### Core Models & Relationships
- **Video** (news articles): Has slug-based URLs, `is_sensitive` for PayWall, token-based media streaming. Uses SoftDeletes.
- **User**: Invite-based registration via `Invite` → `InviteRedemption`. Has `is_admin`, `is_suspended`, `is_verified`.
- **SiteSetting**: Key-value config with caching (`SiteSetting::get()`, `SiteSetting::set()`). Used for `maintenance_mode`, `public_uploads_enabled`, `emergency_access_key`.
- **VideoMedia**: Separate media files per video, streamed via `SecureMediaController`.

### Route Structure
```
/news/*          → Public feed (NewsController) - PayWall only on media
/media/stream/*  → Token-protected streaming (SecureMediaController)
/dashboard       → Admin analytics (requires 'admin' middleware)
/admin/*         → Content moderation (requires 'admin' + 'check.suspended')
```

### Key Middleware (bootstrap/app.php)
- `UpdateLastSeen` + `CheckMaintenanceMode` → Global (appended)
- `admin` → AdminOnly alias
- `check.suspended` → CheckSuspended alias

### View Composers
`CategoryMenuComposer` provides `$categoryMenu` to all layouts. Categories: guerra, terrorismo, chacina, massacre, suicidio, tribunal-do-crime.

## Conventions

### Blade Views
- Use `@extends('layouts.app')` + `@section('content')` (NOT component slots)
- Add `@push('styles')` / `@push('scripts')` for page-specific assets
- Route names: `news.index`, `news.show`, `news.category`, `news.tag`, `news.search`

### Video/News URLs
- Always use slug: `route('news.show', $video->slug)` or `route('news.show', $video)` (model binding)
- Video model auto-generates unique slugs from title via `boot()` method
- Old `/videos/*` routes redirect 301 to `/news/*`

### Security Patterns
- Sensitive media requires: (1) auth check, (2) valid temporary token via `$video->generateMediaToken()`
- Rate limiting on media streaming: 60 req/min per IP
- Maintenance mode bypass: admins auto-bypass, emergency key for locked-out users

### Caching Strategy
- Use `Cache::remember()` with TTL: settings=3600s, category_stats=300s, news.featured=300s
- Clear specific cache on updates: `Cache::forget("setting_{$key}")`

## Development Commands
```bash
php artisan serve              # Start dev server
php artisan migrate            # Run migrations
php artisan view:clear         # Clear compiled views (required after layout changes)
php artisan route:clear        # Clear route cache
php artisan tinker             # REPL for testing models
```

## Common Patterns

### Adding New Routes
```php
// Public route
Route::get('/news/feature', [NewsController::class, 'feature'])->name('news.feature');

// Admin-only route
Route::middleware(['auth', 'admin', 'check.suspended'])->group(function () {
    Route::get('/admin/feature', [AdminController::class, 'index']);
});
```

### Activity Logging
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'action_name',
    'description' => 'Human readable description',
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
]);
```

### Site Settings Toggle
```php
SiteSetting::set('feature_enabled', true, 'boolean');
$enabled = SiteSetting::get('feature_enabled', false);
```

## File Locations
- Controllers: `app/Http/Controllers/` (Admin in `Admin/` subdirectory)
- Middleware: `app/Http/Middleware/`
- Models: `app/Models/`
- Views: `resources/views/` (news/, admin/, dashboard/, layouts/, components/)
- View Composers: `app/View/Composers/`

## Route Names (news.*)
All content routes use `news.*` namespace:
- `news.index` - Feed principal
- `news.show` - Visualizar notícia
- `news.create` - Formulário de envio (/submit)
- `news.store` - Processar envio
- `news.edit` - Formulário de edição
- `news.update` - Processar edição
- `news.destroy` - Deletar notícia
- `news.my-submissions` - Minhas publicações (/my-submissions)
- `news.comments.store` - Adicionar comentário
- `news.report` - Denunciar notícia
- `news.category` - Feed por categoria
- `news.tag` - Feed por tag
- `news.search` - Busca
