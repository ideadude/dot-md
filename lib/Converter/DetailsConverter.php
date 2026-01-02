<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown\Converter;

use DotMD\HtmlToMarkdown\ElementInterface;

/**
 * Converter for HTML5 details/summary elements.
 * Converts to a bold summary followed by the details content.
 */
class DetailsConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        $tag = $element->getTagName();
        $value = $element->getValue();

        // For summary, make it bold to stand out
        if ($tag === 'summary') {
            return '**' . \trim($value) . '**';
        }

        // For details, just return the content (summary will be inside)
        // Add some spacing around it
        if ($tag === 'details') {
            return "\n\n" . $value . "\n\n";
        }

        return $value;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['details', 'summary'];
    }
}
