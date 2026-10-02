<?php
declare(strict_types=1);

function inbox_file(): string
{
    return dirname(__DIR__) . '/storage/messages.json';
}

function inbox_all(): array
{
    $file = inbox_file();
    if (!is_file($file)) {
        return [];
    }
    $rows = json_decode((string) file_get_contents($file), true);
    return is_array($rows) ? $rows : [];
}

function inbox_mutate(callable $mutator): mixed
{
    $dir = dirname(inbox_file());
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $fp = fopen(inbox_file(), 'c+');
    if (!$fp) {
        throw new RuntimeException('Could not store the message.');
    }
    flock($fp, LOCK_EX);
    $raw = stream_get_contents($fp);
    $rows = json_decode($raw ?: '[]', true);
    if (!is_array($rows)) {
        $rows = [];
    }
    $result = $mutator($rows);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $result;
}

function inbox_add(array $message): array
{
    inbox_mutate(function (array &$rows) use ($message) {
        array_unshift($rows, $message);
    });
    return $message;
}

function inbox_find(string $id): ?array
{
    foreach (inbox_all() as $row) {
        if (($row['id'] ?? '') === $id) {
            return $row;
        }
    }
    return null;
}

function inbox_update(string $id, callable $mutator): ?array
{
    return inbox_mutate(function (array &$rows) use ($id, $mutator) {
        foreach ($rows as $i => $row) {
            if (($row['id'] ?? '') === $id) {
                $rows[$i] = $mutator($row);
                return $rows[$i];
            }
        }
        return null;
    });
}
