<?php

namespace Lucinda\STDOUT\Request;

/**
 * Encapsulates information about server that received the request.
 */
class Server
{
    private string $name;
    private string $ip;
    private int $port;
    private string $email;
    private string $software;

    /**
     * Detects info based on values in $_SERVER superglobal
     *
     * @param array<string,string> $server
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
     * Sets server host name.
     *
     * @param array<string,string> $server
     */
    private function setName(array $server): void
    {
        $this->name = $server["SERVER_NAME"];
    }

    /**
     * Gets server host name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets server IP address.
     *
     * @param array<string,string> $server
     */
    private function setIP(array $server): void
    {
        $this->ip = $server["SERVER_ADDR"];
    }

    /**
     * Gets server IP address
     *
     * @return string
     */
    public function getIP(): string
    {
        return $this->ip;
    }


    /**
     * Sets server port
     *
     * @param array<string,string> $server
     */
    private function setPort(array $server): void
    {
        $this->port = (int) $server["SERVER_PORT"];
    }

    /**
     * Gets server port
     *
     * @return integer
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Sets server admin email.
     *
     * @param array<string,string> $server
     */
    private function setEmail(array $server): void
    {
        $this->email = $server["SERVER_ADMIN"];
    }

    /**
     * Gets server admin email.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets software web server is using.
     *
     * @param array<string,string> $server
     */
    private function setSoftware(array $server): void
    {
        $this->software = $server["SERVER_SOFTWARE"];
    }

    /**
     * Gets software web server is using.
     *
     * @return string
     */
    public function getSoftware(): string
    {
        return $this->software;
    }
}
