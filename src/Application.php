<?php
namespace Lucinda\STDOUT;

use Lucinda\STDOUT\Session\Options as SessionOptions;
use Lucinda\STDOUT\Cookies\Options as CookiesOptions;
use Lucinda\MVC\ConfigurationException;
use Lucinda\STDOUT\Application\Route;

/**
 * Compiles information about application.
 */
class Application extends \Lucinda\MVC\Application
{
    private $validatorsPath;
    private $sessionOptions;
    private $cookiesOptions;
    
    /**
     * Populates attributes based on an XML file
     *
     * @param string $xmlFilePath XML file url
     * @throws ConfigurationException If xml content has failed validation.
     */
    public function __construct(string $xmlFilePath)
    {
        $this->readXML($xmlFilePath);
        $this->setApplicationInfo();
        $this->setRoutes();
        $this->setResolvers();
        $this->setSessionOptions();
        $this->setCookieOptions();
    }
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\MVC\Application::setRoutes()
     */
    protected function setRoutes(): void
    {
        $xml = $this->getTag("routes");
        $list = $xml->xpath("//route");
        foreach ($list as $info) {
            $id = (string) $info['id'];
            if (!$id) {
                throw new ConfigurationException("Attribute 'id' is mandatory for 'route' tag");
            }
            $this->routes[$id] = new Route($info);
        }
    }
    
    /**
     * Sets options to start session with based on "session" XML tag
     */
    private function setSessionOptions(): void
    {
        $xml = $this->getTag("session");
        if ($xml===null) {
            return;
        }
        $this->sessionOptions = new SessionOptions($xml);
    }
    
    /**
     * Sets options to create cookies with based on "cookies" XML tag
     */
    private function setCookieOptions(): void
    {
        $xml = $this->getTag("cookies");
        if ($xml===null) {
            return;
        }
        $this->cookiesOptions = new CookiesOptions($xml);
    }
    
    /**
     * Gets  options to start session with based on "session" XML tag
     *
     * @return SessionOptions|NULL
     */
    public function getSessionOptions(): ?SessionOptions
    {
        return $this->sessionOptions;
    }
    
    /**
     * Gets options to create cookies with based on "cookies" XML tag
     *
     * @return CookiesOptions|NULL
     */
    public function getCookieOptions(): ?CookiesOptions
    {
        return $this->cookiesOptions;
    }
}
