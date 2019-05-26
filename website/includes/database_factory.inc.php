<?php

function db_factory_produce()
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return;
    }

    // Users table:
    // Delete the old user table if it exists.
    $sql_query = "DROP TABLE IF EXISTS users;";
    __db_factory_run_safe_sql($db_conn, $sql_query);
    // Create the new table.
    $sql_query = "CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username TINYTEXT NOT NULL,
    passwd LONGTEXT NOT NULL,
    permissionlevel INT(11) NOT NULL
    );";
    __db_factory_run_safe_sql($db_conn, $sql_query);

    // Runs table:
    // Delete the old runs table if it exists.
    $sql_query = "DROP TABLE IF EXISTS runs;";
    __db_factory_run_safe_sql($db_conn, $sql_query);
    // Create the new table.
    $sql_query = "CREATE TABLE runs (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name TEXT NOT NULL,
    active BOOLEAN NOT NULL
    );";
    __db_factory_run_safe_sql($db_conn, $sql_query);

    // Relays table:
    // Delete the old relays table it it exists.
    $sql_query = "DROP TABLE IF EXISTS relays;";
    __db_factory_run_safe_sql($db_conn, $sql_query);
    // Create the new table
    $sql_query = "CREATE TABLE relays (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    runid INT(11) NOT NULL,
    name TEXT NOT NULL
    );";
    __db_factory_run_safe_sql($db_conn, $sql_query);

    // Relay start event table:
    // Delete the old relay start table if it exists.
    $sql_query = "DROP TABLE IF EXISTS startevents;";
    __db_factory_run_safe_sql($db_conn, $sql_query);
    // Create the new table
    $sql_query = "CREATE TABLE startevents (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    relayid INT(11) NOT NULL,
    utc BIGINT NOT NULL
    );";
    __db_factory_run_safe_sql($db_conn, $sql_query);

    // Runners table:
    // Delete the old runners table if it exists.
    $sql_query = "DROP TABLE IF EXISTS runners;";
    __db_factory_run_safe_sql($db_conn, $sql_query);
    // Create the new table
    $sql_query = "CREATE TABLE runners (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    relayid INT(11) NOT NULL,
    runneruid TEXT NOT NULL,
    firstname TEXT NOT NULL,
    surname TEXT NOT NULL
    );";
    __db_factory_run_safe_sql($db_conn, $sql_query);

    // Runner stop events table:
    // Delete the old runner stop events table if it exists.
    $sql_query = "DROP TABLE IF EXISTS stopevents;";
    __db_factory_run_safe_sql($db_conn, $sql_query);
    // Create the new table
    $sql_query = "CREATE TABLE stopevents (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    runid INT(11) NOT NULL,
    utc BIGINT NOT NULL
    );";
    __db_factory_run_safe_sql($db_conn, $sql_query);

    // Runner info acquisition table:
    // Delete the old runner info acquisition table if it exists.
    $sql_query = "DROP TABLE IF EXISTS stopacquisition;";
    __db_factory_run_safe_sql($db_conn, $sql_query);
    // Create the new table
    $sql_query = "CREATE TABLE stopacquisition (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    runnerid INT(11) NOT NULL,
    utc BIGINT NOT NULL
    );";
    __db_factory_run_safe_sql($db_conn, $sql_query);
}

// Exits the PHP file with an SQL-error.
function __db_factory_exit_sql_error()
{
    exit("Error: Bad SQL.");
}

// Runs safe SQL queries without variables.
function __db_factory_run_safe_sql($db_conn, $sql_query)
{
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        __db_factory_exit_sql_error();
    }
    mysqli_stmt_execute($sql_stmt);
}