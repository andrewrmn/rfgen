<?php
/**
 * Pagination Helper Class
 */

namespace WishListMember;

class Pagination {
	public $items;
	public $per_page;
	public $pages;
	public $current;
	public $variable;
	public $url;
	public $quickjump_url;
	public $from;
	public $to;
	public $prev;
	public $next;
	public $per_page_options;

	/**
	 * Pagination Constructor
	 * @param int $items Number of items
	 * @param int $per_page Number of items per page
	 * @param int $current Current page
	 * @param string $variable Page offset variable to use for links generated
	 * @param string $url Base URL
	 * @param $per_page_options array (optional) Per page options. Default [ 10, 25, 50, 100, 250, 500 ];
	 */
	function __construct( $items, $per_page, $current, $variable, $url, $per_page_options = [] ) {
		$this->items = $items;
		$this->per_page = (int) $per_page ?: PHP_INT_MAX;
		$this->pages = ceil( $items / $this->per_page );
		$current = max( (int) $current, 1 );
		$this->current = $current > $this->pages ? $this->pages : $current;

		$this->variable = $variable;
		$this->url = $url;
		$this->quickjump_url = add_query_arg( 'offset', '%d', $url );

		$this->from = ( $this->current - 1 ) * $this->per_page + 1;
		if( $this->from > $items ) $this->from = $items;

		$to = $this->from + $this->per_page - 1;
		$this->to = $to > $items ? $items : $to;

		$this->prev = $current - 1;
		if( $this->prev < 1 ) $this->prev = $this->pages; // rotate

		$this->next = $current + 1;
		if( $this->next > $this->pages ) $this->next = 1; // rotate

		$this->per_page_options	= !is_array( $per_page_options ) ? [ 10, 25, 50, 100, 200, __( 'Show All', 'wishlist-member' ) ] : $per_page_options;

	}

	/**
	 * Get Pagination HTML
	 * @return string HTML Markup
	 */
	public function get_html() {
		if( $this->items < 1 ) {
			return '<div class="pagination pull-right"></div>';
		}
		if( $this->pages > 1) {
			$markup = sprintf( '
				<div class="pagination pull-right">
					<div class="input-group">
						<div class="input-group-prepend">
								%s
								%s
						</div>
						%s
						<div class="input-group-append">
							<span class="mt-9px"> of %d</span>
							%s
						</div>

					</div>
				</div>
			', $this->get_range_markup(), $this->get_prev_link_markup(), $this->get_input_markup(), $this->pages, $this->get_next_link_markup() );
		} else {
			$markup = sprintf( '
				<div class="pagination pull-right">
					<div class="input-group">
						<div class="input-group-prepend">
								%s
						</div>
					</div>
				</div>
			', $this->get_range_markup() );
		}
		return $markup;
	}

	private function get_prev_link() {
		return $this->prev ? add_query_arg( $this->variable, $this->prev, $this->url ) : remove_query_arg( $this->variable, $this->url );
	}

	private function get_next_link() {
		return $this->next ? add_query_arg( $this->variable, $this->next, $this->url ) : remove_query_arg( $this->variable, $this->url );
	}

	private function get_range_markup( $per_page_query_var = 'howmany' ) {
		$per_page_options_markup = '';
		foreach( $this->per_page_options AS $x ) {
			$per_page_options_markup .= sprintf( '<a class="dropdown-item" target="_parent" href="%s">%s</a>', add_query_arg( $per_page_query_var, $x, $this->url ), $x );
		}
		$markup = '<span class="text-muted pr-2">
			<div role="presentation" class="dropdown mt-9px">
				<a href="#" class="dropdown-toggle" id="drop-page" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">%d - %d</a> of %d
				<ul class="dropdown-menu" id="menu1" aria-labelledby="drop-page">%s</ul>
			</div>
		</span>';
		$markup = sprintf( $markup, $this->from, $this->to, $this->items, $per_page_options_markup );
		return $markup;
	}

	private function get_prev_link_markup() {
		if( $this->current > 1 ) {
			$markup = sprintf( '<a href="%s" class="mt-6px"><i class="wlm-icons md-26">first_page</i></a>', remove_query_arg( $this->variable, $this->url ) );
		} else {
			$markup = '<a class="mt-6px text-muted disabled" disabled="disabled"><i class="wlm-icons md-26">first_page</i></a>';
		}

		$markup .= sprintf( '<a href="%s" class="mt-6px"><i class="wlm-icons md-26">keyboard_arrow_left</i></a>', $this->get_prev_link() );

		return $markup;
	}

	private function get_next_link_markup() {
		$markup = sprintf( '<a href="%s" class="mt-6px"><i class="wlm-icons md-26">keyboard_arrow_right</i></a>', $this->get_next_link() );

		if( $this->current < $this->pages ) {
			$markup .= sprintf( '<a href="%s" class="mt-6px"><i class="wlm-icons md-26">last_page</i></a>', add_query_arg( $this->variable, $this->pages, $this->url ) );
		} else {
			$markup .= '<a class="mt-6px text-muted disabled" disabled="disabled"><i class="wlm-icons md-26">last_page</i></a>';
		}

		return $markup;
	}

	private function get_input_markup() {
		$markup = sprintf( '<input type="text" value="%d" data-orig="%d" class="form-control text-center pagination-pagenum" data-pages="%d" data-link="%s">', $this->current, $this->current, $this->pages, $this->quickjump_url );
		return $markup;
	}
}


