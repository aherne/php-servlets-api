<?php

namespace Lucinda\STDOUT;

use Lucinda\MVC\Response\Http;

/**
 * Implements a HTTP response paradigm, always setting Content-Type as well
 */
final class Response extends Http
{
    /**
     * Bootstraps the process by setting Content-Type header automatically.
     * 
     * @param string $contentType
     */
    public function __construct(string $contentType)
    {
        parent::__construct();
        
        $this->setHeader("Content-Type", $contentType);
    }
}