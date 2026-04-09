<?php

namespace Test\Lucinda\STDOUT\Support\Transformers;

use Lucinda\MVC\EventListener;
use Lucinda\MVC\Response\Transformer\Body;

final class BodySuffixTransformer implements Body, EventListener
{
    public function transform(string $source): string
    {
        return $source . "!";
    }
}
