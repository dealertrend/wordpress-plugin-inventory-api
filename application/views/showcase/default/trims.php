<div id="loader" style="display:none; height:50px; width:50px;"></div>
<script type="text/javascript">
function addCommas(nStr)
{
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}

	var dealertrend = jQuery.noConflict();
	dealertrend(document).ready(function () {
	dealertrend('#loader').hide();
	dealertrend( '#loader' ).dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		position: 'center',
		maxHeight: 50,
		maxWidth: 50,
		title: false
	});
	dealertrend( '#showcase .jquery-ui-button' ).button();
	dealertrend( '#showcase .jquery-ui-button' ).click( function(e) {
		button = dealertrend(this);
		dealertrend('#loader').dialog('open');
		button.css('cursor','wait');
		// trims
          dealertrend.ajax(
          {
            url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=default&country_code=<?php echo $country_code; ?>',
            context: document.body,
            success: function(data) {
              var json = JSON.parse(data);
		if( json[ 0 ].oem_cab_type == "" || json[ 0 ].oem_cab_type == null ){
	              dealertrend( '#variation' ).html( 'TRIM: '+ json[ 0 ].name_variation + '<br>' +  json[ 0 ].body_style );
		} else {
	              dealertrend( '#variation' ).html( 'TRIM: '+ json[ 0 ].name_variation + '<br>' +  json[ 0 ].oem_cab_type + '<br>' + json[ 0 ].ads_drive_type );
		}

              dealertrend( '#pricing #msrp' ).html( '$' + addCommas( json[ 0 ].msrp ) );
              dealertrend( '#trim' ).addClass( json[ 0 ].acode );
              var default_photo = json[ 0 ].images.medium.replace( /IMG=(.*)\.\w{3,4}/i , function(a, b) {
                return 'IMG=' + b + '.png';
              });
              dealertrend( '#spotlight' ).html('<img src="' + default_photo  + '" class="active" />');
              button.css('cursor','pointer');
              populate_page( json[ 0 ].acode );
            }
          });
          function populate_page( acode ) {
            button.css('cursor','wait');
            // fuel economy
console.log('/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=fuel_economy&acode=' + acode + '&country_code=    <?php echo $country_code; ?>');
            dealertrend.ajax(
            {
              url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=fuel_economy&acode=' + acode + '&country_code=<?php echo $country_code; ?>',
              context: document.body,
              success: function(data) {
                var json = JSON.parse(data);
<?php if($country_code == 'CA') { ?>
                dealertrend( '#fuel #city .number' ).html( json[ 0 ].city_lp_100km );
                dealertrend( '#fuel #hwy .number' ).html( json[ 0 ].highway_lp_100km );
<?php } else { ?>
                dealertrend( '#fuel #city .number' ).html( json[ 0 ].city_mpg );
                dealertrend( '#fuel #hwy .number' ).html( json[ 0 ].highway_mpg );
<?php } ?>
                button.css('cursor','pointer');
              }
            });

          dealertrend.ajax(
          {
            url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=colors&acode=' + acode + '&country_code=<?php echo $country_code; ?>',
            context: document.body,
            success: function(data) {
              var json = JSON.parse(data);
              var count = 0;
              var color_history = null;
              if( json.length > 0 ) {
                dealertrend( '#spotlight' ).html('');
                json.forEach( function( color ) {
                  if( color.rgb != null ) {
                    if( color_history == null || color_history.search( /color.rgb/i , color_history ) == -1 ) {
                      color_history += '[' + color.rgb + ']';
                      var image = document.createElement('img');
                      var link = document.createElement('a');
                      var text = document.createTextNode( color.name );
                      link.href = '#' + color.code;
                      link.title = 'Color: ' + color.name;
                      link.id = '#swatch-' + color.code;
                      link.className = 'swatch';
                      link.style.backgroundColor = 'rgb(' + color.rgb + ')';
                      link.appendChild(text);
                      image.src = color.image_urls.medium.replace( /IMG=(.*)\.\w{3,4}/i , function(a, b) {
                        return 'IMG=' + b + '.png';
                      });
                      image.id = color.code;
                      if( count == 0 ) {
                        image.className = 'active';
                        link.className += ' active';
                        var color_text = dealertrend( '#color-text' ).html( 'Color: ' + color.name );
                        dealertrend( '#swatches' ).html( color_text );
                        count++;
                      }
                      dealertrend( '#spotlight' ).append( image );
                      dealertrend( '#swatches' ).append( link );
                    }
                  }
                });
              } else {

              }
              dealertrend('#loader').dialog('close');
        var current_image, next_image, color_text;
        color_text = dealertrend('#color-text');
        dealertrend('#swatches a').click(function (e) {
            current_image = dealertrend('#spotlight .active');
            next_image = dealertrend('#spotlight ' + e.target.hash);
            current_image.removeClass('active').hide();

            color_text.text(e.target.title);
            next_image.show().addClass('active');
            current_image = next_image;
            dealertrend('#swatches .active').removeClass('active');
            dealertrend('#' + e.target.id).addClass('active');
            e.preventDefault();
        });
        dealertrend('#swatches a').hover(function (e) {
            current_image = dealertrend('#spotlight .active');
            next_image = dealertrend('#spotlight ' + e.target.hash);
            current_image.removeClass('active').hide();
            next_image.show().addClass('active');
            color_text.text(e.target.title);
        }, function (e) {
            next_image.removeClass('active').hide();
            current_image.show().addClass('active');
            color_text.text(dealertrend('#swatches .active').attr('title'));
        });
            }
          });

        dealertrend.ajax(
        {
          url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=reviews&acode=' + acode + '&country_code=<?php echo $country_code; ?>',
          context: document.body,
          success: function( data ) {
          }
        });

        dealertrend.ajax(
        {
          url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=equipment&acode=' + acode + '&country_code=<?php echo     $country_code; ?>',
          context: document.body,
          success: function( data ) {
          }
        });

        dealertrend.ajax(
        {
          url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=photos&acode=' + acode + '&country_code=<?php echo $country_code; ?>',
          context: document.body,
          success: function( data ) {
            var json = JSON.parse(data);
            json.forEach( function( photo ) {
              dealertrend('#trim #photos').append( '<img src="' + photo.image_urls.small + '" />' );
            });
          }
        });

          }
          e.preventDefault();
      } );

    });
</script>


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
			if( $a->image_filename == NULL ) {
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

	//$trims = array_merge( $trim_data[ $next_year ] , $trim_data[ $current_year ] , $trim_data[ $last_year ] );
	if ( !empty( $trim_data[ $next_year ] ) ) {
		$trims = $trim_data[ $next_year ];
	} elseif ( !empty( $trim_data[ $current_year ] ) ) {
		$trims = $trim_data[ $current_year ];
	} else {
		$trims = $trim_data[ $last_year ];
	}

	$names_and_prices = array();
	foreach( $trims as $key => $trim ) {
		$important_stuff = array( $trim->name_variation, $trim->mfg_code );
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
							if( isset( $color->image_urls ) && $color->type == 'Pri' ) {
								$count++;
								( $active == false ) ? $class = NULL : $class = 'active'; $active = false;
								echo '<img id="' . $color->code . '" src="' . make_transparent( $color->image_urls->medium ) . '" class="' . $class . '" />';
							}
						}
						if( $count == 0 ) {
							echo '<img src="' . make_transparent( $trim->images->medium ) . '" class="active" />';
						}
					?>
				</div>

				<div id="swatches">
					<?php
						$active = true;
						$options = array();
						$default_set = false;
						foreach( $colors as $color ) {
							if( ! empty( $color->file ) ) {
								if( ! in_array( $color->rgb , $colors ) ) {
									$type = $color->type == 'Pri' ? 'Exterior' : 'Interior';
									if( $type == 'Exterior' ) {
										$colors[] = $color->rgb;
										if( $default_set == false ) {
											echo '<div id="color-text">Color: ' . $color->name . '</div>';
											$default_set = true;
										}
										( $active == false ) ? $class = NULL : $class = 'active'; $active = false;
										echo '<a id="swatch-' . $color->code .'" title="Color: ' . $color->name . '" href="#' . $color->code . '" class="swatch ' . $class . '" style="background-color:rgb(' . $color->rgb .')">';
										echo $color->name . '</a>';
									}
								}
							}
						}
					?>
				</div>
			</div>
			<div id="right-side">
				<div id="variation">
					Trim: <?php echo $trim->name_variation; ?>
					<br>
					<?php
						if ( isset($trim->oem_cab_type) && !empty( $trim->oem_cab_type ) ){
							echo $trim->oem_cab_type ."<br>";
							echo $trim->ads_drive_type;
						} else {
							echo $trim->body_style;
						}
					?>
				</div>
				<div id="pricing">
					<span>Starting at:</span>
					<div id="msrp"><?php echo $trim->msrp; ?></div>
				</div>
				<?php
					if( $fuel_economy != false ) {
				?>
				<div id="fuel">
					<?php
						if( $country_code == 'CA' ) {
					?>
					<div id="city"><div class="label">CITY:</div><div class="number"><?php echo $fuel_economy->city_lp_100km; ?></div></div>
					<div id="icon"><img src="http://static.dealer.com/v8/tools/automotive/showroom/v4/images/white/mpg.gif" /></div>
					<div id="hwy"><div class="label">HWY:</div><div class="number"><?php echo $fuel_economy->highway_lp_100km; ?></div></div>
					<?php
						} else {
					?>
					<div id="city"><div class="label">CITY:</div><div class="number"><?php echo $fuel_economy->city_mpg; ?></div></div>
					<div id="icon"><img src="http://static.dealer.com/v8/tools/automotive/showroom/v4/images/white/mpg.gif" /></div>
					<div id="hwy"><div class="label">HWY:</div><div class="number"><?php echo $fuel_economy->highway_mpg; ?></div></div>
					<?php
						}
					?>
				</div>
				<div id="disclaimer">Actual rating will vary with options, driving conditions, habits and vehicle condition.</div>
			<?php } ?>
			</div>
			<?php
				if ( is_active_sidebar( 'showcase-trim-page' ) ) :
					echo '<div id="sidebar-widget-area" class="sidebar">';
						dynamic_sidebar( 'showcase-trim-page' );
					echo '</div>';
				endif;
			?>
		</div>
		<div id="showcase-tabs">
		<ul>
		<?php
			if( count( $trims ) > 1 ) {
				echo '<li><a href="#trims">Trims</a></li>';
			}
			echo '<li><a href="#overview">Reviews and Video</a></li>';
			echo '<li><a href="#equipment">Equipment</a></li>';
			echo '<li><a href="#photos">Photos</a></li>';
		?>
		</ul>
		<div id="overview" style="overflow:hidden;">
			<?php
				$videos = $vehicle_reference_system->get_videos( $trim->acode )->please();
				if( isset( $videos[ 'response' ][ 'code' ] ) && $videos[ 'response' ][ 'code' ] == 200 ) {
					$videos = json_decode( $videos[ 'body' ] );
					if( $videos != false && (isset($videos[ 0 ]->filename) && !empty($videos[ 0 ]->filename) ) ) {
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
							if( $title_object->title == 'LIKED_MOST' ) {
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
							echo '<p style="font-size:14px; margin-left:15px;"> - ' . $reviews[ $index ]->review_by . '</p>';
						echo '</div>';
					}
				}
		echo '</div>';
				$equipment = $vehicle_reference_system->get_equipment( $trim->acode )->please();
				$equipment = isset( $equipment[ 'body' ] ) ? json_decode( $equipment[ 'body' ] ) : NULL;

				$photos = $vehicle_reference_system->get_photos( $trim->acode )->please( array( 'type' => 'oem_exterior_standard' ) );
				$photos = isset( $photos[ 'body' ] ) ? json_decode( $photos[ 'body' ] ) : NULL;

			function sort_equipment( $a , $b ) {
				return ( $a->group > $b->group ) ? +1 : -1;
			}

			function sort_photos( $a , $b ) {
				if( $a->filename > $b->filename ) {
					return -1;
				}
				if( $a->filename == $b->filename ) {
					return 1;
				}
			}

			usort( $equipment , 'sort_equipment' );
			usort( $photos , 'sort_photos' );

			$equipment_groups = array();
			$equipment_data = array();
			foreach( $equipment as $item ) {
				$equipment_data[ $item->group ][] = $item;
				if( ! in_array( $item->group , $equipment_groups ) ) {
					$equipment_groups[] = $item->group;
				}
			}
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
			$shown = array();
			foreach( $photos as $photo ) {
				if( ! in_array( $photo->filename , $shown ) ) {
					$shown[] = $photo->filename;
					echo '<img src="' . $photo->image_urls->small . '" />';
				}
			}
			echo '</div>';
		?>
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
						<th>&nbsp;</th>
						<?php
							foreach( $trim_acodes as $acode ){
								if ( isset( $trim_acode_data[ $acode ]->oem_cab_type ) && !empty( $trim_acode_data[ $acode ]->oem_cab_type ) ){
									echo '<th>' . $trim_acode_data[ $acode ]->oem_cab_type . '</th>';
								} else {
									echo '<th>' . $trim_acode_data[ $acode ]->body_style . '</th>';
								}
							}
						?>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<?php
							foreach( $trim_acodes as $acode ) {
								echo '<td><a href="#" id="' . $trim_acode_data[ $acode ]->acode . '" class="jquery-ui-button">Load</a></td>';
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
									if( $item->name == 'Transmission' ) {
										echo '<div>' . $item->data . '</div>';
									}
								}
								echo '</td>';
							}
						?>
					</tr>
					<tr>
						<th>Drive Type</th>
						<?php
							foreach( $trim_acodes as $acode ){
								echo '<td>' . $trim_acode_data[ $acode ]->ads_drive_type . '</td>';
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
									if( $item->name == 'Engine displacement' ) {
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
									if( $item->name == 'Fuel economy city' ) {
										echo '<td>CTY:' . $item->data;
									} elseif( $item->name == 'Fuel economy highway' ) {
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
