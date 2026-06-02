<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyNews;
use App\Models\ContactTicket;
use App\Models\Lawyer;
use App\Models\Position;
use App\Models\Ticket;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class GlobalSearchController extends Controller
{
    public function admin(Request $request): JsonResponse
    {
        $term = $this->term($request);

        if ($term === '') {
            return $this->response(collect());
        }

        $results = collect()
            ->merge($this->adminNavigationPages($term))
            ->merge($this->companies($term, 'admin.companies.show'))
            ->merge($this->lawyers($term, 'admin.lawyers.show'))
            ->merge($this->workers($term, 'admin.workers.show'))
            ->merge($this->tickets($term, 'admin.tickets.show'))
            ->merge($this->companyNews($term, 'admin.company-news.show'))
            ->merge($this->positions($term, 'admin.positions.show'))
            ->merge($this->categories($term))
            ->merge($this->contactTickets($term))
            ->merge($this->adminDatabaseFallback($term))
            ->merge($this->adminTranslations($term));

        return $this->response($results);
    }

    public function lawyer(Request $request): JsonResponse
    {
        $term = $this->term($request);
        $lawyerId = (int) auth('lawyer')->id();

        if ($term === '' || ! $lawyerId) {
            return $this->response(collect());
        }

        $companyIds = DB::table('lawyers_categories')
            ->where('lawyer_id', $lawyerId)
            ->distinct()
            ->pluck('company_id');

        $results = collect()
            ->merge($this->companies($term, 'lawyer.companies.show', $companyIds))
            ->merge($this->workers($term, 'lawyer.workers.show', $companyIds))
            ->merge($this->tickets($term, 'lawyer.tickets.show', $companyIds, $lawyerId))
            ->merge($this->companyNews($term, null, $companyIds))
            ->merge($this->lawyerCategories($term, $lawyerId));

        return $this->response($results);
    }

    public function company(Request $request): JsonResponse
    {
        $term = $this->term($request);
        $companyId = (int) auth('company')->id();

        if ($term === '' || ! $companyId) {
            return $this->response(collect());
        }

        $companyIds = collect([$companyId]);

        $results = collect()
            ->merge($this->workers($term, 'company.workers.show', $companyIds))
            ->merge($this->assignedCompanyLawyers($term, $companyId))
            ->merge($this->companyNews($term, 'company.company-news.show', $companyIds))
            ->merge($this->positions($term, 'company.positions.show'))
            ->merge($this->companyCategories($term, $companyId));

        return $this->response($results);
    }

    private function companies(string $term, string $routeName, ?Collection $ids = null): Collection
    {
        if (! Route::has($routeName)) {
            return collect();
        }

        return Company::query()
            ->when($ids, fn ($query) => $query->whereIn('id', $ids))
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'companies', [
                    'company_name',
                    'email',
                    'phone',
                    'tax_number',
                    'address',
                ], $term);
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Company $company) => $this->item(
                'شركة',
                $company->company_name ?? ('#' . $company->id),
                $company->email ?? $company->phone ?? '-',
                route($routeName, $company->id),
                'company'
            ));
    }

    private function lawyers(string $term, string $routeName, ?Collection $ids = null): Collection
    {
        if (! Route::has($routeName)) {
            return collect();
        }

        return Lawyer::query()
            ->when($ids, fn ($query) => $query->whereIn('id', $ids))
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'lawyers', [
                    'name',
                    'email',
                    'phone',
                ], $term);
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Lawyer $lawyer) => $this->item(
                'محامي',
                $lawyer->name ?? ('#' . $lawyer->id),
                $lawyer->email ?? $lawyer->phone ?? '-',
                route($routeName, $lawyer->id),
                'lawyer'
            ));
    }

    private function assignedCompanyLawyers(string $term, int $companyId): Collection
    {
        $lawyerIds = DB::table('lawyers_categories')
            ->where('company_id', $companyId)
            ->distinct()
            ->pluck('lawyer_id');

        if ($lawyerIds->isEmpty()) {
            return collect();
        }

        return Lawyer::query()
            ->whereIn('id', $lawyerIds)
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'lawyers', [
                    'name',
                    'email',
                    'phone',
                ], $term);
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Lawyer $lawyer) => $this->item(
                'محامي مسؤول',
                $lawyer->name ?? ('#' . $lawyer->id),
                $lawyer->email ?? $lawyer->phone ?? '-',
                Route::has('company.lawyer.show') ? route('company.lawyer.show') : '#',
                'lawyer'
            ));
    }

    private function workers(string $term, string $routeName, ?Collection $companyIds = null): Collection
    {
        if (! Route::has($routeName)) {
            return collect();
        }

        return Worker::query()
            ->with('company:id,company_name')
            ->when($companyIds, fn ($query) => $query->whereIn('company_id', $companyIds))
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'workers', [
                    'name',
                    'email',
                    'phone',
                    'iqama_number',
                    'residency_number',
                ], $term);
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Worker $worker) => $this->item(
                'عامل',
                $worker->name ?? ('#' . $worker->id),
                trim(($worker->company?->company_name ?? '-') . ' - ' . ($worker->phone ?? '-')),
                route($routeName, $worker->id),
                'worker'
            ));
    }

    private function tickets(string $term, string $routeName, ?Collection $companyIds = null, ?int $lawyerId = null): Collection
    {
        if (! Route::has($routeName)) {
            return collect();
        }

        $ticketNumber = ltrim(preg_replace('/\D+/', '', $term) ?? '', '0');

        return Ticket::query()
            ->with(['worker:id,name', 'company:id,company_name'])
            ->when($companyIds, fn ($query) => $query->whereIn('company_id', $companyIds))
            ->when($lawyerId, fn ($query) => $query->where('lawyer_id', $lawyerId))
            ->where(function ($query) use ($term, $ticketNumber) {
                if ($ticketNumber !== '') {
                    $query->where('id', $ticketNumber);
                }

                $this->applyColumnSearch($query, 'tickets', [
                    'title',
                    'title_original',
                    'title_translated',
                    'last_message_preview',
                ], $term, $ticketNumber !== '');
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Ticket $ticket) => $this->item(
                'تذكرة',
                '#' . $ticket->id . ' - ' . ($ticket->title_translated ?: $ticket->title ?: $ticket->title_original ?: 'تذكرة'),
                trim(($ticket->worker?->name ?? '-') . ' - ' . ($ticket->company?->company_name ?? '-')),
                route($routeName, $ticket->id),
                'ticket'
            ));
    }

    private function companyNews(string $term, ?string $routeName = null, ?Collection $companyIds = null): Collection
    {
        if ($routeName && ! Route::has($routeName)) {
            return collect();
        }

        return CompanyNews::query()
            ->with('company:id,company_name')
            ->when($companyIds, fn ($query) => $query->whereIn('company_id', $companyIds))
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'company_news', [
                    'title',
                    'description',
                ], $term);
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (CompanyNews $news) => $this->item(
                'خبر شركة',
                $news->title ?? ('#' . $news->id),
                $news->company?->company_name ?? '-',
                $routeName ? route($routeName, $news->id) : (Route::has('lawyer.companies.show') && $news->company_id ? route('lawyer.companies.show', $news->company_id) : '#'),
                'news'
            ));
    }

    private function positions(string $term, string $routeName): Collection
    {
        if (! Route::has($routeName)) {
            return collect();
        }

        return Position::query()
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'positions', ['name'], $term);
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Position $position) => $this->item(
                'وظيفة',
                $position->name ?? ('#' . $position->id),
                $position->status ?? '-',
                route($routeName, $position->id),
                'position'
            ));
    }

    private function categories(string $term): Collection
    {
        return Category::query()
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'categories', ['name'], $term);
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Category $category) => $this->item(
                'نوع قضية',
                $category->name,
                $category->status ?? '-',
                Route::has('admin.categories.edit') ? route('admin.categories.edit', $category->id) : '#',
                'category'
            ));
    }

    private function lawyerCategories(string $term, int $lawyerId): Collection
    {
        return Category::query()
            ->join('lawyers_categories', 'categories.id', '=', 'lawyers_categories.category_id')
            ->where('lawyers_categories.lawyer_id', $lawyerId)
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'categories', ['categories.name'], $term);
            })
            ->select('categories.id', 'categories.name', 'categories.status')
            ->distinct()
            ->limit(5)
            ->get()
            ->map(fn (Category $category) => $this->item(
                'نوع قضية',
                $category->name,
                $category->status ?? '-',
                Route::has('lawyer.tickets.index') ? route('lawyer.tickets.index', ['category_id' => $category->id]) : '#',
                'category'
            ));
    }

    private function companyCategories(string $term, int $companyId): Collection
    {
        return Category::query()
            ->join('lawyers_categories', 'categories.id', '=', 'lawyers_categories.category_id')
            ->where('lawyers_categories.company_id', $companyId)
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'categories', ['categories.name'], $term);
            })
            ->select('categories.id', 'categories.name', 'categories.status')
            ->distinct()
            ->limit(5)
            ->get()
            ->map(fn (Category $category) => $this->item(
                'نوع قضية',
                $category->name,
                $category->status ?? '-',
                Route::has('company.lawyer.show') ? route('company.lawyer.show') : '#',
                'category'
            ));
    }

    private function contactTickets(string $term): Collection
    {
        if (! Route::has('admin.contact-tickets.show')) {
            return collect();
        }

        return ContactTicket::query()
            ->where(function ($query) use ($term) {
                $this->applyColumnSearch($query, 'contact_tickets', [
                    'name',
                    'email',
                    'phone',
                    'company',
                    'message',
                ], $term);
            })
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (ContactTicket $ticket) => $this->item(
                'رسالة تواصل',
                $ticket->name ?? ('#' . $ticket->id),
                $ticket->email ?? $ticket->phone ?? '-',
                route('admin.contact-tickets.show', $ticket->id),
                'contact'
            ));
    }

    private function adminNavigationPages(string $term): Collection
    {
        return collect([
            ['title' => 'لوحة التحكم', 'subtitle' => 'dashboard.sidebar.dashboard', 'route' => 'admin.dashboard', 'icon' => 'page'],
            ['title' => 'الشركات', 'subtitle' => 'companies.title', 'route' => 'admin.companies.index', 'icon' => 'company'],
            ['title' => 'إدارة الشركات', 'subtitle' => 'companies.page_title', 'route' => 'admin.companies.index', 'icon' => 'company'],
            ['title' => 'أخبار الشركات', 'subtitle' => 'company_news.title', 'route' => 'admin.company-news.index', 'icon' => 'news'],
            ['title' => 'أنواع القضايا', 'subtitle' => 'categories.title', 'route' => 'admin.categories.index', 'icon' => 'category'],
            ['title' => 'المحامين', 'subtitle' => 'lawyers.title', 'route' => 'admin.lawyers.index', 'icon' => 'lawyer'],
            ['title' => 'العمال', 'subtitle' => 'workers.title', 'route' => 'admin.workers.index', 'icon' => 'worker'],
            ['title' => 'الوظائف', 'subtitle' => 'positions.title', 'route' => 'admin.positions.index', 'icon' => 'position'],
            ['title' => 'التذاكر', 'subtitle' => 'tickets.title', 'route' => 'admin.tickets.index', 'icon' => 'ticket'],
            ['title' => 'رسائل التواصل', 'subtitle' => 'contact_tickets.title', 'route' => 'admin.contact-tickets.index', 'icon' => 'contact'],
            ['title' => 'التنبيهات', 'subtitle' => 'notifications.title', 'route' => 'admin.notifications.index', 'icon' => 'notification'],
            ['title' => 'الإشعارات', 'subtitle' => 'notifications.title', 'route' => 'admin.notifications.index', 'icon' => 'notification'],
            ['title' => 'سياسة الخصوصية', 'subtitle' => 'app_pages.privacy_policy', 'route' => 'admin.app-pages.privacy-policy', 'icon' => 'page'],
            ['title' => 'نبذة عن التطبيق', 'subtitle' => 'app_pages.about_app', 'route' => 'admin.app-pages.about-app', 'icon' => 'page'],
            ['title' => 'محتوى التطبيق', 'subtitle' => 'app_pages.title', 'route' => 'admin.app-pages.index', 'icon' => 'page'],
        ])
            ->filter(fn (array $page) => Route::has($page['route']))
            ->filter(function (array $page) use ($term) {
                return mb_stripos($page['title'], $term) !== false
                    || mb_stripos($page['subtitle'], $term) !== false;
            })
            ->map(fn (array $page) => $this->item(
                'صفحة',
                $page['title'],
                $page['subtitle'],
                route($page['route']),
                $page['icon']
            ));
    }

    private function adminDatabaseFallback(string $term): Collection
    {
        return collect($this->adminSearchableTables())
            ->flatMap(function (array $config, string $table) use ($term) {
                if (! Schema::hasTable($table)) {
                    return collect();
                }

                $columns = collect(Schema::getColumnListing($table))
                    ->reject(fn (string $column) => in_array($column, $this->ignoredSearchColumns(), true))
                    ->values();

                if ($columns->isEmpty()) {
                    return collect();
                }

                try {
                    $rows = DB::table($table)
                        ->where(function ($query) use ($columns, $term) {
                            foreach ($columns as $column) {
                                $query->orWhere($column, 'like', "%{$term}%");
                            }
                        })
                        ->latest($columns->contains('id') ? 'id' : $columns->first())
                        ->limit(2)
                        ->get();
                } catch (\Throwable $exception) {
                    return collect();
                }

                return $rows->map(function ($row) use ($table, $config, $columns, $term) {
                    $id = $row->id ?? null;
                    $url = $this->adminUrlForTable($table, $id);

                    return $this->item(
                        $config['label'],
                        $this->rowTitle($row, $config['title'], $id),
                        $this->matchingColumnPreview($row, $columns, $term),
                        $url,
                        $config['icon']
                    );
                });
            });
    }

    private function adminTranslations(string $term): Collection
    {
        $directories = [
            resource_path('lang'),
            base_path('lang'),
        ];

        return collect($directories)
            ->filter(fn (string $directory) => File::isDirectory($directory))
            ->flatMap(fn (string $directory) => collect(File::allFiles($directory)))
            ->filter(fn ($file) => $file->getExtension() === 'php')
            ->flatMap(function ($file) use ($term) {
                $translations = require $file->getPathname();

                if (! is_array($translations)) {
                    return collect();
                }

                $locale = basename(dirname($file->getPathname()));
                $group = $file->getBasename('.php');

                return $this->flattenTranslations($translations)
                    ->filter(fn ($value) => mb_stripos((string) $value, $term) !== false)
                    ->take(3)
                    ->map(fn ($value, $key) => $this->item(
                        'نص ترجمة',
                        (string) $value,
                        "{$locale}.{$group}.{$key}",
                        $this->translationUrl($group, $key),
                        'translation'
                    ));
            });
    }

    private function adminSearchableTables(): array
    {
        return [
            'admins' => ['label' => 'أدمن', 'title' => ['name', 'email'], 'icon' => 'admin'],
            'companies' => ['label' => 'شركة', 'title' => ['company_name', 'email'], 'icon' => 'company'],
            'lawyers' => ['label' => 'محامي', 'title' => ['name', 'email'], 'icon' => 'lawyer'],
            'workers' => ['label' => 'عامل', 'title' => ['name', 'email', 'phone'], 'icon' => 'worker'],
            'tickets' => ['label' => 'تذكرة', 'title' => ['title_translated', 'title', 'title_original'], 'icon' => 'ticket'],
            'ticket_messages' => ['label' => 'رسالة تذكرة', 'title' => ['message_translated', 'message_original', 'message'], 'icon' => 'ticket'],
            'categories' => ['label' => 'نوع قضية', 'title' => ['name'], 'icon' => 'category'],
            'positions' => ['label' => 'وظيفة', 'title' => ['name'], 'icon' => 'position'],
            'company_news' => ['label' => 'خبر شركة', 'title' => ['title', 'description'], 'icon' => 'news'],
            'app_pages' => ['label' => 'محتوى التطبيق', 'title' => ['title', 'type', 'content'], 'icon' => 'page'],
            'contact_tickets' => ['label' => 'رسالة تواصل', 'title' => ['name', 'email', 'message'], 'icon' => 'contact'],
            'notifications' => ['label' => 'إشعار', 'title' => ['title', 'body', 'message'], 'icon' => 'notification'],
            'nationalities' => ['label' => 'جنسية', 'title' => ['name'], 'icon' => 'nationality'],
            'prefered_languages' => ['label' => 'لغة مفضلة', 'title' => ['prefered_language', 'code'], 'icon' => 'language'],
        ];
    }

    private function ignoredSearchColumns(): array
    {
        return [
            'password',
            'remember_token',
            'api_token',
            'token',
            'fcm_token',
            'mail_password',
        ];
    }

    private function adminUrlForTable(string $table, mixed $id): string
    {
        $id = $id ? (int) $id : null;

        $routes = [
            'companies' => ['admin.companies.show', $id],
            'lawyers' => ['admin.lawyers.show', $id],
            'workers' => ['admin.workers.show', $id],
            'tickets' => ['admin.tickets.show', $id],
            'ticket_messages' => ['admin.tickets.show', $this->ticketIdFromMessage($id)],
            'categories' => ['admin.categories.edit', $id],
            'positions' => ['admin.positions.show', $id],
            'company_news' => ['admin.company-news.show', $id],
            'contact_tickets' => ['admin.contact-tickets.show', $id],
            'notifications' => ['admin.notifications.index', null],
            'app_pages' => ['admin.app-pages.index', null],
            'admins' => ['admin.profile.show', null],
        ];

        [$routeName, $parameter] = $routes[$table] ?? ['admin.dashboard', null];

        if (! Route::has($routeName)) {
            return Route::has('admin.dashboard') ? route('admin.dashboard') : '#';
        }

        if (! $parameter && (str_ends_with($routeName, '.show') || str_ends_with($routeName, '.edit'))) {
            $fallbackRoute = preg_replace('/\.(show|edit)$/', '.index', $routeName);

            return $fallbackRoute && Route::has($fallbackRoute)
                ? route($fallbackRoute)
                : (Route::has('admin.dashboard') ? route('admin.dashboard') : '#');
        }

        return $parameter ? route($routeName, $parameter) : route($routeName);
    }

    private function ticketIdFromMessage(?int $messageId): ?int
    {
        if (! $messageId || ! Schema::hasTable('ticket_messages')) {
            return null;
        }

        return DB::table('ticket_messages')->where('id', $messageId)->value('ticket_id');
    }

    private function rowTitle(object $row, array $preferredColumns, mixed $id): string
    {
        foreach ($preferredColumns as $column) {
            if (isset($row->{$column}) && trim((string) $row->{$column}) !== '') {
                return mb_substr((string) $row->{$column}, 0, 80);
            }
        }

        return $id ? '#' . $id : 'نتيجة بحث';
    }

    private function matchingColumnPreview(object $row, Collection $columns, string $term): string
    {
        foreach ($columns as $column) {
            $value = $row->{$column} ?? null;

            if ($value !== null && mb_stripos((string) $value, $term) !== false) {
                return $column . ': ' . mb_substr((string) $value, 0, 90);
            }
        }

        return 'نتيجة من قاعدة البيانات';
    }

    private function applyColumnSearch($query, string $table, array $columns, string $term, bool $startsWithOr = false): void
    {
        $columns = array_values(array_filter(
            $columns,
            fn (string $column) => Schema::hasColumn($table, str_contains($column, '.') ? str($column)->afterLast('.')->toString() : $column)
        ));

        if ($columns === []) {
            if (! $startsWithOr) {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        $needleGroups = $this->searchNeedleGroups($term);
        $method = $startsWithOr ? 'orWhere' : 'where';

        $query->{$method}(function ($outerQuery) use ($columns, $needleGroups) {
            foreach ($columns as $column) {
                $outerQuery->orWhere(function ($columnQuery) use ($column, $needleGroups) {
                    foreach ($needleGroups as $group) {
                        $columnQuery->where(function ($variantQuery) use ($column, $group) {
                            foreach ($group as $variant) {
                                $variantQuery->orWhere($column, 'like', "%{$variant}%");
                            }
                        });
                    }
                });
            }
        });
    }

    private function searchNeedleGroups(string $term): array
    {
        $normalized = $this->normalizeArabicSearch($term);
        $tokens = preg_split('/\s+/u', $term) ?: [];
        $normalizedTokens = preg_split('/\s+/u', $normalized) ?: [];

        return collect($tokens)
            ->merge($normalizedTokens)
            ->map(fn ($value) => $this->normalizeArabicSearch(trim((string) $value)))
            ->filter(fn (string $value) => mb_strlen($value) >= 2)
            ->unique()
            ->map(fn (string $value) => collect($this->arabicNeedleVariants($value))
                ->map(fn ($variant) => trim((string) $variant))
                ->filter()
                ->unique()
                ->values()
                ->all())
            ->unique()
            ->values()
            ->all();
    }

    private function arabicNeedleVariants(string $value): array
    {
        $variants = [$value];

        if (str_contains($value, 'ه')) {
            $variants[] = str_replace('ه', 'ة', $value);
        }

        if (str_contains($value, 'ة')) {
            $variants[] = str_replace('ة', 'ه', $value);
        }

        if (str_contains($value, 'ا')) {
            $variants[] = str_replace('ا', 'أ', $value);
            $variants[] = str_replace('ا', 'إ', $value);
            $variants[] = str_replace('ا', 'آ', $value);
        }

        if (str_starts_with($value, 'ا')) {
            $tail = mb_substr($value, 1);
            $variants[] = 'أ' . $tail;
            $variants[] = 'إ' . $tail;
            $variants[] = 'آ' . $tail;
        }

        return $variants;
    }

    private function normalizeArabicSearch(string $value): string
    {
        $value = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $value) ?? $value;

        return str_replace(
            ['أ', 'إ', 'آ', 'ٱ', 'ى', 'ة', 'ؤ', 'ئ'],
            ['ا', 'ا', 'ا', 'ا', 'ي', 'ه', 'و', 'ي'],
            $value
        );
    }

    private function flattenTranslations(array $translations, string $prefix = ''): Collection
    {
        return collect($translations)->flatMap(function ($value, string|int $key) use ($prefix) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                return $this->flattenTranslations($value, $fullKey);
            }

            return [$fullKey => (string) $value];
        });
    }

    private function translationUrl(string $group, string $key): string
    {
        $exactRoutes = [
            'companies' => 'admin.companies.index',
            'company_news' => 'admin.company-news.index',
            'lawyers' => 'admin.lawyers.index',
            'workers' => 'admin.workers.index',
            'tickets' => 'admin.tickets.index',
            'lawyer_tickets' => 'admin.tickets.index',
            'categories' => 'admin.categories.index',
            'positions' => 'admin.positions.index',
            'contact_tickets' => 'admin.contact-tickets.index',
            'notifications' => 'admin.notifications.index',
            'dashboard' => 'admin.dashboard',
        ];

        if ($group === 'app_pages') {
            if (str_contains($key, 'privacy')) {
                return Route::has('admin.app-pages.privacy-policy') ? route('admin.app-pages.privacy-policy') : route('admin.app-pages.index');
            }

            if (str_contains($key, 'about')) {
                return Route::has('admin.app-pages.about-app') ? route('admin.app-pages.about-app') : route('admin.app-pages.index');
            }
        }

        $routeName = $exactRoutes[$group] ?? 'admin.dashboard';

        return Route::has($routeName) ? route($routeName) : '#';
    }

    private function item(string $type, string $title, string $subtitle, string $url, string $icon): array
    {
        return compact('type', 'title', 'subtitle', 'url', 'icon');
    }

    private function term(Request $request): string
    {
        $term = (string) $request->query('q', '');
        $term = preg_replace('/[\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $term) ?? $term;

        return trim($term);
    }

    private function response(Collection $results): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                'results' => $results
                    ->filter(fn ($item) => ($item['url'] ?? '#') !== '#')
                    ->unique(fn ($item) => ($item['url'] ?? '#') . '|' . ($item['title'] ?? ''))
                    ->take(18)
                    ->values(),
            ],
        ]);
    }
}
