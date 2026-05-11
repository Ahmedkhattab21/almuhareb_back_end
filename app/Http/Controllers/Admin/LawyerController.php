<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class LawyerController extends Controller
{
    public function index(Request $request)
    {
        $query = Lawyer::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', $search);
                }

                $q->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        switch ($request->get('sort')) {
            case 'rating_desc':
                $query->orderByDesc('rating');
                break;

            case 'cases_desc':
                $query->orderByDesc('active_cases_count');
                break;

            case 'response_asc':
                $query->orderBy('avg_response_minutes', 'asc');
                break;

            case 'latest':
            default:
                $query->latest();
                break;
        }

        $lawyers = $query
            ->paginate(20)
            ->withQueryString();

        $avgResponseMinutes = Lawyer::avg('avg_response_minutes');
        $avgRating = Lawyer::avg('rating');

        $stats = [
            'total' => Lawyer::count(),
            'active' => Lawyer::where('status', 'active')->count(),
            'response' => $avgResponseMinutes ? round($avgResponseMinutes / 60, 1) : 0,
            'avg_rating' => $avgRating ? round($avgRating, 1) : 0,
        ];

        $specializations = Lawyer::query()
            ->whereNotNull('specialization')
            ->where('specialization', '!=', '')
            ->distinct()
            ->orderBy('specialization')
            ->pluck('specialization');

        return view('admin.lawyers.index', compact('lawyers', 'stats', 'specializations'));
    }

    public function create()
    {
        return view('admin.lawyers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateLawyer($request);

        $data['password'] = Hash::make($data['password']);
        $data['admin_id'] = auth('admin')->id();
        $data['created_by'] = auth('admin')->id();
        $data['preferred_language'] = $data['preferred_language'] ?? 'ar';

        Lawyer::create($data);

        return redirect()
            ->route('admin.lawyers.index')
            ->with('toast_success', __('lawyers.messages.created'));
    }

    public function edit(Lawyer $lawyer)
    {
        return view('admin.lawyers.edit', compact('lawyer'));
    }

    public function update(Request $request, Lawyer $lawyer)
    {
        $data = $this->validateLawyer($request, $lawyer);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $lawyer->update($data);

        return redirect()
            ->route('admin.lawyers.index')
            ->with('toast_success', __('lawyers.messages.updated'));
    }

    public function destroy(Lawyer $lawyer)
    {
        $lawyer->delete();

        return redirect()
            ->route('admin.lawyers.index')
            ->with('toast_error', __('lawyers.messages.deleted'));
    }

    private function validateLawyer(Request $request, ?Lawyer $lawyer = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('lawyers', 'email')->ignore($lawyer?->id),
            ],

            'phone' => ['nullable', 'string', 'max:30'],

            'license_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('lawyers', 'license_number')->ignore($lawyer?->id),
            ],

            'specialization' => ['nullable', 'string', 'max:255'],

            'preferred_language' => ['nullable', 'string', 'max:10'],

            'status' => [
                'required',
                Rule::in(['active', 'pending', 'suspended']),
            ],

            'password' => [
                $lawyer ? 'nullable' : 'required',
                'string',
                'min:8',
            ],

            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],

            'avg_response_minutes' => ['nullable', 'integer', 'min:0'],

            'active_cases_count' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
