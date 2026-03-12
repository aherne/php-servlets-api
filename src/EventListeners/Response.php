<?php

namespace Lucinda\STDOUT\EventListeners;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\Runnable;

/**
 * Defines blueprint of an event that executes before response is rendered to client
 */
interface Response extends EventListener
{
}
