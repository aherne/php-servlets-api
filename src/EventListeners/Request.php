<?php

namespace Lucinda\STDOUT\EventListeners;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\Runnable;

/**
 * Defines blueprint of an event that executes after request that came from client is parsed into a Request object
 */
interface Request extends EventListener
{
}
