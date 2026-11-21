<?php
/**
 * db.php  -  connection to the MySQL database (used by the Contact form).
 *
 * These are the XAMPP defaults. When you host the site on the subdomain,
 * change the four values below to the ones your hosting panel gives you.
 */
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'portfolio';

/**
 * Opens the connection and returns it, or returns null if it fails.
 * Returning null (instead of crashing) lets the page show a friendly message.
 */
function db_connect()
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);   // problems throw exceptions we can catch
    try {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $db->set_charset('utf8mb4');
        return $db;
    } catch (mysqli_sql_exception $ex) {
        error_log('Portfolio DB connection failed: ' . $ex->getMessage());   // detail goes to the server log, not the visitor
        return null;
    }
}
