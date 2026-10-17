<?php

namespace Lucinda\STDOUT\Request;

/**
 * Captures network information about the client that made the request
 *
 * Values are read from the REMOTE_HOST, REMOTE_ADDR, and REMOTE_PORT server
 * entries. No forwarded-header or trusted-proxy interpretation is performed.
 */
final class Client
{
    /**
     * Remote host name, or an empty string when unavailable
     */
    private string $name;

    /**
     * Remote IP address reported by the web server
     */
    private string $ip;

    /**
     * Remote source port reported by the web server
     */
    private int $port;

    /**
     * Captures client values from a $_SERVER snapshot
     *
     * @param array<string,mixed> $server Server values containing REMOTE_ADDR and REMOTE_PORT
     */
    public function __construct(array $server)
    {
        $this->setName($server);
        $this->setIP($server);
        $this->setPort($server);
    }

    /**
     * Captures the optional remote host name
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setName(array $server): void
    {
        $this->name = ($server["REMOTE_HOST"] ?? "");
    }

    /**
     * Gets the remote host name
     *
     * @return string Host name, or an empty string when REMOTE_HOST was absent
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Captures the remote IP address
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setIP(array $server): void
    {
        $this->ip = $server["REMOTE_ADDR"];
    }

    /**
     * Gets the remote IP address
     *
     * @return string Address reported by REMOTE_ADDR
     */
    public function getIP(): string
    {
        return $this->ip;
    }

    /**
     * Captures the remote source port
     *
     * @param array<string,mixed> $server Snapshot of $_SERVER
     */
    private function setPort(array $server): void
    {
        $this->port = (int) $server["REMOTE_PORT"];
    }

    /**
     * Gets the remote source port
     *
     * @return int Port reported by REMOTE_PORT
     */
    public function getPort(): int
    {
        return $this->port;
    }
}
