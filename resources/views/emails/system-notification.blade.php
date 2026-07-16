<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
</head>
<body style="margin:0;background:#f6f8fc;font-family:Tahoma,Arial,sans-serif;color:#0f1b3d;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8fc;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f1b3d;color:#ffffff;padding:22px 26px;">
                            <div style="font-size:20px;font-weight:800;">أمان</div>
                            <div style="margin-top:6px;font-size:13px;color:#cbd5e1;">إشعار جديد داخل النظام</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:26px;">
                            <div style="display:inline-block;margin-bottom:14px;border-radius:999px;background:#eef3ff;color:#5368aa;padding:7px 12px;font-size:12px;font-weight:800;">
                                {{ $notification->type }}
                            </div>

                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.5;color:#0f1b3d;">
                                {{ $notification->title }}
                            </h1>

                            @if($notification->body)
                                <p style="margin:0 0 22px;font-size:15px;line-height:2;color:#475569;">
                                    {{ $notification->body }}
                                </p>
                            @endif

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;border-collapse:collapse;">
                                <tr>
                                    <td style="padding:12px;border-top:1px solid #edf2f7;color:#94a3b8;font-size:12px;font-weight:800;">وقت الإشعار</td>
                                    <td style="padding:12px;border-top:1px solid #edf2f7;color:#0f1b3d;font-size:13px;font-weight:700;">
                                        {{ $notification->created_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i') }}
                                    </td>
                                </tr>

                                @if($notification->url)
                                    <tr>
                                        <td style="padding:12px;border-top:1px solid #edf2f7;color:#94a3b8;font-size:12px;font-weight:800;">الرابط</td>
                                        <td style="padding:12px;border-top:1px solid #edf2f7;font-size:13px;font-weight:700;">
                                            <a href="{{ $notification->url }}" style="color:#2563eb;text-decoration:none;">فتح الإشعار</a>
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            @if($notification->url)
                                <div style="margin-top:24px;">
                                    <a href="{{ $notification->url }}" style="display:inline-block;border-radius:14px;background:#0f1b3d;color:#ffffff;padding:12px 20px;font-size:14px;font-weight:800;text-decoration:none;">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 26px;background:#f8fbff;color:#94a3b8;font-size:12px;line-height:1.8;">
                            هذا البريد تم إرساله تلقائيًا بناءً على إشعار داخل أمان.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
