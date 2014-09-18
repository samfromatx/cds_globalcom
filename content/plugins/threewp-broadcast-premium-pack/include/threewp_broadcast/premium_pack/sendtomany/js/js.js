jQuery(document).ready(function($) {

	window.broadcast_sendtomany =
	{
		$button : null,
		$button_container : null,
		$cancel_button : null,
		$meta_box_window : null,
		post_ids : null,
		$send_to_many_button : null,
		$tablenav : null,

		button_clicked : function()
		{
			this.close_meta_box();

			// Are there any blogs selected?
			var post_ids = '';

			// Get all selected rows
			var $inputs = $( '#posts-filter tbody#the-list th.check-column input:checked' );
			$.each( $inputs, function( index, item )
			{
				var $item = $( item );
				var $row = $( item ).parentsUntil( 'tr' ).parent();
				// Is this post already broadcasted?
				if ( $( 'div.linked_from', $row ).length > 0 )
					return;
				// Add it
				var id = $row.prop( 'id' ).replace( 'post-', '' );
				post_ids += ',' + id;
			});

			// Nothing selected?
			if ( post_ids.length < 1 )
			{
				alert( window.broadcast_sendtomany_data.strings.no_posts_selected );
				return;
			}

			// Retrieve the post meta box
			this.post_ids = post_ids;
			this.open_meta_box();
		},

		close_meta_box : function()
		{
			if ( this.$meta_box_window === null )
				return;
			this.$meta_box_window.remove();
			this.$meta_box_window = null;
			this.$button.show();
		},

		open_meta_box : function()
		{
			// Hide the button
			this.$button.hide();

			var $posts_filter = $( '#posts-filter' );

			// Insert it above the tablenav
			this.$meta_box_window = $( '<div />' )
				.append( '<br class="clear"/>' )
				.append( window.broadcast_sendtomany_data.strings.loading )
				.prependTo( $posts_filter )
				.prop( 'id', 'threewp_broadcast' );

			$.ajax({
				'data' : {
					'action' : window.broadcast_sendtomany_data.actions.get_meta_box,
					'post_ids' : this.post_ids,
				},
				'dataType' : 'json',
				'error' : function ( data )
				{
					alert( "Error opening Send To Many box: " + JSON.stringify( data ) );
				},
				'type' : 'post',
				'url' : ajaxurl,
				'success' : function ( data )
				{
					// Display the html
					var html = '<div id="threewp_broadcast" class="postbox clear"><div class="inside">' + data.html + '</div></div>';
					window.broadcast_sendtomany.$meta_box_window.html( html );

					// Add the extra css and js files
					var $head = $( 'head' );
					$.each( data.css, function( index, item )
					{
						$head.append( "<link rel='stylesheet' href='" + item + "' type='text/css' media='all'>" );
					});

					var $body = $( 'body' );
					$.each( data.js, function( index, item )
					{
						$body.append( '<script type="text/javascript" src="' + item + '"/>' );
					});

					// Make the submit button clickable.
					window.broadcast_sendtomany.$meta_box_window.$send_to_many_button = $( 'input#send_to_many', window.broadcast_sendtomany.$meta_box_window )
						.click( function( e )
						{
							e.preventDefault();
							return window.broadcast_sendtomany.send_to_many();
						});

					window.broadcast_sendtomany.$meta_box_window.$cancel_button = $( 'input#cancel_send_to_many', window.broadcast_sendtomany.$meta_box_window )
						.click( function( e )
						{
							e.preventDefault();
							return window.broadcast_sendtomany.close_meta_box();
						});
				},
			});
		},

		send_to_many : function()
		{
			var blog_ids = new Array();

			// Find out which blogs are selected
			var $blogs = $( '#plainview_sdk_form2_inputs_checkboxes_blogs input.checkbox:checked' );
			$.each( $blogs, function( index, item )
			{
				var id = $( item ).prop( 'value' );
				blog_ids.push( id );
			});

			if ( blog_ids.length < 1 )
			{
				alert( window.broadcast_sendtomany_data.strings.no_blogs_selected );
				return;
			}

			this.$meta_box_window.$send_to_many_button.prop( 'disabled', true ).prop( 'value', window.broadcast_sendtomany_data.strings.sending );
			this.$meta_box_window.$cancel_button.prop( 'disabled', true );

			$form = $( 'form', this.$meta_box_window );

			var data = $form.serialize() + '&' + $.param({
					'action' : window.broadcast_sendtomany_data.actions.send_to_many,
					'post_ids' : this.post_ids,
			});

			$.ajax({
				'data' : data,
				'dataType' : 'json',
				'error' : function ( data )
				{
					alert( "Error sending Send To Many command: " + JSON.stringify( data ) );
				},
				'type' : 'post',
				'url' : ajaxurl,
				'success' : function ( data )
				{
					window.broadcast_sendtomany.close_meta_box();
					// Click the filter button to reload the page
					$( '#post-query-submit', this.$tablenav ).click();
				},
			});
		},

		init : function()
		{
			this.$tablenav = $( '#posts-filter .tablenav.top' );

			if ( this.$tablenav.length < 1 )
				return;

			// Create a new button at the end.
			this.$button_container = $( '<div />' )
				.addClass( 'alignleft' )
				.addClass( 'actions' )
				.insertBefore( $( 'br.clear', this.$tablenav ) );

			this.$button = $( '<button />' )
				.addClass( 'button' )
				.click( function( e )
				{
					e.preventDefault();
					return window.broadcast_sendtomany.button_clicked();
				})
				.html( window.broadcast_sendtomany_data.strings.send_to_many )
				.appendTo( this.$button_container );
		}
	};
	broadcast_sendtomany.init();
});
