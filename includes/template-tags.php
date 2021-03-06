<?php
/**
 * Custom template tags for PippinsPlugins
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since PippinsPlugins 1.0
 */

/**
 * Shows labels on tutorials etc
 * @return [type] [description]
 */
function pp_post_footer() {

?>
	<footer>
	
			<?php if ( has_category( 'tutorials' ) ) : // posts are assigned to tutorials category ?>
			<a href="<?php the_permalink(); ?>">Learn now &rarr;</a>
			<?php else : ?>
				<a href="<?php the_permalink(); ?>">Read now &rarr;</a>
			<?php endif; ?>

			<?php if ( has_category( 'tutorials' ) || has_category( 'free-plugins' ) ) : ?>
			
			<div class="label-meta">

				<?php if ( has_category( 'tutorials' ) ) : // posts are assigned to tutorials category ?>

					<?php if ( has_category( 'free' ) ) : ?>
						<a href="<?php echo get_category_link( get_cat_ID( 'free' ) ); ?>" class="label free" title="View other free tutorials">Free</a>
					<?php endif; ?>	

					<?php if ( has_category( 'subscriber-only' ) ) : ?>
						<a href="<?php echo get_category_link( get_cat_ID( 'Subscriber Only' ) ); ?>" class="label subscriber-only" title="This tutorial is only available for subscribers">Subscriber Only</a>
					<?php endif; ?>	

					<?php if ( has_category( 'beginner' ) ) : ?>
						<a href="<?php echo get_category_link( get_cat_ID( 'beginner' ) ); ?>" class="label beginner" title="This tutorial requires a beginner skill level">Beginner</a>
					<?php endif; ?>	

					<?php if ( has_category( 'intermediate' ) ) : ?>
						<a href="<?php echo get_category_link( get_cat_ID( 'intermediate' ) ); ?>" class="label intermediate" title="This tutorial requires an intermediate skill level">Intermediate</a>
					<?php endif; ?>	

					<?php if ( has_category( 'advanced' ) ) : ?>
						<a href="<?php echo get_category_link( get_cat_ID( 'advanced' ) ); ?>" class="label advanced" title="This tutorial requires an advanced skill level">Advanced</a>
					<?php endif; ?>

					<?php
						$series_id        = get_post_meta( get_the_ID(), 'series_id', true );
						$series_permalink = get_permalink( $series_id );
					?>
					<?php if ( get_post_meta( get_the_ID(), 'series_id', true ) ) : ?>
						<a href="<?php echo $series_permalink; ?>" class="label series" title="This tutorial is part of a series">Part Of Series</a>
					<?php endif; ?>

				<?php endif; ?>
				
				<?php if ( has_category( 'free-plugins' ) ) : ?>
					<a href="<?php echo get_category_link( get_cat_ID( 'free-plugins' ) ); ?>" class="label free" title="This plugin is free">Free Plugin</a>
				<?php endif; ?>

			</div>
			<?php endif; ?>
		
	</footer>
	<?php

}

/**
 * Single post type information box
 */
function pp_single_post_type_info() {
	
	if ( ! ( is_singular( 'post' ) || is_singular( 'series' ) ) ) {
		return;
	}
	?>
	<aside class="box <?php echo get_post_type(); ?>-info">
		<p>
			<span>Posted On</span>
			<?php if ( 'series' == get_post_type() || 'post' == get_post_type() ) : ?>
				<?php printf( '<time datetime="%1$s">%2$s</time>',
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date() )
				); ?>
			<?php endif; ?>
		</p>

		<?php if ( in_array( 'doc_category', get_object_taxonomies( get_post_type() ) ) && affwp_categorized_blog() ) : ?>
			<p><span>Categories</span>
			<?php echo get_the_term_list( get_the_ID(), 'doc_category', '', '<br/>' ); ?>
			</p>
		<?php endif; ?>

		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && affwp_categorized_blog() ) : ?>
			<p><span>Categories</span>
			<?php echo get_the_term_list( get_the_ID(), 'category', '', '<br/>' ); ?>
			</p>
		<?php endif; ?>

		<?php if ( 'post' == get_post_type() ) : ?>
			<?php the_tags( '<p><span>Tags</span> ', ', ', '</p>' ); ?>
			<p>
				<span>Comments</span>
				<?php comments_popup_link( __( 'Leave a comment', 'pp' ), __( '1', 'pp' ), __( '%', 'pp' ) ); ?>
			</p>
		<?php endif; ?>

	</aside>
	<?php
}

/**
 * Filter excerpt
 */
function affwp_custom_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'affwp_custom_excerpt_more' );

/**
 * Remove purchase links from add-ons pages
 */
remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );

/**
 * Filter comment form
 */
function affwp_comment_form_defaults( $defaults ) {
	$defaults['title_reply'] 	= '';
	$defaults['label_submit']	= 'Go for it';

	return $defaults;
}
add_filter( 'comment_form_defaults', 'affwp_comment_form_defaults', 1 );

/**
 * Share purchase shortcode
 */
function affwp_show_sharing_buttons_after_purchase( $atts, $content = null ) {
	
	$success_page = edd_get_option( 'success_page' ) ? is_page( edd_get_option( 'success_page' ) ) : false;

	// return if no affiliate was rewarded via the select menu
	if ( ! $success_page )
		return;

	$content = affwp_share_box( '', 'I just purchased AffiliateWP, the best affiliate marketing plugin for WordPress!' );

	return $content;
}
add_shortcode( 'affwp_share_purchase', 'affwp_show_sharing_buttons_after_purchase' );


/**
 * Output custom icons - favicon & apple touch icon
 * @link https://github.com/audreyr/favicon-cheat-sheet
 */
function pp_favicons() {
?>
	<link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri() . '/images/favicon-152.png'; ?>">
	<?php 
}
add_action( 'wp_head', 'pp_favicons' );

/**
 * Make doc category pags show all posts
 */
function affwp_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() )
		return;
 	
 	// Make doc category pags show all posts
	if ( $query->is_tax( 'doc_category' ) ) {

		$query->set( 'posts_per_page', -1 );
			return;
	}

	// add-ons page
	if ( $query->is_post_type_archive( 'download' ) ) {

		// show all add-ons
		$query->set( 'posts_per_page', -1 );

		// don't show downloads that belong to RCP
		$term = get_term_by( 'slug', 'add-ons', 'download_category' );

		if ( $term ) {
			$args = array(
		       array(
		           'taxonomy' => 'download_category',
		           'field' => 'id',
		           'terms' => array( $term->term_id ),
		           'operator'=> 'NOT IN'
		       )
		    );

			$query->set( 'tax_query', $args );
		}
		

		return;
	}
}
add_action( 'pre_get_posts', 'affwp_pre_get_posts' );

/**
 * Get started now button
 * @param  string $text [description]
 * @return [type]       [description]
 */
function affwp_button( $text = '', $url = '', $size = 'large' ) {
	if ( ! ( $text && $url ) )
		return;
?>
	<a class="button <?php echo $size; ?>" href="<?php echo $url; ?>"><?php echo $text; ?></a>
<?php }

/**		
 * Render the_title
 * @since 1.0
 * Add filter similar to subheader to make modify title easier
*/
function affwp_the_title( $header = '' ) {
	$header = $header ? $header : get_the_title();
	echo apply_filters( 'affwp_the_title', '<h1>' . $header . '</h1>'  );
}	

/**
 * Filter the title for specific pages
 */
function affwp_the_title_filters( $title ) {
	global $wp_query;

	// search query
	if ( get_search_query() ) {
		if ( in_the_loop() ) {
			$title = the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		else {
			$title = __( '<h1>Search Results</h1>', 'pp' );
		}
	}

	// download category pages
	if ( is_tax( 'download_category' ) ) {
		if ( in_the_loop() ) {
			$title = the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		else {
			$title = sprintf( __( '<h1>%s</h1>', 'pp' ), single_cat_title( '', false ) );
		}

	}

	// normal category pages
	if ( is_category() ) {
		if ( in_the_loop() ) {
			$title = the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		else {
			$title = sprintf( __( '<h1>%s</h1>', 'pp' ), single_cat_title( '', false ) );
		}
	}

	// tag pages
	if ( is_tag() ) {
		if ( in_the_loop() ) {
			$title = the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		else {
			$title = sprintf( __( '<h1>Posts Tagged with: %s</h1>', 'pp' ), single_tag_title( '', false ) );
		}
	}

	return $title;
	
}
add_filter( 'affwp_the_title', 'affwp_the_title_filters' );



/**
 * Show shortcode without shortcode activating
 */
function affwp_show_shortcode( $atts, $content = '' ) {
	return $content;
}
add_shortcode( 'show_shortcode', 'affwp_show_shortcode' );

/**
 * Page header
 */
function pp_page_header( $header = '', $sub_header = '' ) {
	global $post;

	?>
	<?php do_action( 'affwp_page_header_before' ); ?>
	<header class="page-header">

		<?php if( is_home() ) : ?>
			<?php affwp_the_title( 'Blog' ); ?>
		<?php else : ?>

			<?php affwp_the_title( $header ); ?>

			<?php
				
				if ( ! $sub_header && isset( $post->ID ) && ! is_post_type_archive() ) {
					$sub_header = function_exists( 'get_the_subheading' ) && get_the_subheading() ? '<h2>' . get_the_subheading() . '</h2>' : '';
				}
				
				echo apply_filters( 'affwp_excerpt', $sub_header );
			?>

		<?php endif; ?>

		<?php do_action( 'affwp_page_header_end' ); ?>

	</header>
	<?php do_action( 'affwp_page_header_after' ); ?>
<?php }




/**
 * Filter the subheadings
 */
function affwp_modify_excerpts( $sub_header ) {
	global $wp_query;

	// search query
	if ( get_search_query() ) {
		$sub_header = sprintf( __( '<h2>You searched for <strong>%s</strong></h2>', 'pp' ), get_search_query() );
	}

	// normal category pages
	if ( is_category() ) {
		$term = $wp_query->queried_object;
		$sub_header = $term->description ? sprintf( '<h2>%s</h2>', $term->description ) : '';
	}

	// download category and documentation category sub headers
	if ( is_tax( 'download_category' ) || is_tax( 'doc_category' ) ) {
		$term = $wp_query->queried_object;

		if ( $term->description ) {
			$sub_header = sprintf( '<h2>%s</h2>', $term->description );
		}
	}


	return $sub_header;
}
add_filter( 'affwp_excerpt', 'affwp_modify_excerpts' );







/**
 * Add twitter custom timeline to testimonials page
 */
function affwp_testimonials_twitter_feed() {
	if ( ! is_page_template( 'page-templates/testimonials.php' ) )
		return;
	?>

	<a class="twitter-timeline" href="https://twitter.com/affwp/timelines/458773013576417280" data-widget-id="458774486909595648" data-border-color="#F7F7F7">Word on the street</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

	<?php
}
add_action( 'affwp_page_header_end', 'affwp_testimonials_twitter_feed' );



if ( ! function_exists( 'affwp_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since AffiliateWP 1.0
 *
 * @return void
 */
function pp_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => __( '&larr; Previous', 'pp' ),
		'next_text' => __( 'Next &rarr;', 'pp' ),
	) );

	if ( $links ) :

	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'pp' ); ?></h1>
		<div class="pagination loop-pagination">
			<?php echo $links; ?>
		</div><!-- .pagination -->
	</nav><!-- .navigation -->
	<?php
	endif;
}
endif;

if ( ! function_exists( 'affwp_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @since AffiliateWP 1.0
 *
 * @return void
 */
function affwp_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'pp' ); ?></h1>
		<div class="nav-links columns columns-2 clear">
			<?php
			if ( is_attachment() ) :
				previous_post_link( '%link', __( '<span class="meta-nav col">Published In</span>%title', 'pp' ) );
			else :
				previous_post_link('<div class="col">%link</div>');
				next_post_link('<div class="col">%link</div>');
				
				// previous_post_link( '%link', __( '<span class="meta-nav col">%title</span>', 'pp' ) );
				// next_post_link( '%link', __( '<span class="meta-nav col">%title</span>', 'pp' ) );
			endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

/**
 * Display navigation to next/previous post when applicable.
 *
 * @since 1.1.1
 *
 * @return void
 */
function affwp_single_post_nav() {
	?>

	<nav class="nav-links columns columns-2 clear">

	<?php
		$prev_post = get_adjacent_post( false, '', true );

		if ( ! empty( $prev_post ) ) {
			echo '<div class="nav-previous col"><a href="' . get_permalink( $prev_post->ID ) . '" title="' . $prev_post->post_title . '"><i class="icon-arrow-left"></i><span>' . $prev_post->post_title . '</span></a></div>';
		}

		$next_post = get_adjacent_post( false, '', false );

		if ( ! empty( $next_post ) ) {
			echo ' <div class="nav-next col"><a href="' . get_permalink( $next_post->ID ) . '" title="' . $next_post->post_title . '"><span>' . $next_post->post_title . '</span><i class="icon-arrow-right"></i></a></div>';
		}
	?>
	</nav>
	<?php
}


if ( ! function_exists( 'pp_posted_on' ) ) :
/**
 * Print HTML with meta information for the current post-date/time and author.
 *
 * @return void
 */
function pp_posted_on() {
	if ( is_sticky() && is_home() && ! is_paged() ) {
		echo '<span class="featured-post">' . __( 'Sticky', 'pp' ) . '</span>';
	}

	// Set up and print post meta information.
	// printf( '<span class="entry-date"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span> <span class="byline"><span class="author vcard"><a class="url fn n" href="%4$s" rel="author">%5$s</a></span></span>',
	// 	esc_url( get_permalink() ),
	// 	esc_attr( get_the_date( 'c' ) ),
	// 	esc_html( get_the_date() ),
	// 	esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
	// 	get_the_author()
	// );

	// printf( '<span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span> <span class="byline"><span class="author vcard"><a class="url fn n" href="%3$s" rel="author">%4$s</a></span></span>',
	// 	esc_attr( get_the_date( 'c' ) ),
	// 	esc_html( get_the_date() ),
	// 	esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
	// 	get_the_author()
	// );

	// printf( '<div class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></div>',
	// 	esc_attr( get_the_date( 'c' ) ),
	// 	esc_html( get_the_date() )
	// );

	printf( '<div class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></div>',
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
}
endif;

/**
 * Entry Meta
 *
 * @since       1.0
*/
if ( ! function_exists( 'pp_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own pp_entry_meta() to override in a child theme.
 *
 * @since 1.0
 */
function pp_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	//$categories_list = get_the_category_list( __( ', ', 'pp' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'pp' ) );

	// $date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
	// 	esc_url( get_permalink() ),
	// 	esc_attr( get_the_time() ),
	// 	esc_attr( get_the_date( 'c' ) ),
	// 	esc_html( get_the_date( 'M d, Y' ) )
	// );

	$date = sprintf( '<time class="entry-date" datetime="%3$s">%4$s</time>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date( 'M d, Y' ) )
	);

	// $author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
	// 	esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
	// 	esc_attr( sprintf( __( 'View all posts by %s', 'pp' ), get_the_author() ) ),
	// 	get_the_author()
	// );

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	// if ( $tag_list ) {
	// 	$utility_text = __( 'Posted on %1$s <span class="by-author">by %2$s</span> in %3$s and tagged %4$s.', 'pp' );
	// } elseif ( $categories_list ) {
	// 	$utility_text = __( '%1$s <span class="by-author">by %2$s</span> in %3$s.', 'pp' );
	// } else {
	// 	$utility_text = __( 'Posted on %1$s by <span class="by-author"> by %2$s</span>.', 'pp' );
	// }

	$utility_text = __( '%1$s', 'pp' );

	// printf(
	// 	$utility_text,
	// 	$date,
	// 	$author,
	// 	$categories_list,
	// 	$tag_list
	// );

	printf(
		$utility_text,
		$date
	//	$author,
	//	$categories_list,
	//	$tag_list
	);

}
endif;

/**
 * Find out if blog has more than one category.
 *
 * @since AffiliateWP 1.0
 *
 * @return boolean true if blog has more than 1 category
 */
function affwp_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'affwp_category_count' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'affwp_category_count', $all_the_cool_cats );
	}

	if ( 1 !== (int) $all_the_cool_cats ) {
		// This blog has more than 1 category so affwp_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so affwp_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in affwp_categorized_blog.
 *
 * @since AffiliateWP 1.0
 *
 * @return void
 */
function affwp_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'affwp_category_count' );
}
add_action( 'edit_category', 'affwp_category_transient_flusher' );
add_action( 'save_post',     'affwp_category_transient_flusher' );

/**
 * Display an optional post thumbnail.
 * 
 * @return void
*/
function pp_post_thumbnail( $size = 'thumbnail', $link = false ) {

	if ( post_password_required() || ! has_post_thumbnail() ) {
		return;
	}

	if ( $link ) : ?>
	
	<a title="<?php the_title_attribute(); ?>" class="post-thumbnail" href="<?php the_permalink(); ?>">
		<?php the_post_thumbnail( $size ); ?>
	</a>

   <?php elseif ( is_singular() ) : ?>

	<div class="post-thumbnail">
		<?php the_post_thumbnail( $size ); ?>
	</div>

	<?php else : ?>

	<a title="<?php the_title_attribute(); ?>" class="post-thumbnail" href="<?php the_permalink(); ?>">
		<?php the_post_thumbnail( $size ); ?>
	</a>

	<?php endif; 
}


/**
 * Returns the URL to upgrade a license from personal -> business or dev, or from business -> dev
 *
 * @since AffiliateWP 1.x
 *
 * @return string
*/
function affwp_get_license_upgrade_url( $type = '' ) {

	if ( ! function_exists( 'edd_get_checkout_uri' ) || ! $type ) {
		return home_url( '/pricing' );
	}

	$args = array(
		'edd_action' => 'upgrade_affwp_license',
		'type'		 => $type
	);

	return add_query_arg( $args, edd_get_checkout_uri() );
}

/**
 * Returns the URL to download an add on
 *
 * @since AffiliateWP 1.x
 *
 * @return string
*/
function affwp_get_add_on_download_url( $add_on_id = 0 ) {

	$args = array(
		'edd_action' => 'add_on_download',
		'add_on'     => $add_on_id,
	);

	return add_query_arg( $args, home_url() );
}
