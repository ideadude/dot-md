<?php

declare(strict_types=1);

namespace DotMD\HtmlToMarkdown;

use DotMD\HtmlToMarkdown\Converter\BlockquoteConverter;
use DotMD\HtmlToMarkdown\Converter\CodeConverter;
use DotMD\HtmlToMarkdown\Converter\CommentConverter;
use DotMD\HtmlToMarkdown\Converter\ConverterInterface;
use DotMD\HtmlToMarkdown\Converter\DetailsConverter;
use DotMD\HtmlToMarkdown\Converter\DefaultConverter;
use DotMD\HtmlToMarkdown\Converter\DivConverter;
use DotMD\HtmlToMarkdown\Converter\EmphasisConverter;
use DotMD\HtmlToMarkdown\Converter\HardBreakConverter;
use DotMD\HtmlToMarkdown\Converter\HeaderConverter;
use DotMD\HtmlToMarkdown\Converter\HorizontalRuleConverter;
use DotMD\HtmlToMarkdown\Converter\ImageConverter;
use DotMD\HtmlToMarkdown\Converter\InlineFormatConverter;
use DotMD\HtmlToMarkdown\Converter\InputConverter;
use DotMD\HtmlToMarkdown\Converter\LinkConverter;
use DotMD\HtmlToMarkdown\Converter\ListBlockConverter;
use DotMD\HtmlToMarkdown\Converter\ListItemConverter;
use DotMD\HtmlToMarkdown\Converter\ParagraphConverter;
use DotMD\HtmlToMarkdown\Converter\PreformattedConverter;
use DotMD\HtmlToMarkdown\Converter\SemanticConverter;
use DotMD\HtmlToMarkdown\Converter\TableConverter;
use DotMD\HtmlToMarkdown\Converter\TextConverter;

final class Environment
{
    /** @var Configuration */
    protected $config;

    /** @var ConverterInterface[] */
    protected $converters = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new Configuration($config);
        $this->addConverter(new DefaultConverter());
    }

    public function getConfig(): Configuration
    {
        return $this->config;
    }

    public function addConverter(ConverterInterface $converter): void
    {
        if ($converter instanceof ConfigurationAwareInterface) {
            $converter->setConfig($this->config);
        }

        foreach ($converter->getSupportedTags() as $tag) {
            $this->converters[$tag] = $converter;
        }
    }

    public function getConverterByTag(string $tag): ConverterInterface
    {
        if (isset($this->converters[$tag])) {
            return $this->converters[$tag];
        }

        return $this->converters[DefaultConverter::DEFAULT_CONVERTER];
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function createDefaultEnvironment(array $config = []): Environment
    {
        $environment = new static($config);

        $environment->addConverter(new BlockquoteConverter());
        $environment->addConverter(new CodeConverter());
        $environment->addConverter(new CommentConverter());
        $environment->addConverter(new DetailsConverter());
        $environment->addConverter(new DivConverter());
        $environment->addConverter(new EmphasisConverter());
        $environment->addConverter(new HardBreakConverter());
        $environment->addConverter(new HeaderConverter());
        $environment->addConverter(new HorizontalRuleConverter());
        $environment->addConverter(new ImageConverter());
        $environment->addConverter(new InlineFormatConverter());
        $environment->addConverter(new InputConverter());
        $environment->addConverter(new LinkConverter());
        $environment->addConverter(new ListBlockConverter());
        $environment->addConverter(new ListItemConverter());
        $environment->addConverter(new ParagraphConverter());
        $environment->addConverter(new PreformattedConverter());
        $environment->addConverter(new SemanticConverter());
        $environment->addConverter(new TableConverter());
        $environment->addConverter(new TextConverter());

        return $environment;
    }
}
