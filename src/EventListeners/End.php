<?php

namespace Lucinda\STDOUT\EventListeners;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\Runnable;

/**
 * Defines blueprint of an event that executes when application ends execution (after response is committed to client)
 */
interface End extends EventListener
{
}
