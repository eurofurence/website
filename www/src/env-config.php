<?php
/**
 * Environment configuration - data source selection and global feature flags
 */
header('Content-Type: application/javascript; charset=utf-8');
?>

window.__EF_ENVIRONMENT__ = {
    USE_MOCK_DATA: <?php echo getenv('USE_MOCK_DATA') === 'true' ? 'true' : 'false'; ?>,
    MOCK_LF_DATA: '__mocks__/lostandfound.mock.json',
    EFNAV_ENABLED: <?php echo getenv('EFNAV_ENABLED') === 'true' ? 'true' : 'false'; ?>
};

if (window.__EF_ENVIRONMENT__.USE_MOCK_DATA) {
    console.warn('[EF] Mock environment enabled - using test data');
}
