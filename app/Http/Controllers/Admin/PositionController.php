<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'id_asc');

        match ($sort) {
            'latest' => $query->orderByDesc('id'),
            'oldest', 'id_asc' => $query->orderBy('id', 'asc'),
            'name_asc' => $query->orderBy('name', 'asc')->orderBy('id', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc')->orderBy('id', 'asc'),
            default => $query->orderBy('id', 'asc'),
        };

        $positions = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Position::count(),
            'active' => Position::where('status', 'active')->count(),
            'inactive' => Position::where('status', 'inactive')->count(),
        ];

        return view('admin.positions.index', compact('positions', 'stats'));
    }

    public function create()
    {
        return view('admin.positions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('positions', 'name'),
            ],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            $position = Position::create($data);

            SystemNotifier::notifyPositionChange(
                position: $position,
                type: 'position_created',
                title: 'تم إضافة وظيفة جديدة',
                body: "تم إضافة وظيفة {$position->name}.",
                actor: auth('admin')->user(),
                data: ['position_id' => $position->id, 'action' => 'created']
            );

            if ($request->input('action') === 'save_and_show') {
                return redirect()
                    ->route('admin.positions.show', $position->id)
                    ->with('toast_success', __('positions.messages.created'));
            }

            return redirect()
                ->route('admin.positions.index')
                ->with('toast_success', __('positions.messages.created'));

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('positions.messages.create_failed'));
        }
    }

    public function show(Position $position)
    {
        $position->loadCount('workers');

        return view('admin.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        return view('admin.positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('positions', 'name')->ignore($position->id),
            ],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            $position->update($data);

            SystemNotifier::notifyPositionChange(
                position: $position,
                type: 'position_updated',
                title: 'تم تعديل وظيفة',
                body: "تم تعديل وظيفة {$position->name}.",
                actor: auth('admin')->user(),
                data: ['position_id' => $position->id, 'action' => 'updated']
            );

            if ($request->input('action') === 'save_and_show') {
                return redirect()
                    ->route('admin.positions.show', $position->id)
                    ->with('toast_success', __('positions.messages.updated'));
            }

            return redirect()
                ->route('admin.positions.index')
                ->with('toast_success', __('positions.messages.updated'));

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('toast_error', __('positions.messages.update_failed'));
        }
    }

    public function destroy(Position $position)
    {
        try {
            if ($position->workers()->exists()) {
                return back()
                    ->with('toast_error', __('positions.messages.delete_has_workers'));
            }

            $positionName = $position->name;

            SystemNotifier::notifyPositionChange(
                position: $position,
                type: 'position_deleted',
                title: 'تم حذف وظيفة',
                body: "تم حذف وظيفة {$positionName}.",
                actor: auth('admin')->user(),
                data: ['position_id' => $position->id, 'action' => 'deleted']
            );

            $position->delete();

            return redirect()
                ->route('admin.positions.index')
                ->with('toast_success', __('positions.messages.deleted'));

        } catch (Throwable $e) {
            report($e);

            return back()
                ->with('toast_error', __('positions.messages.delete_failed'));
        }
    }
}
