<?php

namespace Lucinda\STDOUT\XmlTags;

use Lucinda\MVC\XmlReader\Element;
use Lucinda\MVC\XmlReader\Exception;

/**
 * Parses PHP session settings from the XML `session` element
 *
 * String settings are retained verbatim, server lifetimes must be positive integers and cookie lifetimes non-negative,
 * and boolean settings accept only 0 or 1. Every option is nullable so omitted
 * attributes leave the corresponding PHP session setting unchanged.
 */
final class SessionOptions
{
    private ?string $sameSite = null;

    /**
     * Optional session storage path
     */
    private ?string $savePath = null;
    /**
     * Optional session cookie name
     */
    private ?string $name = null;
    /**
     * Optional server-side lifetime in seconds
     */
    private ?int $expiredTime = null;
    /**
     * Optional cookie lifetime in seconds
     */
    private ?int $expiredOnBrowserClose = null;
    /**
     * Optional secure-cookie flag
     */
    private ?bool $isSecuredByHTTPS = null;
    /**
     * Optional HTTP-only cookie flag
     */
    private ?bool $isSecuredByHTTPheaders = null;
    /**
     * Optional referrer substring required by PHP
     */
    private ?string $referrerCheck = null;
    /**
     * Optional custom session handler class
     */
    private ?string $handlerFile = null;
    /**
     * Optional PHP session auto-start flag
     */
    private ?bool $autoStart = null;

    /**
     * Parses all supported session attributes
     *
     * @param Element $element Parsed `session` element
     * @throws Exception If a duration or boolean attribute is invalid
     */
    public function __construct(Element $element)
    {
        $info = $element->getAttributes();
        $this->setStringOptions($info);
        $this->setIntOptions($info);
        $this->setBooleanOptions($info);
        $this->setSameSite($info);
    }

    /**
     * Assigns configured string-valued options
     *
     * @param array<string,string> $info Session element attributes
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
     * Validates server and cookie duration options
     *
     * @param array<string,string> $info Session element attributes
     * @throws Exception If a configured duration is not a positive number
     */
    private function setIntOptions(array $info): void
    {
        $options = [
            "expiredTime"=>"expired_time",
            "expiredOnBrowserClose"=>"expired_on_close"
        ];
        foreach ($options as $field=>$column) {
            if (isset($info[$column])) {
                if (!preg_match("/^[0-9]+$/D", $info[$column]) || ($column === "expired_time" && (int) $info[$column] === 0)) {
                    throw new Exception("Field ".$column." must hold a positive integer (expired_on_close also accepts zero)");
                }
                $this->$field = (int) $info[$column];
            }
        }
    }

    /**
     * Validates and assigns boolean options
     *
     * @param array<string,string> $info Session element attributes
     * @throws Exception If a configured boolean is neither 0 nor 1
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
     * Gets the server-side session storage path
     *
     * @return string|null Configured path, or null to retain the PHP default
     */
    public function getSavePath(): ?string
    {
        return $this->savePath;
    }

    /**
     * Gets the session cookie name
     *
     * @return string|null Configured name, or null to retain the PHP default
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Gets the server-side session lifetime
     *
     * @return int|null Lifetime in seconds, or null to retain the PHP default
     */
    public function getExpiredTime(): ?int
    {
        return $this->expiredTime;
    }

    /**
     * Gets the session cookie lifetime
     *
     * @return int|null Lifetime in seconds, or null to retain the PHP default
     */
    public function getExpiredOnBrowserClose(): ?int
    {
        return $this->expiredOnBrowserClose;
    }

    /**
     * Gets whether the session cookie requires HTTPS
     *
     * @return bool|null Secure flag, or null to retain the PHP default
     */
    public function isSecuredByHTTPS(): ?bool
    {
        return $this->isSecuredByHTTPS;
    }

    /**
     * Gets whether the session cookie is hidden from browser scripting
     *
     * @return bool|null HTTP-only flag, or null to retain the PHP default
     */
    public function isSecuredByHTTPheaders(): ?bool
    {
        return $this->isSecuredByHTTPheaders;
    }

    /**
     * Gets the referrer substring accepted by PHP session handling
     *
     * @return string|null Configured substring, or null to retain the PHP default
     */
    public function getReferrerCheck(): ?string
    {
        return $this->referrerCheck;
    }

    /**
     * Gets the custom session save handler class
     *
     * @return string|null Autoloadable class name, or null to use PHP's configured handler
     */
    public function getHandler(): ?string
    {
        return $this->handlerFile;
    }

    /**
     * Gets whether PHP session auto-start is enabled
     *
     * @return bool|null Configured flag, or null to retain the PHP default
     */
    public function isAutoStart(): ?bool
    {
        return $this->autoStart;
    }
    /**
     * Parses the optional SameSite policy without depending on the security API.
     *
     * @param array<string,string> $info XML attributes
     * @throws Exception If the policy is unsupported
     */
    private function setSameSite(array $info): void
    {
        if (!isset($info["same_site"])) {
            return;
        }
        if (!in_array($info["same_site"], ["Lax", "Strict", "None"], true)) {
            throw new Exception("Field same_site must be Lax, Strict or None");
        }
        $this->sameSite = $info["same_site"];
    }

    /**
     * Gets the configured SameSite policy, or null when omitted.
     */
    public function getSameSite(): ?string
    {
        return $this->sameSite;
    }

}
