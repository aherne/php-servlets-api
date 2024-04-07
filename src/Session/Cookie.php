<?php

namespace Lucinda\STDOUT\Session;

/**
 * Encapsulates operations to perform on session id cookie
 */
class Cookie
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
        return session_create_id();
    }
}
