<?php

namespace Test\Lucinda\STDOUT\XmlTags;

use Lucinda\STDOUT\XmlTags\ResolverInfo;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class ResolverInfoTest
{
    private ResolverInfo $object;

    public function __construct()
    {
        $this->object = new ResolverInfo(
            TestHelper::element(
                '<resolver format="json" content_type="application/json" class="Test\Lucinda\STDOUT\Support\Resolvers\JsonResolver" charset="UTF-8"/>'
            )
        );
    }

    public function getContentType()
    {
        return new Strings($this->object->getContentType())->assertEquals("application/json");
    }

    public function getCharacterEncoding()
    {
        return new Strings($this->object->getCharacterEncoding())->assertEquals("UTF-8");
    }
}
