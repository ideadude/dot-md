<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown;

interface PreConverterInterface
{
    public function preConvert(ElementInterface $element): void;
}
