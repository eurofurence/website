<?php
/**
 * Environment configuration - data source selection and global feature flags.
 * Values come directly from the "environment" section of config/core.json.
 */
header('Content-Type: application/javascript; charset=utf-8');

$ef_environment = [
    'USE_MOCK_DATA' => false,
    'EFNAV_ENABLED' => false,
    'MOCK_LF_DATA' => '__mocks__/lostandfound.mock.json',
];

$config_raw = @file_get_contents(__DIR__ . '/../config/core.json');
if ($config_raw !== false) {
    $config = json_decode($config_raw, true);
    if (is_array($config) && isset($config['environment']) && is_array($config['environment'])) {
        $environment = $config['environment'];

        $ef_environment['USE_MOCK_DATA'] = ($environment['USE_MOCK_DATA'] ?? false) === true;
        $ef_environment['EFNAV_ENABLED'] = ($environment['EFNAV_ENABLED'] ?? false) === true;

        if (is_string($environment['MOCK_LF_DATA'] ?? null) && $environment['MOCK_LF_DATA'] !== '') {
            $ef_environment['MOCK_LF_DATA'] = $environment['MOCK_LF_DATA'];
        }
    }
}
?>

window.__EF_ENVIRONMENT__ = <?= json_encode($ef_environment, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?>;

if (window.__EF_ENVIRONMENT__.USE_MOCK_DATA) {
    console.warn('[EF] Mock environment enabled - using test data');
}
