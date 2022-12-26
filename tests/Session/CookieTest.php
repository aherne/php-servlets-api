<?php
namespace Test\Lucinda\STDOUT\Session;
    
use Lucinda\STDOUT\Session;
use Lucinda\STDOUT\Session\Cookie;
use Lucinda\UnitTest\Result;

class CookieTest
{
    private Session $session;
    private Cookie $cookie;

    public function __construct()
    {
        $this->session = new Session();
        $this->cookie = new Cookie();
    }
    public function getName()
    {
        return new Result($this->cookie->getName()=="PHPSESSID");
    }
    public function getID()
    {
        $this->session->start();
        $sessionID = $this->cookie->getID();
        $this->session->destroy();
        return new Result(strlen($sessionID)>=26);
    }
    public function regenerateID()
    {
        $this->session->start();
        $status = $this->cookie->regenerateID();
        $this->session->destroy();
        return new Result($status);
    }

    public function createNewID()
    {
        $this->session->start();
        $status = $this->cookie->createNewID();
        $this->session->destroy();
        return new Result($status);
    }
}
