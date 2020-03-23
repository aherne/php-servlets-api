<?php
namespace Test\Lucinda\STDOUT\Session;

use Lucinda\STDOUT\Session\SecurityOptions;
use Lucinda\UnitTest\Result;

class SecurityOptionsTest
{
    private $object;
    private $settings = [];
    
    public function __construct()
    {
        $this->object = new SecurityOptions();
        
        $fields = [
            "session.save_path",
            "session.name",
            "session.gc_maxlifetime",
            "session.cookie_lifetime",
            "session.cookie_secure",
            "session.cookie_httponly",
            "session.referer_check"
        ];
        foreach ($fields as $field) {
            $this->settings[$field] = ini_get($field);
        }
    }
    
    private function check($iniVariable, $newValue)
    {
        $this->object->save();
        $new = ini_get($iniVariable);
        ini_set($iniVariable, $this->settings[$iniVariable]);
        return new Result($new == $newValue);
    }

    public function setSavePath()
    {
        $this->object->setSavePath("asd");
        return $this->check("session.save_path", "asd");
    }
        

    public function setName()
    {
        $this->object->setName("asd");
        return $this->check("session.name", "asd");
    }
        

    public function setExpiredTime()
    {
        $this->object->setExpiredTime(10);
        return $this->check("session.gc_maxlifetime", 10);
    }
        

    public function setExpiredOnBrowserClose()
    {
        $this->object->setExpiredOnBrowserClose(10);
        return $this->check("session.cookie_lifetime", 10);
    }
        

    public function setSecuredByHTTPS()
    {
        $this->object->setSecuredByHTTPS(true);
        return $this->check("session.cookie_secure", true);
    }
        

    public function setSecuredByHTTPheaders()
    {
        $this->object->setSecuredByHTTPheaders(true);
        return $this->check("session.cookie_httponly", true);
    }
        

    public function setSecuredByReferrerCheck()
    {
        $this->object->setSecuredByReferrerCheck("asd");
        return $this->check("session.referer_check", "asd");
    }
        

    public function save()
    {
        $this->object->setSavePath("asd");
        $this->object->save();
        $new = ini_get("session.save_path");
        ini_set("session.save_path", $this->settings["session.save_path"]);
        return new Result($new == "asd");
    }
}
