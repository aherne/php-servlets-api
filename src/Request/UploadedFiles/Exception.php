<?php

namespace Lucinda\STDOUT\Request\UploadedFiles;

/**
 * Reports an upload failure supplied by PHP's multipart processing
 *
 * Callers may present this as a request validation error instead of treating it
 * as an internal application failure.
 */
class Exception extends \Exception
{
}
