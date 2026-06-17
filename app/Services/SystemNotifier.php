<?php

namespace App\Services;

use App\Mail\SystemNotificationMail;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Lawyer;
use App\Models\Notifications;
use App\Models\Position;
use App\Models\Recommendation;
use App\Models\Ticket;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class SystemNotifier
{
    public static function sendTo(
        Model $recipient,
        string $type,
        string $title,
        ?string $body = null,
        ?string $url = null,
        ?Model $actor = null,
        ?Model $entity = null,
        ?array $data = null
    ): Notifications {
        if ($recipient instanceof Worker) {
            $localized = app(WorkerLocalizationService::class)
                ->localizedNotificationForWorker($recipient, $type, $title, $body, $data);

            $data = array_merge($data ?? [], [
                'original_title' => $title,
                'original_body' => $body,
            ]);

            $title = $localized['title'];
            $body = $localized['body'];
        }

        $notification = Notifications::create([
            'recipient_type' => get_class($recipient),
            'recipient_id' => $recipient->getKey(),

            'actor_type' => $actor ? get_class($actor) : null,
            'actor_id' => $actor ? $actor->getKey() : null,

            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity ? $entity->getKey() : null,

            'type' => $type,
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'data' => $data,
        ]);

        self::sendNotificationEmail($recipient, $notification);
        self::sendPushNotification($recipient, $notification);

        return $notification;
    }

    public static function sendToMany(
        iterable $recipients,
        string $type,
        string $title,
        ?string $body = null,
        ?string $url = null,
        ?Model $actor = null,
        ?Model $entity = null,
        ?array $data = null
    ): void {
        collect($recipients)
            ->filter()
            ->unique(fn ($recipient) => get_class($recipient) . ':' . $recipient->getKey())
            ->each(function ($recipient) use ($type, $title, $body, $url, $actor, $entity, $data) {
                if (
                    $actor &&
                    get_class($recipient) === get_class($actor) &&
                    (int) $recipient->getKey() === (int) $actor->getKey()
                ) {
                    return;
                }

                self::sendTo(
                    recipient: $recipient,
                    type: $type,
                    title: $title,
                    body: $body,
                    url: $url,
                    actor: $actor,
                    entity: $entity,
                    data: $data
                );
            });
    }

    public static function admins(): Collection
    {
        return Admin::query()
            ->where('status', 'active')
            ->get();
    }

    public static function notifyLawyerChange(Lawyer $lawyer, string $type, string $title, string $body, ?Model $actor = null, ?array $data = null): void
    {
        $lawyer->loadMissing('companies');

        $recipients = self::admins()
            ->push($lawyer)
            ->merge($lawyer->companies)
            ->filter()
            ->unique(fn ($recipient) => get_class($recipient) . ':' . $recipient->getKey());

        foreach ($recipients as $recipient) {
            if (self::isSameModel($recipient, $actor)) {
                continue;
            }

            self::sendTo(
                recipient: $recipient,
                type: $type,
                title: $title,
                body: $body,
                url: self::lawyerUrlFor($recipient, $lawyer),
                actor: $actor,
                entity: $lawyer,
                data: $data
            );
        }
    }

    public static function notifyPositionChange(Position $position, string $type, string $title, string $body, ?Model $actor = null, ?Company $company = null, ?array $data = null): void
    {
        $recipients = self::admins();

        if ($company) {
            $recipients->push($company);
        }

        foreach ($recipients->filter()->unique(fn ($recipient) => get_class($recipient) . ':' . $recipient->getKey()) as $recipient) {
            self::sendTo(
                recipient: $recipient,
                type: $type,
                title: $title,
                body: $body,
                url: self::positionUrlFor($recipient, $position),
                actor: $actor,
                entity: $position,
                data: $data
            );
        }
    }

    public static function notifyTicketChange(Ticket $ticket, string $type, string $title, string $body, ?Model $actor = null, ?array $data = null): void
    {
        $ticket->loadMissing(['worker', 'company', 'lawyer']);
        $data = array_merge($data ?? [], [
            'ticket_id' => $ticket->id,
            'worker_name' => $ticket->worker?->name,
        ]);

        $recipients = self::admins()
            ->push($ticket->lawyer)
            ->push($ticket->worker)
            ->filter()
            ->unique(fn ($recipient) => get_class($recipient) . ':' . $recipient->getKey());

        foreach ($recipients as $recipient) {
            if (self::isSameModel($recipient, $actor) && ! $recipient instanceof Worker) {
                continue;
            }

            self::sendTo(
                recipient: $recipient,
                type: $type,
                title: $title,
                body: $body,
                url: self::ticketUrlFor($recipient, $ticket),
                actor: $actor,
                entity: $ticket,
                data: $data
            );
        }
    }

    public static function notifyRecommendationCreated(Recommendation $recommendation, ?Model $actor = null): void
    {
        $recommendation->loadMissing(['ticket', 'worker', 'company', 'lawyer']);

        $title = 'تم إرسال توصية جديدة';
        $body = "تم إرسال توصية جديدة بخصوص التذكرة رقم {$recommendation->ticket_id} إلى شركة {$recommendation->company?->company_name}.";

        $recipients = self::admins()
            ->push($recommendation->company)
            ->push($recommendation->lawyer)
            ->filter()
            ->unique(fn ($recipient) => get_class($recipient) . ':' . $recipient->getKey());

        foreach ($recipients as $recipient) {
            if (self::isSameModel($recipient, $actor)) {
                continue;
            }

            self::sendTo(
                recipient: $recipient,
                type: 'recommendation_created',
                title: $title,
                body: $body,
                url: self::recommendationUrlFor($recipient, $recommendation),
                actor: $actor,
                entity: $recommendation,
                data: [
                    'recommendation_id' => $recommendation->id,
                    'ticket_id' => $recommendation->ticket_id,
                    'worker_id' => $recommendation->worker_id,
                    'company_id' => $recommendation->company_id,
                    'lawyer_id' => $recommendation->lawyer_id,
                    'action' => 'created',
                ]
            );
        }
    }

    public static function notifyWorkerChange(Worker $worker, string $type, string $title, string $body, ?Model $actor = null, ?array $data = null): void
    {
        $worker->loadMissing('company.lawyer');
        $data = array_merge($data ?? [], [
            'worker_id' => $worker->id,
            'worker_name' => $worker->name,
        ]);

        $recipients = self::admins()
            ->push($worker->company)
            ->push($worker->company?->lawyer)
            ->push($worker)
            ->filter()
            ->unique(fn ($recipient) => get_class($recipient) . ':' . $recipient->getKey());

        foreach ($recipients as $recipient) {
            self::sendTo(
                recipient: $recipient,
                type: $type,
                title: $title,
                body: $body,
                url: self::workerUrlFor($recipient, $worker),
                actor: $actor,
                entity: $worker,
                data: $data
            );
        }
    }

    private static function ticketUrlFor(Model $recipient, Ticket $ticket): ?string
    {
        return match (get_class($recipient)) {
            Admin::class => Route::has('admin.tickets.show') ? route('admin.tickets.show', $ticket->id) : null,
            Company::class => Route::has('company.tickets.show') ? route('company.tickets.show', $ticket->id) : null,
            Lawyer::class => Route::has('lawyer.tickets.show') ? route('lawyer.tickets.show', $ticket->id) : null,
            default => null,
        };
    }

    private static function isSameModel(Model $recipient, ?Model $actor): bool
    {
        return $actor
            && get_class($recipient) === get_class($actor)
            && (int) $recipient->getKey() === (int) $actor->getKey();
    }

    private static function sendNotificationEmail(Model $recipient, Notifications $notification): void
    {
        $email = self::recipientEmail($recipient);

        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send(new SystemNotificationMail($notification));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send system notification email.', [
                'notification_id' => $notification->id,
                'recipient_type' => get_class($recipient),
                'recipient_id' => $recipient->getKey(),
                'email' => $email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private static function recipientEmail(Model $recipient): ?string
    {
        $email = $recipient->getAttribute('email');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $email;
    }

    private static function sendPushNotification(Model $recipient, Notifications $notification): void
    {
        $token = $recipient->getAttribute('fcm_token');

        if (! is_string($token) || trim($token) === '') {
            return;
        }

        try {
            app(FirebaseCloudMessagingService::class)->send($token, $notification);
        } catch (\Throwable $exception) {
            Log::warning('Failed to send system push notification.', [
                'notification_id' => $notification->id,
                'recipient_type' => get_class($recipient),
                'recipient_id' => $recipient->getKey(),
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private static function workerUrlFor(Model $recipient, Worker $worker): ?string
    {
        return match (get_class($recipient)) {
            Admin::class => Route::has('admin.workers.show') ? route('admin.workers.show', $worker->id) : null,
            Company::class => Route::has('company.workers.show') ? route('company.workers.show', $worker->id) : null,
            Lawyer::class => Route::has('lawyer.workers.show') ? route('lawyer.workers.show', $worker->id) : null,
            default => null,
        };
    }

    private static function lawyerUrlFor(Model $recipient, Lawyer $lawyer): ?string
    {
        return match (get_class($recipient)) {
            Admin::class => Route::has('admin.lawyers.show') ? route('admin.lawyers.show', $lawyer->id) : null,
            Lawyer::class => Route::has('lawyer.profile.show') ? route('lawyer.profile.show') : null,
            Company::class => Route::has('company.lawyer.show') ? route('company.lawyer.show') : null,
            default => null,
        };
    }

    private static function positionUrlFor(Model $recipient, Position $position): ?string
    {
        return match (get_class($recipient)) {
            Admin::class => Route::has('admin.positions.show') ? route('admin.positions.show', $position->id) : null,
            Company::class => Route::has('company.positions.show') ? route('company.positions.show', $position->id) : null,
            default => null,
        };
    }

    private static function recommendationUrlFor(Model $recipient, Recommendation $recommendation): ?string
    {
        return match (get_class($recipient)) {
            Admin::class => Route::has('admin.recommendations.show') ? route('admin.recommendations.show', $recommendation->id) : null,
            Company::class => Route::has('company.recommendations.show') ? route('company.recommendations.show', $recommendation->id) : null,
            Lawyer::class => Route::has('lawyer.recommendations.show') ? route('lawyer.recommendations.show', $recommendation->id) : null,
            default => null,
        };
    }
}
