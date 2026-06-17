<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyNews;
use App\Models\Worker;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyNewsController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyNews::query()
            ->with('company:id,company_name,email')
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }

        $news = $query->paginate(10)->withQueryString();
        $companies = Company::query()->orderBy('company_name')->get(['id', 'company_name']);

        return view('admin.company-news.index', compact('news', 'companies'));
    }

    public function create()
    {
        $companies = Company::query()->orderBy('company_name')->get(['id', 'company_name']);

        return view('admin.company-news.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('company-news', 'public');
        }

        $data['created_by_admin_id'] = auth('admin')->id();

        $news = CompanyNews::create($data);
        $this->notifyWorkersAboutNews($news);

        return redirect()
            ->route('admin.company-news.show', $news)
            ->with('toast_success', __('company_news.messages.created'));
    }

    public function show(CompanyNews $companyNews)
    {
        $companyNews->load(['company:id,company_name,email', 'adminCreator:id,name,email', 'companyCreator:id,company_name,email']);

        return view('admin.company-news.show', ['newsItem' => $companyNews]);
    }

    public function edit(CompanyNews $companyNews)
    {
        $companies = Company::query()->orderBy('company_name')->get(['id', 'company_name']);

        return view('admin.company-news.edit', [
            'newsItem' => $companyNews,
            'companies' => $companies,
        ]);
    }

    public function update(Request $request, CompanyNews $companyNews)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($companyNews);
            $data['image'] = $request->file('image')->store('company-news', 'public');
        }

        $companyNews->update($data);

        return redirect()
            ->route('admin.company-news.show', $companyNews)
            ->with('toast_success', __('company_news.messages.updated'));
    }

    public function destroy(CompanyNews $companyNews)
    {
        $this->deleteImage($companyNews);
        $companyNews->delete();

        return redirect()
            ->route('admin.company-news.index')
            ->with('toast_success', __('company_news.messages.deleted'));
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function deleteImage(CompanyNews $companyNews): void
    {
        if ($companyNews->image) {
            Storage::disk('public')->delete($companyNews->image);
        }
    }

    private function notifyWorkersAboutNews(CompanyNews $news): void
    {
        $workers = Worker::query()
            ->where('company_id', $news->company_id)
            ->where('status', 'active')
            ->get();

        foreach ($workers as $worker) {
            SystemNotifier::sendTo(
                recipient: $worker,
                type: 'company_news_created',
                title: 'خبر جديد من الشركة',
                body: $news->title,
                actor: auth('admin')->user(),
                entity: $news,
                data: [
                    'news_id' => $news->id,
                    'company_id' => $news->company_id,
                    'news_title' => $news->title,
                    'action' => 'created',
                ]
            );
        }
    }
}
