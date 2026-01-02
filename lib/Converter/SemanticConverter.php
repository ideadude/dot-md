<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown\Converter;

use DotMD\HtmlToMarkdown\ElementInterface;

/**
 * Converter for semantic HTML5 tags that have no Markdown equivalent.
 * Strips the tags but preserves their content.
 */
class SemanticConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        // Simply return the inner content without the wrapper tags
        return $element->getValue();
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['article', 'header', 'section', 'footer', 'main', 'aside', 'nav', 'figure', 'figcaption', 'cite', 'span'];
    }
}
