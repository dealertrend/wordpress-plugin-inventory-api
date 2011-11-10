<?php

	function sort_trims( $a , $b ) {
		if( $a->msrp == 0 ) {
			return 1;
		} 
		if( $b->msrp == 0 ) {
			return 0;
		}
		if( $a->year >= $b->year ) {
			if( $a->year == $b->year && $a->msrp > $b->msrp ) {
				return 1;
			}
			if( $a->image_filename === NULL ) {
				return 1;
			}
			return -1;
		} else {
			return 1;
		}
	}

	$trim_data[ $last_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $last_year , 'api' => 2 ) );
	$trim_data[ $last_year ] = isset( $trim_data[ $last_year ][ 'body' ] ) ? json_decode( $trim_data[ $last_year ][ 'body' ] ) : NULL; 
	$trim_data[ $current_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $current_year , 'api' => 2 ) );
	$trim_data[ $current_year ] = isset( $trim_data[ $current_year ][ 'body' ] ) ? json_decode( $trim_data[ $current_year ][ 'body' ] ) : NULL;
	$trim_data[ $next_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $next_year , 'api' => 2 ) );
	$trim_data[ $next_year ] = isset( $trim_data[ $next_year ][ 'body' ] ) ? json_decode( $trim_data[ $next_year ][ 'body' ] ) : NULL;

	$trim_data[ $last_year ] = is_array( $trim_data[ $last_year ] ) ? $trim_data[ $last_year ] : array(); 
	$trim_data[ $current_year ] = is_array( $trim_data[ $current_year ] ) ? $trim_data[ $current_year ] : array();
	$trim_data[ $next_year ] = is_array( $trim_data[ $next_year ] ) ? $trim_data[ $next_year ] : array();

	$trims = array_merge( $trim_data[ $next_year ] , $trim_data[ $current_year ] , $trim_data[ $last_year ] );

	$names_and_prices = array();
	foreach( $trims as $key => $trim ) {
		$important_stuff = array( $trim->name_variation );
		if( ! in_array( $important_stuff , $names_and_prices ) ) {
			$names_and_prices[] = $important_stuff;
		} else {
			unset( $trims[ $key ] );
		}
	}

	foreach( $trims as $trim ) {
		$options[] = (object) array( 'acode' => $trim->acode , 'mfg_code' => $trim->mfg_code , 'msrp' => $trim->msrp );
	}

	usort( $trims , 'sort_trims' );

	$trim = $trims[ 0 ];

	function make_transparent( $url ) {
		return preg_replace( '/IMG=(.*)\.\w{3,4}/i', 'IMG=\1.png' , $url );
	}
	
	$trim->msrp = '$' . number_format( $trim->msrp , 0 , '.' , ',' );
	$header = '<h2><a href="/showcase/">Showcase</a> &rsaquo; <a href="/showcase/' . $make . '">' . $make . '</a> &rsaquo; ' . $model . '</h2>';
	
	$fuel_economy = $vehicle_reference_system->get_fuel_economy( $trim->acode )->please();

  function sort_fuel_economies( $a , $b ) {
    $a_sum = $a->city_mpg + $a->highway_mpg;
    $b_sum = $b->city_mpg + $b->highway_mpg;
    if( $a_sum > $b_sum ) {
      return -1;
    } elseif( $a_sum < $b_sum ) {
      return 1;
    }
    return 0;
  }

	if( isset( $fuel_economy[ 'body' ] ) ) {
		$fuel_economy = json_decode( $fuel_economy[ 'body' ] );
		usort( $fuel_economy , 'sort_fuel_economies' );
		$fuel_economy = $fuel_economy[ 0 ];
	} else {
		$fuel_economy = false;
	}


	$colors = $vehicle_reference_system->get_colors( $trim->acode )->please();
	$colors = isset( $colors[ 'body' ] ) ? json_decode( $colors[ 'body' ] ) : NULL;
	
	echo $header; ?>
	<hr />
	<div id="trim">
	<div id="visuals">
		<div>
			<h3><?php echo "$trim->year $make $model"; ?></h3>
		</div>
		<div id="left-side">
		<div id="spotlight" class="ui-widget-header ui-corner-top">
			<?php
			$active = true;
			$count = 0;
			foreach( $colors as $color ) {
				if( isset( $color->image_urls ) && $color->type === 'Pri' ) {
					$count++;
					( $active === false ) ? $class = NULL : $class = 'active'; $active = false;
					echo '<img id="' . $color->code . '" src="' . make_transparent( $color->image_urls->medium ) . '" class="' . $class . '" />';
				}
			}
			if( $count === 0 ) {
				echo '<img src="' . make_transparent( $trim->images->medium ) . '" class="active" />';
			}
			?>
		</div>
	</div>
	<div id="right-side">
		<div id="variation">
			Trim: <?php echo $trim->name_variation; ?>
		</div>
		<div id="pricing">
			<span>Starting at:</span>
			<div id="msrp"><?php echo $trim->msrp; ?></div>
		</div>
		<?php
			if( $fuel_economy !== false ) {
		?>
		<div id="fuel">
			<div id="city"><div class="label">CITY:</div><div class="number"><?php echo $fuel_economy->city_mpg; ?></div></div>
			<div id="icon"><img src="http://static.dealer.com/v8/tools/automotive/showroom/v4/images/white/mpg.gif" /></div>
			<div id="hwy"><div class="label">HWY:</div><div class="number"><?php echo $fuel_economy->highway_mpg; ?></div></div>
		</div>
		<div id="disclaimer">Actual rating will vary with options, driving conditions, habits and vehicle condition.</div>
		<?php } ?>
		</div>
		<div>
			<div id="swatches">
				<?php
					$active = true;
					$options = array();
					$default_set = false;
					foreach( $colors as $color ) {
						if( ! empty( $color->file ) ) {
							if( ! in_array( $color->rgb , $colors ) ) {
								$type = $color->type === 'Pri' ? 'Exterior' : 'Interior';
								if( $type === 'Exterior' ) {
									$colors[] = $color->rgb;
									if( $default_set === false ) {
										echo '<div id="color-text">Color: ' . $color->name . '</div>';
										$default_set = true;
									}
									( $active === false ) ? $class = NULL : $class = 'active'; $active = false;
									echo '<a id="swatch-' . $color->code .'" title="Color: ' . $color->name . '" href="#' . $color->code . '" class="swatch ' . $class . '" style="background-color:rgb(' . $color->rgb .')">';
									echo $color->name . '</a>';
								}
							}
						}
					}
				?>
			</div>
		</div>
	</div>
	<div id="showcase-tabs">
		<ul>
		<li><a href="#overview">Overview</a></li>
		<?php
			if( count( $trims ) > 1 ) {
				echo '<li><a href="#trims">Trims</a></li>';
			}
		?>
		</ul>
		<div id="overview" style="overflow:hidden;">
		<?php
			$videos = $vehicle_reference_system->get_videos( $trim->acode )->please();
			if( isset( $videos[ 'response' ][ 'code' ] ) && $videos[ 'response' ][ 'code' ] === 200 ) {
				$videos = json_decode( $videos[ 'body' ] );
				if( $videos != false ) {
					echo '<div id="video" style="float:right;border:3px double #333;">';
					echo '<iframe src="http://player.dealertrend.com/player.html?t=Test%20Player&autoplay=0&v=' . $videos[ 0 ]->flash_video_url . '" height="300" width="400"></iframe>';
					echo '</div>';
				}
			}
			$reviews = $vehicle_reference_system->get_reviews( $trim->acode )->please();
			$reviews = isset( $reviews[ 'body' ] ) ? json_decode( $reviews[ 'body' ] ) : NULL;
			$options = array();
			if( count( $reviews ) > 0 ) {
				foreach( $reviews as $review ) {
					foreach( $review->titles as $title_object ) {
						if( $title_object->title === 'LIKED_MOST' ) {
							$options[] = $title_object->id;
						}
					}
				}
				if( count( $options ) > 0 ) {
					$index = mt_rand( 0 , count( $options ) - 1 );
					$review = $vehicle_reference_system->get_review( $options[ $index ] )->please();
					$review = json_decode( $review[ 'body' ] );
					echo '<div id="reviews" style="line-height:24px;">';
					echo '<h4 style="display:inline-block; clear:none; margin-bottom:1.2em;">What people are saying:</h4>';
					echo '<blockquote>';
					foreach( $review->content as $paragraph ) {
						echo '<p>' . $paragraph . '</p>';
					} 
					echo '</blockquote>';
					echo '</div>';
				}
			}

			$equipment = $vehicle_reference_system->get_equipment( $trim->acode )->please();
			$equipment = isset( $equipment[ 'body' ] ) ? json_decode( $equipment[ 'body' ] ) : NULL;

			$photos = $vehicle_reference_system->get_photos( $trim->acode )->please( array( 'type' => 'all' ) );
			$photos = isset( $photos[ 'body' ] ) ? json_decode( $photos[ 'body' ] ) : NULL;

			function sort_equipment( $a , $b ) {
				return ( $a->group > $b->group ) ? +1 : -1;
			}

			usort( $equipment , 'sort_equipment' );

			$equipment_groups = array();
			$equipment_data = array();
			foreach( $equipment as $item ) {
				$equipment_data[ $item->group ][] = $item;
				if( ! in_array( $item->group , $equipment_groups ) ) {
					$equipment_groups[] = $item->group;
				}
			}
			echo '<div id="overview-tabs" style="overflow:hidden; clear:both;"><ul>';
			echo '<li><a href="#equipment">Equipment</a></li>';
			echo '<li><a href="#photos">Photos</a></li>';
			echo '</ul>';
			echo '<div id="equipment">';
			foreach( $equipment_groups as $group ) {
				echo '<div class="group">';
				echo '<h4>' . $group . '</h4>';
				echo '<ul>';
				foreach( $equipment_data[ $group ] as $data ) {
					echo '<li>' . $data->name;
					echo ! empty( $data->data ) ? ': ' . $data->data : NULL;
					echo '</li>';
				}
				echo '</ul>';
				echo '</div>';
			}
			echo '</div>';
			echo '<div id="photos">';
			foreach( $photos as $photo ) {
				echo '<img src="' . $photo->image_urls->small . '" />';
			}
			echo '</div>';
			echo '</div>';
		?>
		</div>
		<?php
			if( count( $trims ) > 1 ) {
		?>
		<div id="trims">
			<?php
				foreach( $trims as $trim ) {
					$trim_names[ $trim->acode ] = $trim->name_variation;
					$trim_acodes[] = $trim->acode;
					$trim_acode_data[ $trim->acode ] = $trim;
				}
				foreach( $trim_acodes as $acode ) {
					$data = $vehicle_reference_system->get_equipment( $acode )->please();
					$data = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : NULL;
					$equipment[ $acode ] = isset( $equipment[ $acode ] ) ? $equipment[ $acode ] + $data : $data;
				}
			?>
			<table>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<?php
							foreach( $trim_names as $name ) {
								echo '<th>' . $name . '</th>';
							}
						?>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<?php
							foreach( $trim_names as $name ) {
								echo '<td><a href="#" id="' . $name . '" class="jquery-ui-button">Load</a></td>';
							}
						?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Transmission</th>
						<?php
							foreach( $trim_acodes as $acode ) {
								echo '<td>';
								foreach( $equipment[ $acode ] as $item ) {
									if( $item->name === 'Transmission' ) {
										echo '<div>' . $item->data . '</div>';
									}
								}
								echo '</td>';
							}
						?>
					</tr>
					<tr>
						<th>MSRP</th>
						<?php
							foreach( $trim_acodes as $acode ) {
								$trim_acode_data[ $acode ]->msrp = strpos( $trim_acode_data[ $acode ]->msrp , '$' ) === false ? '$' . number_format( $trim_acode_data[ $acode ]->msrp , 0 , '.' , ',' ) : $trim_acode_data[ $acode ]->msrp; 
								echo '<td>' . $trim_acode_data[ $acode ]->msrp . '</td>';
							}
						?>
					</tr>
					<tr>
						<th>Engine</th>
						<?php
							foreach( $trim_acodes as $acode ) {
								echo '<td>';
								foreach( $equipment[ $acode ] as $item ) {
									if( $item->name === 'Engine displacement' ) {
										echo ' <div>' . $item->data . '</div> ';
									}
								}
								echo '</td>';
							}
						?>
					</tr>
					<tr>
						<th>Fuel Economy</th>
						<?php
							foreach( $trim_acodes as $acode ) {
								foreach( $equipment[ $acode ] as $item ) {
									if( $item->name === 'Fuel economy city' ) {
										echo '<td>CTY: ' . $item->data;
									} elseif( $item->name === 'Fuel economy highway' ) {
										echo ' HWY:' . $item->data . '</td>';
									}
								}
							}
						?>
					</tr>
				</tbody>
			</table>
		</div>
		<?php } ?>
	</div>
	<p style="margin:10px auto; font-size:12px;">* Although every reasonable effort has been made to ensure the accuracy of the information contained on this site, absolute accuracy cannot be guaranteed. This site, and all information and materials appearing on it, are presented to the user "as is" without warranty of any kind, either express or implied, including but not limited to the implied warranties of merchantability, fitness for a particular purpose, title or non-infringement. All vehicles are subject to prior sale. Price does not include applicable tax, title, and license. Not responsible for typographical errors.</p>
</div>
