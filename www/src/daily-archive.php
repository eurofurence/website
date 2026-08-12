<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

const DAILY_PDF_HREF_REGEX = '/href="([^"]+\\.pdf)"/i';
const DAILY_HTTP_URL_REGEX = '/^https?:\/\//i';
const DAILY_PDF_PATH_REGEX = '/\\.pdf$/i';
const DAILY_METADATA_FILENAME_REGEX = '/^(?<event>[A-Za-z0-9]+)_(?<date>\\d{4}\\.\\d{2}\\.\\d{2})_(?<day>[a-z]+)_issue\\.(?<issue>\\d+)\\.pdf$/i';

$archiveBaseUrl = 'https://archive.eurofurence.org/daily/';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 8,
        'ignore_errors' => true,
        'header' => "User-Agent: EF-Website Daily Archive Loader\r\nAccept: text/html\r\n"
    ],
    'ssl' => [
        'verify_peer' => true,
        'verify_peer_name' => true
    ]
]);

$listing = @file_get_contents($archiveBaseUrl, false, $context);
if ($listing === false || $listing === '') {
    respond([
        'ok' => false,
        'source' => $archiveBaseUrl,
        'message' => 'Archive directory unavailable.',
        'items' => []
    ]);
}

preg_match_all(DAILY_PDF_HREF_REGEX, $listing, $matches);
if (empty($matches[1])) {
    respond([
        'ok' => false,
        'source' => $archiveBaseUrl,
        'message' => 'No PDFs found in archive listing.',
        'items' => []
    ]);
}

$items = [];
$seen = [];

foreach ($matches[1] as $href) {
    $decodedHref = html_entity_decode(trim($href), ENT_QUOTES, 'UTF-8');
    $normalizedUrl = normalize_archive_url($decodedHref, $archiveBaseUrl);
    if ($normalizedUrl === null) {
        continue;
    }

    if (array_key_exists($normalizedUrl, $seen)) {
        continue;
    }

    $seen[$normalizedUrl] = true;

    $path = parse_url($normalizedUrl, PHP_URL_PATH);
    if (!$path) {
        continue;
    }

    $fileName = basename($path);
    $meta = parse_daily_metadata($fileName);

    $items[] = [
        'url' => $normalizedUrl,
        'filename' => $fileName,
        'edition' => $meta['edition'],
        'date' => $meta['date'],
        'displayDate' => $meta['displayDate'],
        'day' => $meta['day'],
        'issue' => $meta['issue'],
        'year' => $meta['year'],
        'sortDate' => $meta['sortDate'],
        'label' => build_daily_label($meta, $fileName)
    ];
}

if (empty($items)) {
    respond([
        'ok' => false,
        'source' => $archiveBaseUrl,
        'message' => 'Archive parse yielded no valid PDF links.',
        'items' => []
    ]);
}

usort($items, function ($left, $right) {
    $leftIssue = array_key_exists('issue', $left) && $left['issue'] !== null ? intval($left['issue']) : PHP_INT_MAX;
    $rightIssue = array_key_exists('issue', $right) && $right['issue'] !== null ? intval($right['issue']) : PHP_INT_MAX;

    if ($leftIssue !== $rightIssue) {
        return $leftIssue <=> $rightIssue;
    }

    $leftSortDate = array_key_exists('sortDate', $left) ? intval($left['sortDate']) : PHP_INT_MAX;
    $rightSortDate = array_key_exists('sortDate', $right) ? intval($right['sortDate']) : PHP_INT_MAX;
    if ($leftSortDate !== $rightSortDate) {
        return $leftSortDate <=> $rightSortDate;
    }

    return strcmp($left['filename'], $right['filename']);
});

respond([
    'ok' => true,
    'source' => $archiveBaseUrl,
    'count' => count($items),
    'items' => $items
]);

function respond(array $payload): void
{
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function normalize_archive_url(string $href, string $archiveBaseUrl): ?string
{
    if ($href === '') {
        return null;
    }

    if (strpos($href, '//') === 0) {
        $href = 'https:' . $href;
    } elseif (strpos($href, '/') === 0) {
        $href = 'https://archive.eurofurence.org' . $href;
    } elseif (!preg_match(DAILY_HTTP_URL_REGEX, $href)) {
        $href = $archiveBaseUrl . ltrim($href, '/');
    }

    $parsed = parse_url($href);
    if (!$parsed || !isset($parsed['path'])) {
        return null;
    }

    if (isset($parsed['host']) && strcasecmp($parsed['host'], 'archive.eurofurence.org') !== 0) {
        return null;
    }

    if (!preg_match(DAILY_PDF_PATH_REGEX, $parsed['path'])) {
        return null;
    }

    return 'https://archive.eurofurence.org' . $parsed['path'];
}

function parse_daily_metadata(string $fileName): array
{
    $match = [];
    if (preg_match(DAILY_METADATA_FILENAME_REGEX, $fileName, $match) !== 1) {
        return [
            'edition' => 'Archive',
            'date' => '',
            'displayDate' => '',
            'day' => '',
            'issue' => null,
            'year' => null,
            'sortDate' => 0,
        ];
    }

    $date = $match['date'];
    $year = intval(substr($date, 0, 4));
    $issue = intval($match['issue']);

    return [
        'edition' => strtoupper($match['event']),
        'date' => $date,
        'displayDate' => format_daily_display_date($date),
        'day' => ucfirst(strtolower($match['day'])),
        'issue' => $issue,
        'year' => $year,
        'sortDate' => intval(str_replace('.', '', $date)),
    ];
}

function build_daily_label(array $meta, string $fileName): string
{
    if (!array_key_exists('issue', $meta) || $meta['issue'] === null) {
        return $fileName;
    }

    return sprintf(
        '%s - %s %s (Issue %d)',
        $meta['edition'],
        $meta['date'],
        $meta['day'],
        intval($meta['issue'])
    );
}

function format_daily_display_date(string $date): string
{
    $dateObject = DateTimeImmutable::createFromFormat('Y.m.d', $date);
    if ($dateObject === false) {
        return $date;
    }

    return $dateObject->format('d M Y');
}
