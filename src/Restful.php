<?php

namespace Lucinda\STDOUT;

use Lucinda\MVC\Controller;

/**
 * Marks a controller whose public handler is selected by HTTP request method
 *
 * RestfulWrapper dispatches to a method named after the current Request\Method
 * value, such as GET(), POST(), or DELETE(). The selected method must require
 * no arguments and return either a View or null.
 *
 * @see RestfulWrapper
 */
interface Restful extends Controller
{
}
