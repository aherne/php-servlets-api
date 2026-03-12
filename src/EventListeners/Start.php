<?php

namespace Lucinda\STDOUT\EventListeners;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\Runnable;

/**
 * Defines blueprint of an event that executes when application starts execution (before XML is read)
 */
interface Start extends EventListener
{
}
