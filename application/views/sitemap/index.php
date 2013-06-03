<?php

	$plugin_url = $this->plugin_information[ 'PluginURL' ];

	$sitemap_information = $sitemap_handler->cached() ? $sitemap_handler->cached() : $sitemap_handler->get_file();
	$sitemap_data = isset( $sitemap_information[ 'body' ] ) ? json_decode( $sitemap_information[ 'body' ] ) : false;

	$company_information = json_decode( $company_information[ 'body' ] );
	$city = $company_information->seo->city;
	$state = $company_information->seo->state;

	$new_array = array();
	$new_vehicles = array();

	array_push( $new_array, array( home_url( '/inventory/New/' ), '.9' ) );

	$used_array = array();
	$used_vehicles = array();

	array_push( $used_array, array( home_url( '/inventory/Used/' ), '.9' ) );
	array_push( $used_array, array( home_url( '/inventory/Used/?certified=true' ), '.9' ) );

	$sitemap_output = '';
	$date_raw = date("Y-m-d");
	$last_mod = date("Y-m-d", strtotime('-1 day', strtotime($date_raw)));

	if ( !empty( $sitemap_data ) ) {

		foreach( $sitemap_data as $data ) {

			if ( $data->sale_class == 'New' ) {

				$clean_model = urlencode( $data->model );
				$clean_trim = urlencode( $data->trim );

				$make_value = home_url( '/inventory/New/' . $data->make . '/' );
				$model_value = home_url( '/inventory/New/' . $data->make . '/' . $clean_model . '/' );
				if ( !empty( $clean_trim ) ) {
					$trim_value = home_url( '/inventory/New/' . $data->make . '/' . $clean_model . '/?trim=' . $clean_trim );
				} else {
					$trim_value = '';
				}

				$vehicle_value = home_url( '/inventory/' . $data->year . '/' . $data->make . '/' . $clean_model . '/' . $state . '/' . $city . '/' . $data->vin . '/' );

				if ( !in_array( array( $make_value, '.9' ), $new_array ) ) {
					array_push( $new_array, array( $make_value, '.9' ) );
				}

				if ( !in_array( array( $model_value, '.8' ), $new_array ) ) {
					array_push( $new_array, array( $model_value, '.8' ) );
				}

				if ( !in_array( array( $trim_value, '.7' ), $new_array ) && !empty( $trim_value ) ) {
					array_push( $new_array, array( $trim_value, '.7' ) );
				}
				array_push( $new_vehicles, array( 'URL' => $vehicle_value, 'MOD' => $data->updated_at, 'PRI' => '.7' ) );

			} else {

				$clean_model = urlencode( $data->model );
				$clean_trim = urlencode( $data->trim );

				$make_value = home_url( '/inventory/Used/' . $data->make . '/' );
				$model_value = home_url( '/inventory/Used/' . $data->make . '/' . $clean_model . '/' );
				if ( !empty( $clean_trim ) ) {
					$trim_value = home_url( '/inventory/Used/' . $data->make . '/' . $clean_model . '/?trim=' . $clean_trim );
				} else {
					$trim_value = '';
				}

				$vehicle_value = home_url( '/inventory/' . $data->year . '/' . $data->make . '/' . $clean_model . '/' . $state . '/' . $city . '/' . $data->vin . '/' );

				if ( !in_array( array( $make_value, '.8' ), $used_array ) ) {
					array_push( $used_array, array( $make_value, '.8' ) );
				}

				if ( !in_array( array( $model_value, '.7' ), $used_array ) ) {
					array_push( $used_array, array( $model_value, '.7' ) );
				}

				if ( !in_array( array( $trim_value, '.7' ), $used_array ) && !empty( $trim_value ) ) {
					array_push( $used_array, array( $trim_value, '.7' ) );
				}
				array_push( $used_vehicles, array( 'URL' => $vehicle_value, 'MOD' => $data->updated_at, 'PRI' => '.7' ) );

			}

		}

		if ( $this->taxonomy == 'new-vehicle-sitemap' ) {

			foreach( $new_array as $new ) {

				$value = '<url>' . "\n";
				$value .= '<loc>' . $new[0] . '</loc>' . "\n";
				$value .= '<lastmod>' . $last_mod . ' 20:00' . '</lastmod>' . "\n";
				$value .= '<changefreq>daily</changefreq>' . "\n";
				$value .= '<priority>' . $new[1] . '</priority>' . "\n";
				$value .= '</url>' . "\n";

				$sitemap_output .= $value;
			}

			foreach( $new_vehicles as $vehicle ) {

				$value = '<url>' . "\n";
				$value .= '<loc>' . $vehicle['URL'] . '</loc>' . "\n";
				$value .= '<lastmod>' . $vehicle['MOD'] . '</lastmod>' . "\n";
				$value .= '<changefreq>daily</changefreq>' . "\n";
				$value .= '<priority>' . $vehicle['PRI'] . '</priority>' . "\n";
				$value .= '</url>' . "\n";

				$sitemap_output .= $value;

			}

			display_output( $sitemap_output, $plugin_url );

		} else {

			foreach( $used_array as $used ) {

				$value = '<url>' . "\n";
				$value .= '<loc>' . $used[0] . '</loc>' . "\n";
				$value .= '<lastmod>' . $last_mod . ' 20:00' . '</lastmod>' . "\n";
				$value .= '<changefreq>daily</changefreq>' . "\n";
				$value .= '<priority>' . $used[1] . '</priority>' . "\n";
				$value .= '</url>' . "\n";

				$sitemap_output .= $value;
			}

			foreach( $used_vehicles as $vehicle ) {

				$value = '<url>' . "\n";
				$value .= '<loc>' . $vehicle['URL'] . '</loc>' . "\n";
				$value .= '<lastmod>' . $vehicle['MOD'] . '</lastmod>' . "\n";
				$value .= '<changefreq>daily</changefreq>' . "\n";
				$value .= '<priority>' . $vehicle['PRI'] . '</priority>' . "\n";
				$value .= '</url>' . "\n";

				$sitemap_output .= $value;

			}

			display_output( $sitemap_output, $plugin_url );

		}

	}

	function display_output( $output, $style_url ){

		//header( 'HTTP/1.0 200 OK', true, 200 );
		// Prevent the search engines from indexing the XML Sitemap.
		header( 'X-Robots-Tag: noindex, follow', true );
		header( 'Content-Type: text/xml' );
		echo '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>';
		$stylesheet = '<?xml-stylesheet type="text/xsl" href="' . $style_url . '/application/views/sitemap/inc/xml-sitemap-xsl.php"?>';
		echo $stylesheet . "\n";

		echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
		echo 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
		echo 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		echo $output . "\n";
		echo '</urlset>';

	}

?>
