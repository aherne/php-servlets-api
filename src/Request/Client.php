<?php

namespace Lucinda\STDOUT\Request;

/**
 * Encapsulates information about client that made the request.
 */
class Client
{
    private string $name;
    private string $ip;
    private int $port;

    /**
     * Detects info based on values in $_SERVER superglobal
     *
     * @param array<string,string> $server
     */
    public function __construct(array $server)
    {
        $this->setName($server);
        $this->setIP($server);
        $this->setPort($server);
    }

    /**
     * Sets host name.
     *
     * @param array<string,string> $server
     */
    private function setName(array $server): void
    {
        $this->name = ($server["REMOTE_HOST"] ?? "");
    }

    /**
     * Gets host name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets IP address.
     *
     * @param array<string,string> $server
     */
    private function setIP(array $server): void
    {
        $this->ip = $server["REMOTE_ADDR"];
    }

    /**
     * Gets IP address
     *
     * @return string
     */
    public function getIP(): string
    {
        return $this->ip;
    }

    /**
     * Sets  port
     *
     * @param array<string,string> $server
     */
    private function setPort(array $server): void
    {
        $this->port = (int) $server["REMOTE_PORT"];
    }

    /**
     * Gets  port
     *
     * @return integer
     */
    public function getPort(): int
    {
        return $this->port;
    }
}
