<?php
namespace Lucinda\STDOUT;

/**
 * Enum of events supported by API for whom listeners can be attached
 */
class EventType
{
    const START = "start";
    const APPLICATION = "application";
    const REQUEST = "request";
    const SESSION = "session";
    const COOKIES = "cookies";
    const RESPONSE = "response";
    const END = "end";
}
