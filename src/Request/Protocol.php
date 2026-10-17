<?php

namespace Lucinda\STDOUT\Request;

/**
 * Enumerates the HTTP transport protocols detected for a request
 */
enum Protocol: string
{
    case HTTP = "http";
    case HTTPS = "https";
}
