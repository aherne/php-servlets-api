<?php
namespace Lucinda\STDOUT\Request;

/**
 * Encapsulates information about client that made the request.
 */
class Client
{
    private $name;
    private $iP;
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
    public function setName()
    {
        $this->iP = (isset($_SERVER["REMOTE_HOST"])?$_SERVER["REMOTE_HOST"]:"");
    }

    /**
     * Gets host name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets IP address.
     */
    private function setIP()
    {
        $this->iP = $_SERVER["REMOTE_ADDR"];
    }
    
    /**
     * Gets IP address
     *
     * @return string
     */
    public function getIP()
    {
        return $this->iP;
    }
    
    /**
     * Sets  port
     */
    private function setPort()
    {
        $this->port = $_SERVER["REMOTE_PORT"];
    }
    
    /**
     * Gets  port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
}
