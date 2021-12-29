<?php
namespace Lucinda\STDOUT\Request;

/**
 * Enum containing HTTP request methods
 */
enum Method: string
{
    case OPTIONS = "OPTIONS";
    case GET = "GET";
    case HEAD = "HEAD";
    case POST = "POST";
    case PUT = "PUT";
    case DELETE = "DELETE";
    case TRACE = "TRACE";
    case CONNECT = "CONNECT";
    case PATCH = "PATCH";
}
