// phpcs:ignoreFile -- this is not a php file
<script type="text/html" id="tmpl-shortcode-ui-field-p4-radio">

	<h2>{{{ data.block_heading }}}</h2>
	<p><i>{{{ data.block_description }}}</i></p>
	<div class="shortcode-ui-field-radio shortcode-ui-attribute-{{ data.attr }}">
		<h3>{{{ data.label }}}</h3>
		<div class="row">

			<# _.each( data.options, function( option ) { #>

				<div class="shortcake-p4-radio-div">
					<label style="display: inline;">
						<input type="radio" name="{{ data.attr }}" value="{{ option.value }}"
						<# if ( option.value == data.value ) { print('checked'); } #> />
							{{ option.label }}
					</label>
					<p>
						<img src="{{ option.image }}" alt="submenu">
					</p>
					<p class="description" style="display: inline">{{{ option.desc }}}</p>
				</div>
			<# }); #>
			<br>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-shortcode-ui-field-p4-select">
	<span class="shortcode-ui-field-select shortcode-ui-attribute-{{ data.attr }}">
		<label for="{{ data.id }}" style="display: inline-block">{{{ data.label }}}</label>
		<br>
		<select name="{{ data.attr }}" id="{{ data.id }}" {{{ data.meta }}}>
			<# _.each( data.options, function( option ) { #>

				<option value="{{ option.value }}"
					<# if ( _.contains( _.isArray( data.value ) ? data.value : data.value.split(','), option.value ) ) { print('selected'); } #>>
					{{ option.label }}
				</option>

			<# }); #>
		</select>
	</span>
</script>

<script type="text/html" id="tmpl-shortcode-ui-field-p4-checkbox">
	<span class="shortcode-ui-field-checkbox shortcode-ui-attribute-{{ data.attr }}">
		<label for="{{ data.id }}">{{{ data.label }}}</label>
		<input type="checkbox" name="{{ data.attr }}" id="{{ data.id }}" value="{{ data.value }}" <# if ( 'true' == data.value ){ print('checked'); } #>>
	</span>
</script>
