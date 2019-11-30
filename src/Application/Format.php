<?php
namespace Lucinda\STDOUT\Application;

/**
 * Encapsulates file format information:
 * - format: file format / name
 * - content type: content type that corresponds to above file format
 * - character encoding: charset associated to content type
 * - view resolver: (optional) view resolver class name. If not set, framework will resolve into an empty view with headers only.
 */
class Format
{
    private $name;
    private $contentType;
    private $viewResolverClass;
    private $characterEncoding;

    /**
     * Saves response format data detected from XML tag "format".
     *
     * @param string $name
     * @param string $contentType
     * @param string $characterEncoding
     * @param string $viewResolverClass
     */
    public function __construct(string $name, string $contentType, string $characterEncoding="", string $viewResolverClass=""): void
    {
        $this->name = $name;
        $this->contentType = $contentType;
        $this->characterEncoding= $characterEncoding;
        $this->viewResolverClass = $viewResolverClass;
    }

    /**
     * Gets response format name (extension).
     *
     * @return string
     * @example json
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets content type
     *
     * @return string
     * @example application/json
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }
    
    /**
     * Gets character encoding (charset)
     *
     * @return string
     */
    public function getCharacterEncoding(): string
    {
        return $this->characterEncoding;
    }

    /**
     * Gets view resolver class name
     *
     * @return string
     * @example JsonResolver
     */
    public function getViewResolver(): string
    {
        return $this->viewResolverClass;
    }
}
