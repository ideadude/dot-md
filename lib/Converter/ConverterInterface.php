<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown\Converter;

use DotMD\HtmlToMarkdown\ElementInterface;

interface ConverterInterface
{
    public function convert(ElementInterface $element): string;

    /**
     * @return string[]
     */
    public function getSupportedTags(): array;
}
