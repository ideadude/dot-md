=== Dot MD ===
Contributors: ideadude
Tags: markdown, export, ai, content, conversion
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert any WordPress post or page to Markdown by adding .md or /md/ to the URL.

== Description ==

A simple plugin that makes it easy to get a Markdown version of any post or page on your WordPress site. Perfect for AI assistants, content migration, and developer workflows.

= Features =

* **Two URL Patterns**: View in browser with `/md/` or download with `.md`
* **Smart Caching**: Generated Markdown is cached and auto-refreshed on content updates
* **Complete Conversion**: Converts headers, lists, links, images, code blocks, tables, blockquotes, and more
* **Metadata Included**: Title, publication date, author, permalink, and excerpt
* **Admin Bar Link**: Convenient "Download as Markdown" button in admin bar
* **Developer Friendly**: Filterable output and clean code

= Usage =

**View in Browser:**
Add `/md/` to any post URL:
`https://example.com/my-post/md/`

**Download File:**
Add `.md` to any post URL (requires web server configuration):
`https://example.com/my-post.md`

Or click "Download as Markdown" in the admin bar when viewing any post.

= Use Cases =

* Share content with AI assistants (Claude, ChatGPT, etc.)
* Archive and backup WordPress content
* Migrate content between platforms
* Export for static site generators
* Developer documentation workflows

== Installation ==

1. Upload the `dot-md` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. *Optional:* Configure web server rewrite rules for `.md` download support (see FAQ)

== Frequently Asked Questions ==

= How do I enable .md downloads? =

The `/md/` endpoint works out of the box. To enable `.md` downloads, add a rewrite rule to your web server configuration:

**For Apache (.htaccess):**

Add this rule before the standard WordPress rules:

`# Custom rule for .md downloads
RewriteRule ^(.+)\.md$ /$1/md/?download=1 [QSA,L]`

**For Nginx:**

Add this to your server block:

`rewrite ^/(.+)\.md$ /$1/md/?download=1 last;`

= Does this work with custom post types? =

Yes! Any publicly accessible post or page with a permalink will work.

= How is the Markdown cached? =

Markdown is cached using WordPress transients for 1 week. The cache is automatically cleared when you update or delete the post.

= Can I customize the Markdown output? =

Yes! Use the `dotmd_markdown_output` filter:

`add_filter( 'dotmd_markdown_output', function( $markdown, $post ) {
    // Add custom content
    $markdown .= "\n\nCustom footer";
    return $markdown;
}, 10, 2 );`

= What HTML elements are supported? =

The plugin converts most common HTML elements including headers (h1-h6), paragraphs, lists, links, images, code blocks, tables, blockquotes, emphasis, and more.

= Does this affect SEO or create duplicate content? =

No. The Markdown endpoint returns plain text, not HTML, and includes proper metadata. Search engines treat this as a different content format.

== Screenshots ==

1. Admin bar link for easy access to Markdown version
2. Example Markdown output with metadata header
3. Markdown file ready for download

== Changelog ==

= 0.1 =
* Initial release
* Basic Markdown conversion with caching
* Admin bar integration
* Support for both view (/md/) and download (.md) modes

== Upgrade Notice ==

= 0.1 =
Initial release of Dot MD.

== Technical Details ==

**Conversion Library:** Uses a namespaced version of league/html-to-markdown to avoid plugin conflicts.

**Cache Key Pattern:** `dotmd_{post_id}_v{plugin_version}`

**Supported Post Types:** All public post types with permalinks

**Performance:** Cached Markdown is served instantly. First generation may take 100-500ms depending on content size.

== Support ==

For issues, questions, or contributions, visit:
https://github.com/ideadude/dot-md
