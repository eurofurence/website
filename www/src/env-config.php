<?php
/**
 * Environment configuration - determines which data source is used
 */
header('Content-Type: application/javascript; charset=utf-8');
?>

window.__EF_ENVIRONMENT__ = {
    USE_MOCK_DATA: <?php echo getenv('USE_MOCK_DATA') === 'true' ? 'true' : 'false'; ?>,
    MOCK_LF_DATA: '__mocks__/lostandfound.mock.json'
};

if (window.__EF_ENVIRONMENT__.USE_MOCK_DATA) {
    console.warn('[EF] Mock environment enabled - using test data');
}
