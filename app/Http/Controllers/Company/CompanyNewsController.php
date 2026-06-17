<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyNews;
use App\Models\Worker;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyNewsController extends Controller
{
    public function index(Request $request)
    {
        $company = auth('company')->user();

        $query = CompanyNews::query()
            ->where('company_id', $company->id)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $news = $query->paginate(10)->withQueryString();

        return view('company.company-news.index', compact('news'));
    }

    public function create()
    {
        return view('company.company-news.create');
    }

    public function store(Request $request)
    {
        $company = auth('company')->user();
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('company-news', 'public');
        }

        $data['company_id'] = $company->id;
        $data['created_by_company_id'] = $company->id;

        $news = CompanyNews::create($data);
        $this->notifyWorkersAboutNews($news);

        return redirect()
            ->route('company.company-news.show', $news)
            ->with('toast_success', __('company_news.messages.created'));
    }

    public function show(CompanyNews $companyNews)
    {
        $this->authorizeCompanyNews($companyNews);

        return view('company.company-news.show', ['newsItem' => $companyNews]);
    }

    public function edit(CompanyNews $companyNews)
    {
        $this->authorizeCompanyNews($companyNews);

        return view('company.company-news.edit', ['newsItem' => $companyNews]);
    }

    public function update(Request $request, CompanyNews $companyNews)
    {
        $this->authorizeCompanyNews($companyNews);

        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($companyNews);
            $data['image'] = $request->file('image')->store('company-news', 'public');
        }

        $companyNews->update($data);

        return redirect()
            ->route('company.company-news.show', $companyNews)
            ->with('toast_success', __('company_news.messages.updated'));
    }

    public function destroy(CompanyNews $companyNews)
    {
        $this->authorizeCompanyNews($companyNews);
        $this->deleteImage($companyNews);
        $companyNews->delete();

        return redirect()
            ->route('company.company-news.index')
            ->with('toast_success', __('company_news.messages.deleted'));
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function authorizeCompanyNews(CompanyNews $companyNews): void
    {
        abort_if((int) $companyNews->company_id !== (int) auth('company')->id(), 403);
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
                actor: auth('company')->user(),
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
