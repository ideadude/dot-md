# Dot MD - WordPress Markdown Export Plugin

Convert any WordPress post or page to Markdown by simply adding `.md` to the URL.

## Description

Dot MD makes it incredibly easy to get a Markdown version of any post or page on your WordPress site. This is particularly useful for:

- AI assistants that need to consume your content
- Content archival and backups
- Migrating content between platforms
- Sharing content in developer-friendly formats

## Features

- **Simple URL Pattern**: Just add `/md/` to the end of any post or page URL
- **Smart Caching**: Generated Markdown is cached for performance and automatically refreshed when content updates
- **Complete Conversion**: Converts rendered HTML content including:
  - Headers
  - Lists (ordered and unordered)
  - Links and images
  - Code blocks
  - Tables
  - Blockquotes
  - And more!
- **Metadata Included**: Each Markdown file includes:
  - Post title
  - Publication date
  - Author
  - Permalink
  - Excerpt (if available)
- **Admin Bar Integration**: Convenient "Download as Markdown" link in the admin bar

## Installation

1. Upload the `dot-md` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it! No configuration needed.

## Usage

To get a Markdown version of any post or page:

1. Visit the post or page normally
2. Add `/md/` to the end of the URL
3. The Markdown file will automatically download

**Example:**
- Original URL: `https://example.com/my-blog-post/`
- Markdown URL: `https://example.com/my-blog-post/md/`

Alternatively, click "Download as Markdown" in the admin bar when viewing any post or page.

## How It Works

The plugin uses a rewrite endpoint to detect when `/md/` is added to a URL. When triggered:

1. It retrieves the post content with all WordPress filters applied (same as what visitors see)
2. Converts the HTML to clean Markdown using a customized version of the league/html-to-markdown library
3. Adds metadata header (title, date, author, URL)
4. Caches the result for 1 week
5. Serves the file with proper headers for download

The cache is automatically cleared whenever the post is updated.

## Technical Details

### Dependencies

The plugin includes a namespaced version of the league/html-to-markdown library to avoid conflicts with other plugins. The namespace `DotMD\HtmlToMarkdown` ensures compatibility.

### Filters

**`dotmd_markdown_output`**
Filter the final Markdown output before it's served.

```php
add_filter( 'dotmd_markdown_output', function( $markdown, $post ) {
    // Add custom footer
    $markdown .= "\n\nCustom footer text";
    return $markdown;
}, 10, 2 );
```

### Cache

Markdown is cached using WordPress transients with the key pattern:
```
dotmd_{post_id}_v{plugin_version}
```

Cache is automatically cleared on:
- Post update
- Post deletion

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## Credits

- Built on top of [league/html-to-markdown](https://github.com/thephpleague/html-to-markdown)
- Inspired by the need to make WordPress content easily accessible to AI assistants

## License

GPL2 - Same as WordPress

## Changelog

### 0.1
- Initial release
- Basic Markdown conversion
- Caching implementation
- Admin bar integration
