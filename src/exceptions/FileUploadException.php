<?php
namespace Lucinda\MVC\STDOUT;
/**
 * Exception thrown when uploaded file fails validation. This should be an error that should be handled manually and not cause application to terminate.
 */
class FileUploadException extends \Exception {}