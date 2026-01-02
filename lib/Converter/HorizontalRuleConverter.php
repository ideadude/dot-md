<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown\Converter;

use DotMD\HtmlToMarkdown\ElementInterface;

class HorizontalRuleConverter implements ConverterInterface
{
    public function convert(ElementInterface $element): string
    {
        return "---\n\n";
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['hr'];
    }
}
