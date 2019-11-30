<?php
namespace Lucinda\STDOUT\Request;

/**
 * Encapsulates information about server that received the request.
 */
class Server
{
    private $name;
    private $iP;
    private $port;
    private $email;
    private $software;
    
    /**
     * Detects info based on values in $_SERVER superglobal
     */
    public function __construct(): void
    {
        $this->setIP();
        $this->setName();
        $this->setPort();
        $this->setEmail();
        $this->setSoftware();
    }
    
    /**
     * Sets server host name.
     */
    private function setName(): void
    {
        $this->name = $_SERVER["SERVER_NAME"];
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
     */
    private function setIP(): void
    {
        $this->iP = $_SERVER["SERVER_ADDR"];
    }
    
    /**
     * Gets server IP address
     *
     * @return string
     */
    public function getIP(): string
    {
        return $this->iP;
    }
    
    
    /**
     * Sets server port
     */
    private function setPort(): void
    {
        $this->port = $_SERVER["SERVER_PORT"];
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
     */
    private function setEmail(): void
    {
        $this->email = $_SERVER["SERVER_ADMIN"];
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
     */
    private function setSoftware(): void
    {
        $this->software = $_SERVER["SERVER_SOFTWARE"];
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
