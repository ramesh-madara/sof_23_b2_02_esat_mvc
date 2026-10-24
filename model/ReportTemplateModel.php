<?php

require_once __DIR__ . '/Database.php';

class ReportTemplateModel
{
    private static ?array $templates = null;

    public static function all(): array
    {
        if (self::$templates === null) {
            $rows = Database::getConnection()->query('SELECT id, type, name, fields FROM report_templates ORDER BY id')->fetchAll();
            self::$templates = [];
            foreach ($rows as $row) {
                $row['fields'] = json_decode($row['fields'], true, 512, JSON_THROW_ON_ERROR);
                self::$templates[$row['type']] = $row;
            }
        }
        return self::$templates;
    }

    public static function find(string $type): ?array
    {
        return self::all()[$type] ?? null;
    }

    public static function readValues(string $type, array $input): array
    {
        $template = self::find($type);
        if ($template === null) {
            throw new InvalidArgumentException('Please select a valid report type.');
        }
        $results = [];
        foreach ($template['fields'] as $field) {
            $raw = $input[$field['name']] ?? '';
            $value = is_string($raw) ? trim($raw) : '';
            if (!is_numeric($value) || !is_finite((float)$value) || (float)$value < $field['min'] || (float)$value > $field['max']) {
                throw new InvalidArgumentException($field['label'] . ' must be a number between ' . $field['min'] . ' and ' . $field['max'] . '.');
            }
            $results[$field['label']] = $value;
        }
        return $results;
    }
}
