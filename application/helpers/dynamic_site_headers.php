<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

/**
 * This is the primary class for the SEO Helpers.
 *
 * It uses standard WordPress hooks and helpers for the different APIs it interfaces with.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 3.0.0
 */

class dynamic_site_headers {

	/**
	 * Public variable for the host of the API we are suposed to request the headers from.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
		public $host = NULL;

	/**
	 * Public variable for the company ID we'll be giving to the API.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var integer
	 */
		public $company_id = 0;

	/**
	 * Public array of the parameters we'll be submitting to the API.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
		public $parameters = array();

	/**
	 * Public array of the headers returned form the API.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
		public $headers = array();

	/**
	 * Public array all requests made within the instance of the object.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
		public $request_stack = array();

	/**
	 * Public variable for the host of the API we are suposed to request the headers from.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
		public $discourage_seo = NULL;

	/**
	 * Sets up object properties and ties into the WordPress procedural hooks. PHP 5 style constructor.
	 *
	 * @since 3.0.0
	 * @return void
	 */
		function __construct( $host , $company_id , $parameters, $discourage ) {
			if( substr($host, 0, 7) == 'http://' ){
				$this->host = $host;
			} else {
				$this->host = 'http://' . $host;
			}
			$this->company_id = $company_id;
			$this->parameters = $parameters;
            $this->discourage_seo = $discourage;
			$this->get_headers();
			if( $this->headers != false ) {
				add_filter( 'wp_title' , array( &$this , 'set_title' ) );
				add_action( 'wp_head' , array( &$this , 'set_head_information' ) , 1 );
			}
		}

	/**
	 * Attempt to get the title and meta information from the VMS.
	 *
	 * @since 3.0.0
	 * @return void
	 */
		function get_headers() {

			$taxonomy = isset( $this->parameters[ 'taxonomy' ] ) ? $this->parameters[ 'taxonomy' ] : 'inventory';
			$sale_class = isset( $this->parameters[ 'saleclass' ] ) ? $this->parameters[ 'saleclass' ] : 'All';
			$year = isset( $this->parameters[ 'year' ] ) ? $this->parameters[ 'year' ] : false;
			$make = isset( $this->parameters[ 'make' ] ) ? urlencode( $this->parameters[ 'make' ] ) : 'All';
			$model = isset( $this->parameters[ 'model' ] ) ? urlencode( $this->parameters[ 'model' ] ) : 'All';
			$trim = isset( $this->parameters[ 'trim' ] ) ? urlencode( $this->parameters[ 'trim' ] ) : 'All';
			$city = isset( $this->parameters[ 'city' ] ) ? urlencode( $this->parameters[ 'city' ] ) : false;
			$state = isset( $this->parameters[ 'state' ] ) ? urlencode( $this->parameters[ 'state' ] ) : false;
			$vin = isset( $this->parameters[ 'vin' ] ) ? $this->parameters[ 'vin' ] : false;

			$base = $year != false ? $year : $sale_class;
			if( $year == false ) {
				$url = $this->host . '/' . $this->company_id . '/seo_helpers.json?cu=/' . $taxonomy . '/' . $base . '/All/' . $make . '/' . $model . '/' . $city . '/' . $state . '/';
			} else {
				$url = $this->host . '/' . $this->company_id . '/seo_helpers.json?cu=/' . $taxonomy . '/' . $base . '/' . $make . '/' . $model . '/' . $vin . '/' . $city . '/' . $state . '/';
			}

			if( strtolower( $trim ) != 'all' ) {
				$url .= '?trim=' . urlencode( $trim );
			}
			$url .= '?' . rawurlencode(http_build_query( $this->parameters , '' , '&' ));
			$request_handler = new http_request( $url , 'dynamic_site_headers' );
			$this->request_stack[] = $url;
			$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file( true );
			$body = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : false;
			if( $body ) {
				$this->headers[ 'page_title' ] = rawurldecode( $body->page_title );
				$this->headers[ 'page_description' ] = $body->page_description;
				$this->headers[ 'page_keywords' ] = $body->page_keywords;
				$this->headers[ 'follow' ] = $body->follow;
				$this->headers[ 'index' ] = $body->index;
			} else {
				$headers = false;
			}
		}

	/**
	 * Set the new title we get from the API.
	 *
	 * @since 3.0.0
	 * @return string The new title from the API.
	 */
		function set_title() {
			return $this->headers[ 'page_title' ] . ' ';
		}

	/**
	 * Set the header information for the site we got back from the VMS.
	 *
	 * @since 3.0.0
	 * @return void
	 */
		function set_head_information() {

			if( isset( $this->headers[ 'page_description' ] ) && !empty( $this->headers[ 'page_description' ] ) ) {
				echo '<meta name="Description" content="' . $this->headers[ 'page_description' ] . '" />' . "\n";
			}
			if( isset( $this->headers[ 'page_keywords' ] ) && !empty( $this->headers[ 'page_keywords' ] ) ) {
				echo '<meta name="Keywords" content="' . $this->headers[ 'page_keywords' ] . '" />' . "\n";
			}
            if( empty( $this->discourage_seo ) ) {
			    $robots = array();
			    if( isset( $this->headers[ 'follow' ] ) && $this->headers[ 'follow' ] != true ) {
				    $robots[] = 'nofollow';
			    }
			    if( isset( $this->headers[ 'index' ] ) && $this->headers[ 'index' ] != true ) {
				    $robots[] = 'noindex';
			    }
			    if( !empty( $robots ) ) {
				    echo '<meta name="robots" content="' . implode( $robots , ',' ) . '" />' . "\n";
			    }
            } else {
                echo '<meta name="robots" content="nofollow,noindex" />' . "\n";
            }
		}

}

?>
