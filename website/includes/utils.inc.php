<?php

/**
 * Utility Library.
 */


/**
 * Gets the name of a run from the given runid.
 */
function db_get_runname_from_id($runid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return "n.a.";
    }

    $sql_query = "SELECT name FROM runs WHERE id=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return "SQL_ERROR";
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $runid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    $runname = "n.a.";
    if ($sql_row = mysqli_fetch_assoc($sql_result)) {
        $runname = $sql_row["name"];
    }
    mysqli_free_result($sql_result);
    mysqli_close($db_conn);
    return $runname;
}

/**
 * Deletes a run specified by the given runid.
 */
function db_delete_run($runid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return false;
    }

    // Delete the run.
    $sql_query = "DELETE FROM runs WHERE id=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return false;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $runid);
    mysqli_stmt_execute($sql_stmt);

    // Close the database connection.
    mysqli_close($db_conn);

    return true;
}

/**
 * Activates a run specified by the given runid.
 * 
 * Sets all active statuses in the database to 0 and afterwards sets the status
 * of the given run to 1. This ensures that there can never be two runs active
 * at once. Even if somehow two runs get activated at once by a bug, the system
 * shall take the first active run found whenever querying the database for an
 * active run.
 * 
 * If a negative $runid is sent, all runs get deactivated.
 */
function db_set_run_active($runid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return false;
    }

    // Set every run to not active
    $sql_query = "UPDATE runs SET active=0;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return false;
    }
    mysqli_stmt_execute($sql_stmt);

    // Activate a single run.
    if ($runid > 0) {
        $sql_query = "UPDATE runs SET active=1 WHERE id=?;";
        $sql_stmt = mysqli_stmt_init($db_conn);
        if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
            return false;
        }
        mysqli_stmt_bind_param($sql_stmt, "i", $runid);
        mysqli_stmt_execute($sql_stmt);
    }

    // Close the database connection.
    mysqli_close($db_conn);

    return true;
}

/**
 * Gets the name of the user from the given userid.
 */
function db_get_username_from_id($userid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return "n.a.";
    }

    $sql_query = "SELECT username FROM users WHERE id=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return "SQL_ERROR";
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $userid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    $username = "n.a.";
    if ($sql_row = mysqli_fetch_assoc($sql_result)) {
        $username = $sql_row["username"];
    }
    mysqli_free_result($sql_result);
    mysqli_close($db_conn);
    return $username;
}
