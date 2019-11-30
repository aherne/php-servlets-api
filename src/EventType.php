<?php
namespace Lucinda\STDOUT;

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
