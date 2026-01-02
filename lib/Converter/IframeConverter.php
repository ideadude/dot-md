<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown\Converter;

use DotMD\HtmlToMarkdown\ElementInterface;

/**
 * Converter for HTML iframe elements.
 * Converts video embeds and other iframes to clean markdown links.
 */
class IframeConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        $src   = $element->getAttribute('src');
        $title = $element->getAttribute('title');

        // If no src, return empty (similar to LinkConverter behavior)
        if ($src === '') {
            return '';
        }

        // If title exists, use link format with title text for context
        if ($title !== '') {
            return '[' . $title . '](' . $src . ')';
        }

        // Default: use auto-link format for clean, simple output
        return '<' . $src . '>';
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['iframe'];
    }
}
