<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown;

interface ConfigurationAwareInterface
{
    public function setConfig(Configuration $config): void;
}
