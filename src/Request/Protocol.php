<?php
namespace Lucinda\STDOUT\Request;

/**
 * Enum containing supported HTTP request protocols
 */
enum Protocol: string
{
    case HTTP = "http";
    case HTTPS = "https";
}