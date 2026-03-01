<?php

if (!function_exists('solu_log')) {
    /**
     * Logs a message to a custom log file.
     *
     * This function allows you to log messages of different levels (info, error, debug)
     * to a custom log file located in the WordPress root directory (ABSPATH . '/solu_log.log').
     * If the log file doesn't exist, it will be automatically created.
     *
     * Version: 1.0.0
     *
     * @param mixed $message The message to log. Can be a string, array, or object.
     *                       If it's an array or object, it will be converted to a JSON string.
     * @param string $level The log level (info, error, debug). Defaults to 'info'.
     *                       If an invalid level is provided, it will default to 'info'.
     *
     * Example usage:
     *
     * solu_log('This is an informational message.'); // Logs an info message
     * solu_log('This is an error message.', 'error'); // Logs an error message
     * solu_log(['key' => 'value'], 'debug'); // Logs a debug message with a JSON encoded array
     *
     * The log file will contain entries like this:
     *
     * 2023-10-27 10:00:00 - INFO: This is an informational message.
     * 2023-10-27 10:00:00 - ERROR: This is an error message.
     * 2023-10-27 10:00:00 - DEBUG: {"key":"value"}
     */
    function solu_log($message, string $level = 'info')
    {
        $allowed_levels = ['info', 'error', 'debug'];
        if (!in_array($level, $allowed_levels)) {
            $level = 'info'; // Default to info if invalid
        }

        if (is_array($message) || is_object($message)) {
            $message = json_encode($message);
        }

        $dateTime = new DateTime("now", new DateTimeZone("America/Lima"));
        $dateTimePeru = $dateTime->format('Y-m-d H:i:s');

        $log_file = ABSPATH . '/solu_log.log';
        $log_file_error = ABSPATH . '/solu_log_error.log';
        if (!file_exists($log_file)) {
            touch($log_file);
        }
        $log_message = $dateTimePeru . ' - ' . strtoupper($level) . ': ' . $message . PHP_EOL;
        file_put_contents($log_file, $log_message, FILE_APPEND);
        if($level == 'error') {
            file_put_contents($log_file_error, $log_message, FILE_APPEND);
        }
    }
}
