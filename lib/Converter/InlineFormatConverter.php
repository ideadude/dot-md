<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown\Converter;

use DotMD\HtmlToMarkdown\ElementInterface;

/**
 * Converter for additional inline formatting tags.
 * Handles strikethrough, underline, and mark/highlight tags.
 */
class InlineFormatConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        $tag = $element->getTagName();
        $value = $element->getValue();

        // If empty content, just return it
        if (! \trim($value)) {
            return $value;
        }

        // Handle strikethrough tags - convert to ~~text~~
        if ($tag === 's' || $tag === 'del' || $tag === 'strike') {
            $prefix = \ltrim($value) !== $value ? ' ' : '';
            $suffix = \rtrim($value) !== $value ? ' ' : '';

            return $prefix . '~~' . \trim($value) . '~~' . $suffix;
        }

        // For underline and mark, just strip tags (no markdown equivalent)
        // Return the inner content
        return $value;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['s', 'del', 'strike', 'u', 'mark'];
    }
}
