<?php
namespace Test\Lucinda\STDOUT\Service;

use Lucinda\STDOUT\Service\ContentTypeDetector;
use Lucinda\STDOUT\XmlTags\ResolverInfo;
use Lucinda\UnitTest\Validator\Strings;
use Test\Lucinda\STDOUT\Support\TestHelper;

class ContentTypeDetectorTest
{
    private ContentTypeDetector $object;

    public function __construct()
    {
        $this->object = new ContentTypeDetector(
            new ResolverInfo(
                TestHelper::element(
                    '<resolver format="json" content_type="application/json" class="Test\Lucinda\STDOUT\Support\Resolvers\JsonResolver" charset="UTF-8"/>'
                )
            )
        );
    }

    public function getContentType()
    {
        return new Strings($this->object->getContentType())->assertEquals("application/json; charset=UTF-8");
    }
}
