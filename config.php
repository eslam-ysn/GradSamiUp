<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'sentiment_feedback');
define('DB_USER', 'root');
define('DB_PASS', '');
define('PYTHON_BIN', 'python');
define('PYTHON_PATH', 'C:\\Users\\sami0\\PyCharmMiscProject\\.venv\\Scripts\\python.exe'); // عدل حسب مسار بايثون
define('MODEL_SCRIPT', __DIR__ . '/model/hyper_model.py');
define('MODEL_ZIP_PATH', 'C:\\Users\\sami0\\Desktop\\best_roberta_model.zip');
define('TOKEN_MIN', 0);
define('TOKEN_MAX', 999999);

session_start();
?>
