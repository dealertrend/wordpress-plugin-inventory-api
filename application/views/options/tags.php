	<div id="tag-table-wrapper" class="settings-table-wrapper">
		<div id="tag-top-wrapper" class="settings-table-top-wrapper">
			<?php
				$this_value = $this->instance->options[ 'vehicle_management_system' ][ 'tags' ][ 'counter' ];
				$this_array = $this->instance->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ];
			?>
			<h3 class="title">Tag Settings</h3>
			<div id="tag-add-wrapper" class="settings-table-add-wrapper">
				<input id="inventory-tags-counter" type="hidden" name="inventory_tags_counter" value="<?php echo $this_value; ?>" readonly />
				<div id="inventory-add-tag" class="table-add-button">Apply Icon to Tag <span>+</span></div>
			</div>
		</div>

		<div id="tag-table-headers" class="table-headers">
			<div class="tag-name">Name</div>
			<div class="tag-order">Order</div>
			<div class="tag-upload">Upload</div>
			<div class="tag-icon">Icon</div>
			<div class="tag-link">Link</div>
			<div class="tag-remove">Remove</div>
		</div>

		<div id="inventory-tags-tr">
			<div id="inventory-tags-td">
				<?php
					if( !empty($this_array) ){
						foreach( $this_array as $key => $value){
							$id_value = 'inventory-tag-'.$key;
							echo '<div class="custom-table-tr inventory-tags-wrapper ' . $id_value . '">';
							//Name
							echo '<div class="tag-name inventory-tag-value ' . $id_value . '">';
							echo '<input type="text" name="inventory_tag[' . $key . '][name]" id="inventory-tag-' . $key . '-name" class="inventory-tag-text ' . $id_value . '" value="' . $value['name'] . '" />';
							echo '</div>';
							//Order
							echo '<div class="tag-order inventory-tag-value ' . $id_value . '">';
							echo '<input type="number" name="inventory_tag[' . $key . '][order]" id="inventory-tag-' . $key . '-order" class="inventory-tag-number ' . $id_value . '" value="' . $value['order'] . '" />';
							echo '</div>';
							//Media Upload
							echo '<div class="tag-upload inventory-tag-value ' . $id_value . '">';
							echo '<a id="' . $id_value . '" href="#" for="inventory_tag[' . $key . '][url]" class="custom_media_upload inventory-tag-label ' . $id_value . '">Upload</a>';
							echo '<input id="inventory-tag-' . $key . '-url" class="custom_media_url inventory-tag-text ' . $id_value . '" type="text" name="inventory_tag[' . $key . '][url]" value="' . $value['url'] . '">';
							echo '</div>';
							//Media Image
							echo '<div class="tag-icon ' . $id_value . '">';
							echo '<img class="custom_media_image inventory-tag-label ' . $id_value . '" src="' . $value['url'] . '" />';
							echo '</div>';
							//Link
							echo '<div class="tag-link inventory-tag-value ' . $id_value . '">';
							echo '<input type="text" name="inventory_tag[' . $key . '][link]" id="inventory-tag-' . $key . '-link" class="inventory-tag-text ' . $id_value . '" value="' . $value['link'] . '" />';
							echo '</div>';
							//Remove
							echo '<div id="' . $id_value . '" class="tag-remove inventory-tag-remove ' . $id_value . '"><span>[x]</span></div>';

							echo '</div>';
						}
					}
				?>
			</div>
		</div>
	</div>
