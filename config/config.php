<?php
/**
 * ການຕັ້ງຄ່າພື້ນຖານຂອງລະບົບ
 */

// ກຳນົດສະພາບແວດລ້ອມການເຮັດວຽກ: 'development' ຫຼື 'production'
define('ENVIRONMENT', 'development');

// ຕັ້ງຄ່າການສະແດງຂໍ້ຜິດພາດຕາມສະພາບແວດລ້ອມ
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ຕັ້ງຄ່າ Base URL
define('BASE_URL', '/library/');

// ຕັ້ງຄ່າການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_library');
define('DB_USER', 'root');
define('DB_PASS', '');

//ຕັງຄ່າ API
define('API_KEY', 'jomvc_api_key');

// ຕັ້ງຄ່າ timezone
date_default_timezone_set('Asia/Vientiane');

// ຕັ້ງຄ່າ session
ini_set('session.cookie_lifetime', 3600); // 1 ຊົ່ວໂມງ
ini_set('session.gc_maxlifetime', 3600);

// ຕັ້ງຄ່າຄວາມປອດໄພ
define('CSRF_TOKEN_NAME', 'csrf_token');
define('ENCRYPTION_KEY', 'jomvc_csrf_token');

// ຕັ້ງຄ່າ logging
define('LOG_PATH', __DIR__ . '/../logs/');
define('ERROR_LOG_FILE', LOG_PATH . 'errors.log');
define('ACCESS_LOG_FILE', LOG_PATH . 'access.log');

// ຕັ້ງຄ່າ file upload
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf']);

// ຕັ້ງຄ່າ cache
define('CACHE_ENABLED', true);
define('CACHE_PATH', __DIR__ . '/../cache/');
define('CACHE_LIFETIME', 3600); // 1 ຊົ່ວໂມງ