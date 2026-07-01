<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Tahoma, Arial, sans-serif; color: #0f1b3d; margin: 24px; background: #fff; }
        header { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px; }
        h1 { margin: 0; font-size: 28px; }
        h2 { margin: 28px 0 12px; font-size: 18px; }
        .meta { color: #64748b; font-size: 13px; line-height: 1.8; }
        .actions { margin-bottom: 18px; }
        button { border: 0; background: #0f1b3d; color: #fff; padding: 12px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; page-break-inside: auto; }
        th, td { border: 1px solid #dbe3ef; padding: 9px 10px; font-size: 12px; vertical-align: top; text-align: right; }
        th { background: #f1f5f9; font-weight: 700; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        .empty { color: #64748b; background: #f8fafc; padding: 16px; border: 1px solid #e2e8f0; border-radius: 12px; }
        @media print {
            body { margin: 10mm; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">حفظ / طباعة PDF</button>
    </div>

    <header>
        <div>
            <h1>{{ $title }}</h1>
            <div class="meta">myaman</div>
        </div>
        <div class="meta">
            تاريخ الاستخراج: {{ $generatedAt->format('Y-m-d H:i') }}
        </div>
    </header>

    @foreach($sections as $section)
        <section>
            <h2>{{ $section['title'] }}</h2>
            @if(empty($section['rows']))
                <div class="empty">لا توجد بيانات في هذا التقرير.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            @foreach($section['headers'] as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($section['rows'] as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @endforeach
</body>
</html>
