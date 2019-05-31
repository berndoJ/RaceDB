<?php

/**
 * Utility Library.
 */

require_once __DIR__ . "/msg.inc.php";


/**
 * Gets the name of a run from the given runid.
 */
function db_get_runname_from_id($runid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return false;
    }

    $sql_query = "SELECT name FROM runs WHERE id=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return false;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $runid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    $runname = false;
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

/**
 * Creates a new relay with the given name and associates it with a run by the
 * given runid.
 * 
 * Returns MSG_SUCCESS if the process was successful.
 * Error messages:
 *      MSG_ERROR_SQL_NO_CONNECTION,
 *      MSG_ERROR_SQL_BAD_QUERY,
 *      "ERR_EXISTS",
 *      "ERR_INVALID_RUNID"
 */
function db_create_relay($runid, $relayname)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return MSG_ERROR_SQL_NO_CONNECTION;
    }

    // Check if relayname exists
    $sql_query = "SELECT * FROM relays WHERE name=? AND runid=?";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "si", $relayname, $runid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    if (mysqli_num_rows($sql_result) > 0) {
        return "ERR_EXISTS";
    }
    mysqli_free_result($sql_result);

    // Validate the runid
    $sql_query = "SELECT * FROM runs WHERE id=?";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $runid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    if (mysqli_num_rows($sql_result) < 1) {
        return "ERR_INVALID_RUNID";
    }
    mysqli_free_result($sql_result);

    // Create the relay.
    $sql_query = "INSERT INTO relays (runid, name) VALUES (?, ?);";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "is", $runid, $relayname);
    mysqli_stmt_execute($sql_stmt);

    // Close the database
    mysqli_close($db_conn);

    // Return success
    return MSG_SUCCESS;
}

/**
 * Deletes a relay specified by the given relay id.
 */
function db_delete_relay($relayid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return MSG_ERROR_SQL_NO_CONNECTION;
    }

    // Delete the relay.
    $sql_query = "DELETE FROM relays WHERE id=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $relayid);
    mysqli_stmt_execute($sql_stmt);

    // Delete all runners of the relay.
    $sql_query = "DELETE FROM runners WHERE relayid=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $relayid);
    mysqli_stmt_execute($sql_stmt);

    // Close the database connection.
    mysqli_close($db_conn);

    return MSG_SUCCESS;
}

/**
 * Modifies the values of a relay given by the relayid. If any property of the
 * relay is set to "null", no changes will be conducted.
 */
function db_modify_relay($relayid, $relayname = null)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return MSG_ERROR_SQL_NO_CONNECTION;
    }

    // Modify the name.
    if ($relayname != null) {
        $sql_query = "UPDATE relays SET name=? WHERE id=?;";
        $sql_stmt = mysqli_stmt_init($db_conn);
        if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
            return MSG_ERROR_SQL_BAD_QUERY;
        }
        mysqli_stmt_bind_param($sql_stmt, "si", $relayname, $relayid);
        mysqli_stmt_execute($sql_stmt);
    }

    // Close the database connection.
    mysqli_close($db_conn);

    return MSG_SUCCESS;
}

/**
 * Gets the name of the relay given by the relayid.
 */
function db_get_relayname_from_id($relayid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return false;
    }

    $sql_query = "SELECT name FROM relays WHERE id=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return false;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $relayid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    $relayname = false;
    if ($sql_row = mysqli_fetch_assoc($sql_result)) {
        $relayname = $sql_row["name"];
    }
    mysqli_free_result($sql_result);
    mysqli_close($db_conn);
    return $relayname;
}

/**
 * Creates a new runner with the given firstname, surname and runner uid and
 * associates the newly created runner with a relay by the given relayid.
 * 
 * Returns MSG_SUCCESS if the process was successful.
 * Error messages:
 *      MSG_ERROR_SQL_NO_CONNECTION,
 *      MSG_ERROR_SQL_BAD_QUERY,
 *      "ERR_EXISTS",
 *      "ERR_UID_EXISTS",
 *      "ERR_INVALID_RELAYID"
 */
function db_create_runner($relayid, $firstname, $surname, $runneruid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return MSG_ERROR_SQL_NO_CONNECTION;
    }

    // Check if runner exists
    $sql_query = "SELECT * FROM runners WHERE firstname=? AND surname=? AND runneruid=? AND relayid=?";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "sssi", $firstname, $surname, $runneruid, $relayid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    if (mysqli_num_rows($sql_result) > 0) {
        return "ERR_EXISTS";
    }
    mysqli_free_result($sql_result);

    // Check if runner UID exists
    $sql_query = 
    "SELECT
        rn.id AS runnerid
    FROM
        runners AS rn
    INNER JOIN
        relays AS rl
        ON (rl.id = rn.relayid)
    WHERE
        rl.runid = (SELECT runid FROM relays WHERE id=?) AND rn.runneruid=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "is", $relayid, $runneruid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    if (mysqli_num_rows($sql_result) > 0) {
        return "ERR_UID_EXISTS";
    }
    mysqli_free_result($sql_result);

    // Validate the relayid
    $sql_query = "SELECT * FROM relays WHERE id=?";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $relayid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    if (mysqli_num_rows($sql_result) < 1) {
        return "ERR_INVALID_RELAYID";
    }
    mysqli_free_result($sql_result);

    // Create the new runner.
    $sql_query = "INSERT INTO runners (firstname, surname, runneruid, relayid) VALUES (?, ?, ?, ?);";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "sssi", $firstname, $surname, $runneruid, $relayid);
    mysqli_stmt_execute($sql_stmt);

    // Close the database
    mysqli_close($db_conn);

    // Return success
    return MSG_SUCCESS;
}

/**
 * Deletes a runner specified by the given runner id.
 */
function db_delete_runner($runnerid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return MSG_ERROR_SQL_NO_CONNECTION;
    }

    // Delete the runner.
    $sql_query = "DELETE FROM runners WHERE id=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $runnerid);
    mysqli_stmt_execute($sql_stmt);

    // Close the database connection.
    mysqli_close($db_conn);

    return MSG_SUCCESS;
}

/**
 * Modifies the values of a runner given by the runnerid. If any property of the
 * relay is set to "null", no changes will be conducted.
 */
function db_modify_runner($runnerid, $firstname = null, $surname = null, $runneruid = null, $relayid = null)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return MSG_ERROR_SQL_NO_CONNECTION;
    }

    // Modify the firstname.
    if ($firstname != null) {
        $sql_query = "UPDATE runners SET firstname=? WHERE id=?;";
        $sql_stmt = mysqli_stmt_init($db_conn);
        if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
            return MSG_ERROR_SQL_BAD_QUERY;
        }
        mysqli_stmt_bind_param($sql_stmt, "si", $firstname, $runnerid);
        mysqli_stmt_execute($sql_stmt);
    }

    // Modify the surname.
    if ($surname != null) {
        $sql_query = "UPDATE runners SET surname=? WHERE id=?;";
        $sql_stmt = mysqli_stmt_init($db_conn);
        if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
            return MSG_ERROR_SQL_BAD_QUERY;
        }
        mysqli_stmt_bind_param($sql_stmt, "si", $surname, $runnerid);
        mysqli_stmt_execute($sql_stmt);
    }

    // Modify the runneruid.
    if ($runneruid != null) {
        $sql_query = "UPDATE runners SET runneruid=? WHERE id=?;";
        $sql_stmt = mysqli_stmt_init($db_conn);
        if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
            return MSG_ERROR_SQL_BAD_QUERY;
        }
        mysqli_stmt_bind_param($sql_stmt, "si", $runneruid, $runnerid);
        mysqli_stmt_execute($sql_stmt);
    }

    // Modify the relayid.
    if ($relayid != null) {
        $sql_query = "UPDATE runners SET relayid=? WHERE id=?;";
        $sql_stmt = mysqli_stmt_init($db_conn);
        if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
            return MSG_ERROR_SQL_BAD_QUERY;
        }
        mysqli_stmt_bind_param($sql_stmt, "ii", $relayid, $runnerid);
        mysqli_stmt_execute($sql_stmt);
    }

    // Close the database connection.
    mysqli_close($db_conn);

    return MSG_SUCCESS;
}

/**
 * Gets the full information about a runner specified by the given runner id.
 * 
 * If the process was successfull, this function returns a string of values
 * separated by "\n". Format:
 * MSG_SUCCESS \n [Firstname] \n [Surname] \n [Runner-UID] \n [Relay-ID]
 */
function db_get_runner_info_from_id($runnerid)
{
    require_once __DIR__ . "/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn) {
        return MSG_ERROR_SQL_NO_CONNECTION;
    }

    $sql_query = "SELECT * FROM runners WHERE id=?;";
    $sql_stmt = mysqli_stmt_init($db_conn);
    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
        return MSG_ERROR_SQL_BAD_QUERY;
    }
    mysqli_stmt_bind_param($sql_stmt, "i", $runnerid);
    mysqli_stmt_execute($sql_stmt);
    $sql_result = mysqli_stmt_get_result($sql_stmt);
    $result = MSG_ERROR_VARIABLES_INVALID;
    if ($sql_row = mysqli_fetch_assoc($sql_result)) {
        $result = MSG_SUCCESS . "\n". $sql_row["firstname"] . "\n" . $sql_row["surname"] . "\n" . $sql_row["runneruid"] . "\n" . $sql_row["relayid"];
    }
    mysqli_free_result($sql_result);
    mysqli_close($db_conn);
    return $result;
}
