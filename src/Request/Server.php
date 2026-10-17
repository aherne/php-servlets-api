<?php

namespace Lucinda\STDOUT\Request;

/**
 * Captures information about the server that received the request
 *
 * Values come directly from the corresponding SERVER_* entries in the supplied
 * $_SERVER snapshot and are not subsequently refreshed.
 */
final class Server
{
    /**
     * Server host name
     */
    private string $name;
    /**
     * Server IP address
     */
    private string $ip;
    /**
     * Listening server port
     */
    private int $port;
    /**
     * Configured server administrator email
     */
    private string $email;
    /**
     * Web server software identification string
     */
    private string $software;

    /**
     * Captures server values from a $_SERVER snapshot
     *
     * @param array<string,mixed> $server Server values containing required SERVER_* entries
     */
    public function __construct(array $server)
    {
        $this->setIP($server);
        $this->setName($server);
        $this->setPort($server);
        $this->setEmail($server);
        $this->setSoftware($server);
    }

    /**
     * Captures the server host name
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setName(array $server): void
    {
        $this->name = $server["SERVER_NAME"];
    }

    /**
     * Gets the server host name
     *
     * @return string Value reported by SERVER_NAME
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Captures the server IP address
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setIP(array $server): void
    {
        $this->ip = $server["SERVER_ADDR"];
    }

    /**
     * Gets the server IP address
     *
     * @return string Value reported by SERVER_ADDR
     */
    public function getIP(): string
    {
        return $this->ip;
    }


    /**
     * Captures the listening server port
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setPort(array $server): void
    {
        $this->port = (int) $server["SERVER_PORT"];
    }

    /**
     * Gets the listening server port
     *
     * @return int Value reported by SERVER_PORT
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Captures the configured server administrator email
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setEmail(array $server): void
    {
        $this->email = $server["SERVER_ADMIN"];
    }

    /**
     * Gets the configured server administrator email
     *
     * @return string Value reported by SERVER_ADMIN
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Captures the web server software description
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setSoftware(array $server): void
    {
        $this->software = $server["SERVER_SOFTWARE"];
    }

    /**
     * Gets the web server software description
     *
     * @return string Value reported by SERVER_SOFTWARE
     */
    public function getSoftware(): string
    {
        return $this->software;
    }
}
