<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown\Converter;

use DotMD\HtmlToMarkdown\ElementInterface;

/**
 * Converter for HTML input elements, primarily for task list checkboxes.
 */
class InputConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        $type = $element->getAttribute('type');

        // Handle checkboxes (for task lists)
        if ($type === 'checkbox') {
            $checked = $element->getAttribute('checked');
            // Return markdown checkbox syntax
            return $checked ? '[x] ' : '[ ] ';
        }

        // For other input types, just remove them
        return '';
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['input'];
    }
}
