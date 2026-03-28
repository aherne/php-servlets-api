<?php

namespace Lucinda\STDOUT\XmlTags;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Detects default session options from XML tag <session>
 */
final class SessionOptions
{
    private ?string $savePath = null;
    private ?string $name = null;
    private ?int $expiredTime = null;
    private ?int $expiredOnBrowserClose = null;
    private ?bool $isSecuredByHTTPS = null;
    private ?bool $isSecuredByHTTPheaders = null;
    private ?string $referrerCheck = null;
    private ?string $handlerFile = null;
    private ?bool $autoStart = null;

    /**
     * Saves session options based on XML tag "session"
     *
     * @param Element $element
     */
    public function __construct(Element $element)
    {
        $info = $element->getAttributes();
        $this->setStringOptions($info);
        $this->setIntOptions($info);
        $this->setBooleanOptions($info);
    }

    /**
     * Sets session options of string type
     */
    private function setStringOptions(array $info): void
    {
        $options = [
            "savePath"=>"save_path",
            "name"=>"name",
            "referrerCheck"=>"referrer_check",
            "handlerFile"=>"handler"
        ];
        foreach ($options as $field=>$column) {
            if (isset($info[$column])) {
                $this->$field = $info[$column];
            }
        }
    }

    /**
     * Sets session options of integer type
     */
    private function setIntOptions(array $info): void
    {
        $options = [
            "expiredTime"=>"expired_time",
            "expiredOnBrowserClose"=>"expired_on_close"
        ];
        foreach ($options as $field=>$column) {
            if (isset($info[$column])) {
                if (!is_numeric($info[$column]) || $info[$column]<=0) {
                    throw new Exception("Field ".$column." must hold a positive numeric value");
                }
                $this->$field = (int) $info[$column];
            }
        }
    }

    /**
     * Sets session options of boolean type
     */
    private function setBooleanOptions(array $info): void
    {
        $options = [
            "isSecuredByHTTPS"=>"https_only",
            "isSecuredByHTTPheaders"=>"headers_only",
            "autoStart"=>"auto_start"
        ];
        foreach ($options as $field=>$column) {
            if (isset($info[$column])) {
                if ($info[$column]!=1 && $info[$column]!=0) {
                    throw new Exception("Field ".$column." can only have 1 or 0 value");
                }
                $this->$field = (bool) $info[$column];
            }
        }
    }

    /**
     * Gets path that is going to be used when storing sessions.
     *
     * @return string
     */
    public function getSavePath(): string
    {
        return $this->savePath;
    }

    /**
     * Gets name of session cookie.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets session cookie's expiration time.
     *
     * @return int
     */
    public function getExpiredTime(): int
    {
        return $this->expiredTime;
    }

    /**
     * Gets session expiration time on browser close.
     *
     * @return int
     */
    public function getExpiredOnBrowserClose(): int
    {
        return $this->expiredOnBrowserClose;
    }

    /**
     * Gets whether sessions are accepted only if protocol is HTTPS
     *
     * @return bool
     */
    public function isSecuredByHTTPS(): bool
    {
        return $this->isSecuredByHTTPS;
    }

    /**
     * Gets whether session id cookie is available to client via JavaScript
     *
     * @return bool
     */
    public function isSecuredByHTTPheaders(): bool
    {
        return $this->isSecuredByHTTPheaders;
    }

    /**
     * Gets HTTP referrer for whom sessions are accepted
     *
     * @return string
     */
    public function getReferrerCheck(): string
    {
        return $this->referrerCheck;
    }

    /**
     * Gets handler file name
     *
     * @return string
     */
    public function getHandler(): string
    {
        return $this->handlerFile;
    }

    /**
     * Gets whether session should start automatically
     *
     * @return bool
     */
    public function isAutoStart(): bool
    {
        return $this->autoStart;
    }
}
