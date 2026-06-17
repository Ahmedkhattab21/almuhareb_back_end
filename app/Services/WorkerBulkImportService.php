<?php

namespace App\Services;

use App\Models\Company;
use App\Models\City;
use App\Models\Nationality;
use App\Models\Position;
use App\Models\PreferedLanguage;
use App\Models\Worker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class WorkerBulkImportService
{
    private const NPL_TABLE = 'nationalities_prefered_language';

    public function import(UploadedFile $file, Company $company, array $defaults = [], mixed $actor = null): array
    {
        $rows = $this->readRows($file);
        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            try {
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $payload = $this->payload($row, $company, $defaults);
                $this->validatePayload($payload, $line);

                DB::transaction(function () use ($payload, $company, &$created) {
                    $worker = Worker::create($payload['worker']);

                    DB::table(self::NPL_TABLE)->updateOrInsert(
                        ['worker_id' => $worker->id],
                        [
                            'nationality_id' => $payload['nationality_id'],
                            'prefered_language_id' => $payload['prefered_language_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    $created++;
                });
            } catch (\Throwable $exception) {
                $skipped++;
                $errors[] = __('worker_import.row_error', [
                    'row' => $line,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
            'errors' => array_slice($errors, 0, 50),
            'total_errors' => count($errors),
        ];
    }

    public function templateResponse(string $fileName = 'workers-import-template.csv')
    {
        $headers = [
            'name',
            'email',
            'phone',
            'iqama_number',
        ];

        $example = [
            'Ahmed Mohamed',
            'worker1@example.com',
            '0555237602',
            '233434232',
        ];

        $callback = function () use ($headers, $example) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            fputcsv($handle, $example);
            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function payload(array $row, Company $company, array $defaults): array
    {
        $languageId = $defaults['preferred_language_id'] ?? $defaults['prefered_language_id'] ?? null;
        $nationalityId = $defaults['nationality_id'] ?? null;
        $positionId = $defaults['position_id'] ?? null;
        $cityId = $defaults['city_id'] ?? null;

        $language = $this->languageById($languageId);
        $nationality = $this->nationalityById($nationalityId);
        $position = $this->positionById($positionId);
        $city = $this->cityById($cityId);

        $worker = [
            'company_id' => $company->id,
            'created_by' => $company->id,
            'name' => trim((string) ($row['name'] ?? '')),
            'email' => $this->nullable($row['email'] ?? null),
            'phone' => trim((string) ($row['phone'] ?? '')),
            'iqama_number' => $this->nullable($row['iqama_number'] ?? $row['residency_number'] ?? null),
            'status' => 'active',
        ];

        if (Schema::hasColumn('workers', 'position_id')) {
            $worker['position_id'] = $position?->id;
        }

        if (Schema::hasColumn('workers', 'city_id')) {
            $worker['city_id'] = $city?->id;
        }

        if (Schema::hasColumn('workers', 'nationality_id')) {
            $worker['nationality_id'] = $nationality?->id;
        }

        if (Schema::hasColumn('workers', 'prefered_language_id')) {
            $worker['prefered_language_id'] = $language?->id;
        }

        if (Schema::hasColumn('workers', 'preferred_language_id')) {
            $worker['preferred_language_id'] = $language?->id;
        }

        if (Schema::hasColumn('workers', 'language_id')) {
            $worker['language_id'] = $language?->id;
        }

        if (Schema::hasColumn('workers', 'preferred_language')) {
            $worker['preferred_language'] = $language?->code;
        }

        if (Schema::hasColumn('workers', 'prefered_language')) {
            $worker['prefered_language'] = $language?->code;
        }

        if (Schema::hasColumn('workers', 'language')) {
            $worker['language'] = $language?->code;
        }

        if (Schema::hasColumn('workers', 'password')) {
            $worker['password'] = Hash::make(Str::random(32));
        }

        return [
            'worker' => $worker,
            'nationality_id' => $nationality?->id,
            'prefered_language_id' => $language?->id,
        ];
    }

    private function validatePayload(array $payload, int $line): void
    {
        $worker = $payload['worker'];

        if ($worker['name'] === '') {
            throw new RuntimeException(__('worker_import.name_required'));
        }

        if ($worker['phone'] === '') {
            throw new RuntimeException(__('worker_import.phone_required'));
        }

        if (Worker::where('phone', $worker['phone'])->exists()) {
            throw new RuntimeException(__('worker_import.phone_exists'));
        }

        if (! empty($worker['email']) && Worker::where('email', $worker['email'])->exists()) {
            throw new RuntimeException(__('worker_import.email_exists'));
        }

        if (! empty($worker['iqama_number']) && Worker::where('iqama_number', $worker['iqama_number'])->exists()) {
            throw new RuntimeException(__('worker_import.iqama_exists'));
        }

        if (! $payload['nationality_id']) {
            throw new RuntimeException(__('worker_import.nationality_required'));
        }

        if (! $payload['prefered_language_id']) {
            throw new RuntimeException(__('worker_import.language_required'));
        }

        if (Schema::hasColumn('workers', 'city_id') && empty($worker['city_id'])) {
            throw new RuntimeException(__('worker_import.city_required'));
        }
    }

    private function readRows(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'csv', 'txt' => $this->readCsv($file->getRealPath()),
            'xlsx' => $this->readXlsx($file->getRealPath()),
            default => throw new RuntimeException(__('worker_import.unsupported_file')),
        };
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if (! $handle) {
            throw new RuntimeException(__('worker_import.read_failed'));
        }

        $headers = null;
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = $this->headers($line);
                continue;
            }

            $rows[] = $this->combine($headers, $line);
        }

        fclose($handle);

        return $rows;
    }

    private function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException(__('worker_import.zip_required'));
        }

        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException(__('worker_import.xlsx_open_failed'));
        }

        $sharedStrings = $this->sharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (! $sheetXml) {
            throw new RuntimeException(__('worker_import.xlsx_sheet_missing'));
        }

        $sheet = new SimpleXMLElement($sheetXml);
        $rawRows = [];

        foreach ($sheet->sheetData->row as $row) {
            $values = [];

            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $index = $this->columnIndex($ref);
                $type = (string) $cell['t'];
                $value = (string) $cell->v;

                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }

                $values[$index] = $value;
            }

            if ($values) {
                ksort($values);
                $rawRows[] = $values;
            }
        }

        if (empty($rawRows)) {
            return [];
        }

        $headers = $this->headers(array_values(array_shift($rawRows)));

        return collect($rawRows)
            ->map(fn ($row) => $this->combine($headers, array_values($row)))
            ->all();
    }

    private function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if (! $xml) {
            return [];
        }

        $shared = new SimpleXMLElement($xml);
        $strings = [];

        foreach ($shared->si as $item) {
            $strings[] = trim((string) $item->t);
        }

        return $strings;
    }

    private function headers(array $headers): array
    {
        return collect($headers)
            ->map(fn ($header) => Str::snake(trim(Str::lower((string) $header))))
            ->map(fn ($header) => ltrim($header, "\xEF\xBB\xBF"))
            ->all();
    }

    private function combine(array $headers, array $values): array
    {
        $row = [];

        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $row[$header] = trim((string) ($values[$index] ?? ''));
        }

        return $row;
    }

    private function columnIndex(string $cellReference): int
    {
        preg_match('/^[A-Z]+/', strtoupper($cellReference), $matches);
        $letters = $matches[0] ?? 'A';
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isEmpty();
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function languageById(mixed $value): ?PreferedLanguage
    {
        $value = trim((string) $value);

        if ($value === '' || ! ctype_digit($value)) {
            return null;
        }

        return PreferedLanguage::query()
            ->where('status', 'active')
            ->whereKey((int) $value)
            ->first();
    }

    private function nationalityById(mixed $value): ?Nationality
    {
        $value = trim((string) $value);

        if ($value === '' || ! ctype_digit($value)) {
            return null;
        }

        return Nationality::query()
            ->where('status', 'active')
            ->whereKey((int) $value)
            ->first();
    }

    private function positionById(mixed $value): ?Position
    {
        $value = trim((string) $value);

        if ($value === '' || ! ctype_digit($value)) {
            return null;
        }

        return Position::query()
            ->whereKey((int) $value)
            ->first();
    }

    private function cityById(mixed $value): ?City
    {
        $value = trim((string) $value);

        if ($value === '' || ! ctype_digit($value)) {
            return null;
        }

        return City::query()
            ->where('status', 'active')
            ->whereKey((int) $value)
            ->first();
    }
}
