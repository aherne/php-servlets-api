<?php

namespace Lucinda\STDOUT\Session;

/**
 * Provides operations for PHP's current session identifier
 *
 * The facade reads and changes process-level session state directly. It does
 * not cache the session name or identifier between calls.
 */
final class Cookie
{
    /**
     * Gets the configured session cookie name
     *
     * @return string Current value returned by session_name()
     */
    public function getName(): string
    {
        return (string) session_name();
    }

    /**
     * Gets the current session identifier
     *
     * @return string Current value returned by session_id()
     */
    public function getID(): string
    {
        return (string) session_id();
    }

    /**
     * Regenerates the identifier while retaining existing session data
     *
     * @return bool True when PHP regenerates the identifier successfully
     */
    public function regenerateID(): bool
    {
        return session_regenerate_id();
    }

    /**
     * Creates a fresh identifier without retaining existing session data
     *
     * An active session is regenerated with deletion of its old data. When no
     * session is active, a new identifier is generated and selected for the
     * next session start.
     *
     * @return bool True when a non-empty identifier is created and selected
     */
    public function createNewID(): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            return session_regenerate_id(true);
        }

        $newSessionID = session_create_id();
        if ($newSessionID === false || $newSessionID === "") {
            return false;
        }
        session_id($newSessionID);
        return true;
    }
}
