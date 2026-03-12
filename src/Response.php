<?php

namespace Lucinda\STDOUT;

use Lucinda\MVC\Response\Headers;
use Lucinda\MVC\Response\Http;

final class Response extends Http
{
    public function __construct(string $contentType)
    {
        parent::__construct();
        
        $this->setHeader("Content-Type", $contentType);
    }
}