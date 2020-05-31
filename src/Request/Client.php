<?php
namespace Lucinda\STDOUT\Request;

/**
 * Encapsulates information about client that made the request.
 */
class Client
{
    private $name;
    private $ip;
    private $port;
    
    /**
     * Detects info based on values in $_SERVER superglobal
     */
    public function __construct()
    {
        $this->setName();
        $this->setIP();
        $this->setPort();
    }
    
    /**
     * Sets host name.
     */
    private function setName(): void
    {
        $this->name = (isset($_SERVER["REMOTE_HOST"])?$_SERVER["REMOTE_HOST"]:"");
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
     */
    private function setIP(): void
    {
        $this->ip = $_SERVER["REMOTE_ADDR"];
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
     */
    private function setPort(): void
    {
        $this->port = $_SERVER["REMOTE_PORT"];
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
