<?php

require_once __DIR__ . '/../../model/ReportTemplateModel.php';

class TestRanges
{
    public static function get(string $testName, int $age, string $gender): ?array
    {
        foreach (ReportTemplateModel::all() as $template) {
            foreach ($template['fields'] as $field) {
                if ($field['label'] === $testName) {
                    if ($age < $field['minAge']) {
                        return null;
                    }
                    return $field['ranges'][$gender] ?? $field['ranges']['all'] ?? null;
                }
            }
        }
        return null;
    }
    public static function check(string $testName, $value, int $age, string $gender): array
    {
        $range = self::get($testName, $age, $gender);
        $result = ['range' => $range['label'] ?? 'Not set', 'status' => 'Review needed', 'style' => 'review'];

        if (!is_numeric($value) || !is_finite((float)$value) || (float)$value < 0) {
            $result['status'] = 'Invalid reading';
            return $result;
        }
        if ($range === null) {
            return $result;
        }

        $reading = (float)$value;
        if ($range['min'] !== null && $reading < $range['min']) {
            $result['status'] = 'Low';
            $result['style'] = 'low';
        } elseif ($range['max'] !== null && ($range['strictMax'] ? $reading >= $range['max'] : $reading > $range['max'])) {
            $result['status'] = 'High';
            $result['style'] = 'high';
        } else {
            $result['status'] = 'Within range';
            $result['style'] = 'okay';
        }
        return $result;
    }
}
