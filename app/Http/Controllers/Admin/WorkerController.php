<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Nationality;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $query = Worker::query()
            ->with(['company', 'nationality', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('iqama_number', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('nationality', function ($nationalityQuery) use ($search) {
                        $nationalityQuery->where('nationality', 'like', "%{$search}%")
                            ->orWhere('preferred_language', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('nationality_id') && $request->nationality_id !== 'all') {
            $query->where('nationality_id', $request->nationality_id);
        }

        if ($request->filled('preferred_language') && $request->preferred_language !== 'all') {
            $query->whereHas('nationality', function ($nationalityQuery) use ($request) {
                $nationalityQuery->where('preferred_language', $request->preferred_language);
            });
        }

        $sort = $request->get('sort', 'id_asc');

        match ($sort) {
            'latest' => $query->orderByDesc('id'),
            'oldest', 'id_asc' => $query->orderBy('id', 'asc'),
            'name_asc' => $query->orderBy('name', 'asc')->orderBy('id', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc')->orderBy('id', 'asc'),
            default => $query->orderBy('id', 'asc'),
        };

        $workers = $query->paginate(10)->withQueryString();

        $topLanguage = Worker::query()
            ->join('nationalities', 'workers.nationality_id', '=', 'nationalities.id')
            ->selectRaw('nationalities.preferred_language, COUNT(*) as total')
            ->whereNotNull('nationalities.preferred_language')
            ->groupBy('nationalities.preferred_language')
            ->orderByDesc('total')
            ->first();

        $stats = [
            'total' => Worker::count(),
            'active' => Worker::where('status', 'active')->count(),
            'pending' => Worker::where('status', 'pending')->count(),
            'nationalities' => Worker::whereNotNull('nationality_id')
                ->distinct('nationality_id')
                ->count('nationality_id'),
            'top_language' => $topLanguage?->preferred_language ?? '-',
        ];

        $companies = Company::query()
            ->select('id', 'company_name')
            ->orderBy('company_name')
            ->get();

        $nationalities = Nationality::query()
            ->where('status', 'active')
            ->orderBy('nationality')
            ->get();

        $languages = Nationality::query()
            ->where('status', 'active')
            ->whereNotNull('preferred_language')
            ->distinct()
            ->orderBy('preferred_language')
            ->pluck('preferred_language');

        return view('admin.workers.index', compact(
            'workers',
            'stats',
            'companies',
            'nationalities',
            'languages'
        ));
    }

    public function create()
    {
        $companies = Company::query()
            ->select('id', 'company_name')
            ->orderBy('company_name')
            ->get();

        $nationalities = Nationality::query()
            ->where('status', 'active')
            ->orderBy('nationality')
            ->get();

        return view('admin.workers.create', compact('companies', 'nationalities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'nationality_id' => ['nullable', 'exists:nationalities,id'],

            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:workers,email'],
            'phone' => ['required', 'string', 'max:50', 'unique:workers,phone'],
            'password' => ['required', 'string', 'min:8'],
            'iqama_number' => ['nullable', 'string', 'max:100', 'unique:workers,iqama_number'],
            'position' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,pending,suspended'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('workers', 'public');
        }

        $data['password'] = Hash::make($data['password']);
        $data['created_by'] = auth('admin')->id();

        Worker::create($data);

        return redirect()
            ->route('admin.workers.index')
            ->with('toast_success', __('workers.messages.created'));
    }

    public function edit(Worker $worker)
    {
        $companies = Company::query()
            ->select('id', 'company_name')
            ->orderBy('company_name')
            ->get();

        $nationalities = Nationality::query()
            ->where('status', 'active')
            ->orderBy('nationality')
            ->get();

        return view('admin.workers.edit', compact('worker', 'companies', 'nationalities'));
    }

    public function update(Request $request, Worker $worker)
    {
        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'nationality_id' => ['nullable', 'exists:nationalities,id'],

            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:workers,email,' . $worker->id],
            'phone' => ['required', 'string', 'max:50', 'unique:workers,phone,' . $worker->id],
            'password' => ['nullable', 'string', 'min:8'],
            'iqama_number' => ['nullable', 'string', 'max:100', 'unique:workers,iqama_number,' . $worker->id],
            'position' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,pending,suspended'],
        ]);

        if ($request->hasFile('image')) {
            if ($worker->image) {
                Storage::disk('public')->delete($worker->image);
            }

            $data['image'] = $request->file('image')->store('workers', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $worker->update($data);

        return redirect()
            ->route('admin.workers.index')
            ->with('toast_success', __('workers.messages.updated'));
    }

    public function destroy(Worker $worker)
    {
        if ($worker->image) {
            Storage::disk('public')->delete($worker->image);
        }

        $worker->delete();

        return redirect()
            ->route('admin.workers.index')
            ->with('toast_error', __('workers.messages.deleted'));
    }
}
