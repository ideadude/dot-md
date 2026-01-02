<?php
/*
Plugin Name: Dot MD
Plugin URI: https://www.paidmembershipspro.com/
Description: Add .md to the end of a post URL to download a Markdown version. Makes it easy for AI to consume your content.
Version: 0.3
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
License: GPL2
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'DOTMD_VERSION', '0.3' );
define( 'DOTMD_DIR', dirname( __FILE__ ) );
define( 'DOTMD_URL', plugin_dir_url( __FILE__ ) );

// Load the HTML to Markdown library
require_once DOTMD_DIR . '/lib/HtmlConverterInterface.php';
require_once DOTMD_DIR . '/lib/HtmlConverter.php';
require_once DOTMD_DIR . '/lib/Configuration.php';
require_once DOTMD_DIR . '/lib/ConfigurationAwareInterface.php';
require_once DOTMD_DIR . '/lib/PreConverterInterface.php';
require_once DOTMD_DIR . '/lib/Environment.php';
require_once DOTMD_DIR . '/lib/ElementInterface.php';
require_once DOTMD_DIR . '/lib/Element.php';
require_once DOTMD_DIR . '/lib/Coerce.php';

// Load all converter classes
require_once DOTMD_DIR . '/lib/Converter/ConverterInterface.php';
require_once DOTMD_DIR . '/lib/Converter/DefaultConverter.php';
require_once DOTMD_DIR . '/lib/Converter/BlockquoteConverter.php';
require_once DOTMD_DIR . '/lib/Converter/CodeConverter.php';
require_once DOTMD_DIR . '/lib/Converter/CommentConverter.php';
require_once DOTMD_DIR . '/lib/Converter/DetailsConverter.php';
require_once DOTMD_DIR . '/lib/Converter/DivConverter.php';
require_once DOTMD_DIR . '/lib/Converter/EmphasisConverter.php';
require_once DOTMD_DIR . '/lib/Converter/HardBreakConverter.php';
require_once DOTMD_DIR . '/lib/Converter/HeaderConverter.php';
require_once DOTMD_DIR . '/lib/Converter/HorizontalRuleConverter.php';
require_once DOTMD_DIR . '/lib/Converter/IframeConverter.php';
require_once DOTMD_DIR . '/lib/Converter/ImageConverter.php';
require_once DOTMD_DIR . '/lib/Converter/InlineFormatConverter.php';
require_once DOTMD_DIR . '/lib/Converter/InputConverter.php';
require_once DOTMD_DIR . '/lib/Converter/LinkConverter.php';
require_once DOTMD_DIR . '/lib/Converter/ListBlockConverter.php';
require_once DOTMD_DIR . '/lib/Converter/ListItemConverter.php';
require_once DOTMD_DIR . '/lib/Converter/ParagraphConverter.php';
require_once DOTMD_DIR . '/lib/Converter/PreformattedConverter.php';
require_once DOTMD_DIR . '/lib/Converter/SemanticConverter.php';
require_once DOTMD_DIR . '/lib/Converter/TextConverter.php';
require_once DOTMD_DIR . '/lib/Converter/TableConverter.php';

/**
 * Register the .md rewrite endpoint on activation
 */
function dotmd_activation() {
	add_rewrite_endpoint( 'md', EP_PERMALINK | EP_PAGES );
	dotmd_add_download_rewrite();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dotmd_activation' );

/**
 * Initialize the .md endpoint
 */
function dotmd_init() {
	add_rewrite_endpoint( 'md', EP_PERMALINK | EP_PAGES );
	dotmd_add_download_rewrite();
}
add_action( 'init', 'dotmd_init' );

/**
 * Add custom rewrite rule for .md download
 * Note: This requires .htaccess or nginx configuration to rewrite .md URLs
 */
function dotmd_add_download_rewrite() {
	// Not used - handled by .htaccess instead
}

/**
 * Register the custom query var for download parameter
 */
function dotmd_query_vars( $vars ) {
	$vars[] = 'download';
	return $vars;
}
add_filter( 'query_vars', 'dotmd_query_vars' );

/**
 * Remove the endpoint on deactivation
 */
function dotmd_deactivation() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dotmd_deactivation' );

/**
 * Detect .md endpoint and return markdown file
 */
function dotmd_template_redirect() {
	global $wp_query, $post;

	// Check if the md endpoint is set
	if ( ! isset( $wp_query->query_vars['md'] ) ) {
		return;
	}

	// Verify post exists
	if ( empty( $post->ID ) ) {
		return;
	}

	// Check if download parameter is set (from .htaccess rewrite)
	$is_download = isset( $wp_query->query_vars['download'] ) && $wp_query->query_vars['download'] == '1';

	// Check if we have a cached version
	$cache_key = 'dotmd_' . $post->ID . '_v' . DOTMD_VERSION;
	$markdown = get_transient( $cache_key );

	// If no cache, generate markdown
	if ( false === $markdown ) {
		$markdown = dotmd_generate_markdown( $post );

		// Cache for 1 week (will be cleared on post update)
		set_transient( $cache_key, $markdown, WEEK_IN_SECONDS );
	}

	// Set headers - download if download=1 parameter, display otherwise
	header( 'Content-Type: text/plain; charset=utf-8' );

	if ( $is_download ) {
		// .md extension (via .htaccess) triggers download
		header( 'Content-Disposition: attachment; filename="' . $post->post_name . '.md"' );
	} else {
		// /md/ endpoint displays in browser
		header( 'Content-Disposition: inline; filename="' . $post->post_name . '.md"' );
	}

	header( 'Content-Length: ' . strlen( $markdown ) );

	// Output markdown
	echo $markdown;

	exit;
}
add_action( 'template_redirect', 'dotmd_template_redirect' );

/**
 * Clean up extra whitespace in markdown output
 *
 * @param string $markdown The markdown content
 * @return string The cleaned markdown
 */
function dotmd_cleanup_whitespace( $markdown ) {
	// Split into lines
	$lines = explode( "\n", $markdown );

	// Remove leading whitespace from each line (except code blocks)
	$in_code_block = false;
	$cleaned_lines = array();

	foreach ( $lines as $line ) {
		// Detect code block boundaries
		if ( strpos( $line, '```' ) === 0 ) {
			$in_code_block = ! $in_code_block;
			$cleaned_lines[] = $line;
			continue;
		}

		// Don't trim whitespace inside code blocks
		if ( $in_code_block ) {
			$cleaned_lines[] = $line;
			continue;
		}

		// Trim leading whitespace from non-code lines
		$cleaned_lines[] = ltrim( $line );
	}

	// Join back together
	$markdown = implode( "\n", $cleaned_lines );

	// Remove excessive blank lines (more than 2 consecutive)
	$markdown = preg_replace( "/\n{3,}/", "\n\n", $markdown );

	return $markdown;
}

/**
 * Generate markdown from post content
 *
 * @param WP_Post $post The post object
 * @return string The markdown content
 */
function dotmd_generate_markdown( $post ) {
	// Get the rendered post content with all filters applied
	$content = apply_filters( 'the_content', $post->post_content );

	// Initialize the HTML to Markdown converter
	$converter = new \DotMD\HtmlToMarkdown\HtmlConverter( array(
		'header_style'    => 'atx',
		'strip_tags'      => false,
		'remove_nodes'    => 'script style',
		'hard_break'      => true,
		'list_item_style' => '-',
	) );

	// Build the complete markdown document
	$markdown = '';

	// Add title
	$markdown .= '# ' . $post->post_title . "\n\n";

	// Add metadata
	$markdown .= '_Published: ' . get_the_date( 'F j, Y', $post ) . '_' . "\n\n";
	$markdown .= '_Author: ' . get_the_author_meta( 'display_name', $post->post_author ) . '_' . "\n\n";
	$markdown .= '_URL: ' . get_permalink( $post->ID ) . '_' . "\n\n";

	// Add excerpt if available
	if ( ! empty( $post->post_excerpt ) ) {
		$markdown .= '**Excerpt:** ' . $post->post_excerpt . "\n\n";
	}

	// Add horizontal rule
	$markdown .= "---\n\n";

	// Convert content to markdown
	try {
		$content_markdown = $converter->convert( $content );
		// Clean up extra whitespace
		$content_markdown = dotmd_cleanup_whitespace( $content_markdown );
		$markdown .= $content_markdown;
	} catch ( Exception $e ) {
		// If conversion fails, fall back to plain text
		$markdown .= strip_tags( $content );
	}

	// Add footer
	$markdown .= "\n\n---\n\n";
	$markdown .= '_Generated from: ' . get_permalink( $post->ID ) . '_' . "\n";

	// Allow filtering the final markdown
	return apply_filters( 'dotmd_markdown_output', $markdown, $post );
}

/**
 * Clear markdown cache when post is updated
 *
 * @param int $post_id The post ID
 */
function dotmd_clear_cache( $post_id ) {
	// Don't clear cache for autosaves or revisions
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Clear the transient cache
	$cache_key = 'dotmd_' . $post_id . '_v' . DOTMD_VERSION;
	delete_transient( $cache_key );
}
add_action( 'save_post', 'dotmd_clear_cache' );
add_action( 'delete_post', 'dotmd_clear_cache' );

/**
 * Add a link to the admin bar for easy access to markdown version
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function dotmd_admin_bar_link( $wp_admin_bar ) {
	// Only show on singular posts/pages
	if ( ! is_singular() ) {
		return;
	}

	global $post;

	$wp_admin_bar->add_node( array(
		'id'    => 'dotmd-download',
		'title' => 'Download as Markdown',
		'href'  => trailingslashit( get_permalink( $post->ID ) ) . 'md/',
		'meta'  => array(
			'title' => 'Download this post as a Markdown file',
		),
	) );
}
add_action( 'admin_bar_menu', 'dotmd_admin_bar_link', 100 );
