<?php

namespace Lucinda\STDOUT\Session;

/**
 * Encapsulates operations to perform on session id cookie
 */
final class Cookie
{
    /**
     * Get name of session id cookie
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) session_name();
    }

    /**
     * Get value of session id
     *
     * @return string
     */
    public function getID(): string
    {
        return (string) session_id();
    }

    /**
     * Regenerate session id, keeping old session info
     *
     * @return bool
     */
    public function regenerateID(): bool
    {
        return session_regenerate_id();
    }

    /**
     * Create new session id disregarding session info
     *
     * @return bool
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
