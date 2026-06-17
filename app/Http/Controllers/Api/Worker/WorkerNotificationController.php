<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\Ticket;
use App\Services\WorkerLocalizationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkerNotificationController extends Controller
{
    public function __construct(private WorkerLocalizationService $localization)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $worker = $request->user();
        $this->localization->setLocale($worker, $request);
        $perPage = min(max((int) $request->integer('per_page', 15), 1), 50);

        $query = Notifications::query()
            ->forRecipient($worker)
            ->with(['actor', 'entity'])
            ->latest();

        if ($request->filled('status')) {
            match ($request->string('status')->toString()) {
                'unread' => $query->unread(),
                'read' => $query->read(),
                default => null,
            };
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        $notifications = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => __('worker_api.notifications_fetched'),
            'data' => [
                'unread_count' => Notifications::query()->forRecipient($worker)->unread()->count(),
                'notifications' => collect($notifications->items())
                    ->map(fn (Notifications $notification) => $this->notificationPayload($notification))
                    ->values(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $worker = $request->user();
        $this->localization->setLocale($worker, $request);

        return response()->json([
            'status' => true,
            'message' => __('worker_api.unread_count_fetched'),
            'data' => [
                'unread_count' => Notifications::query()->forRecipient($worker)->unread()->count(),
            ],
        ]);
    }

    public function show(Request $request, string $notification): JsonResponse
    {
        $this->localization->setLocale($request->user(), $request);
        $notification = $this->findWorkerNotification($request, $notification);

        return response()->json([
            'status' => true,
            'message' => __('worker_api.notification_fetched'),
            'data' => [
                'notification' => $this->notificationPayload($notification),
            ],
        ]);
    }

    public function markAsRead(Request $request, string $notification): JsonResponse
    {
        $this->localization->setLocale($request->user(), $request);
        $notification = $this->findWorkerNotification($request, $notification);
        $notification->markAsRead();

        return response()->json([
            'status' => true,
            'message' => __('worker_api.notification_marked_read'),
            'data' => [
                'notification' => $this->notificationPayload($notification->fresh(['actor', 'entity'])),
            ],
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $worker = $request->user();
        $this->localization->setLocale($worker, $request);

        $updated = Notifications::query()
            ->forRecipient($worker)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'status' => true,
            'message' => __('worker_api.notifications_marked_read'),
            'data' => [
                'updated_count' => $updated,
                'unread_count' => 0,
            ],
        ]);
    }

    private function findWorkerNotification(Request $request, string $notification): Notifications
    {
        return Notifications::query()
            ->forRecipient($request->user())
            ->with(['actor', 'entity'])
            ->findOrFail($notification);
    }

    private function notificationPayload(Notifications $notification): array
    {
        $ticketId = data_get($notification->data, 'ticket_id');

        if (! $ticketId && $notification->entity_type === Ticket::class) {
            $ticketId = $notification->entity_id;
        }

        $worker = request()->user();
        $localized = $worker instanceof \App\Models\Worker
            ? $this->localization->notificationPayload($worker, $notification)
            : ['title' => $notification->title, 'body' => $notification->body];

        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $localized['title'],
            'body' => $localized['body'],
            'url' => $notification->url,
            'data' => $notification->data ?? [],
            'is_read' => $notification->read_at !== null,
            'read_at' => $notification->read_at?->toISOString(),
            'created_at' => $notification->created_at?->toISOString(),
            'created_at_human' => $notification->created_at?->diffForHumans(),
            'ticket_id' => $ticketId ? (int) $ticketId : null,
            'actor' => $this->modelPayload($notification->actor_type, $notification->actor_id, $notification->actor),
            'entity' => $this->modelPayload($notification->entity_type, $notification->entity_id, $notification->entity),
        ];
    }

    private function modelPayload(?string $type, mixed $id, ?Model $model): ?array
    {
        if (! $type && ! $id) {
            return null;
        }

        return [
            'type' => $type ? Str::snake(class_basename($type)) : null,
            'id' => $id,
            'name' => $this->modelName($model),
        ];
    }

    private function modelName(?Model $model): ?string
    {
        if (! $model) {
            return null;
        }

        return $model->getAttribute('name')
            ?? $model->getAttribute('company_name')
            ?? $model->getAttribute('title');
    }
}
