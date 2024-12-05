"use strict";


jQuery(document).ready(function($) {
	var newIndex = $('#custom_meta_box_wrapper .custom_meta_box_item').length;
	
	$('#add_repeater').on('click', function() {
		var index = newIndex++;
		var editorId = 'article_body_' + index;

		var newItem = `
			<div class="custom_meta_box_item live_posts_update_wrap">
				<div class="um_live_post_field um_lv_headline"><label>Headline:</label> <input type="text" name="custom_meta[` + index + `][headline]" />
				</div>
				<div class="um_live_post_field um_lv_date_published"><label>Date Published:</label><div class="date_ui_flex"><input type="datetime-local" name="custom_meta[` + index + `][date_published]" /><a id="update_date_now" class="update_date_now" href="javascript:void(0)">Update Date</a><a id="clear_date_now" class="clear_date_now" href="javascript:void(0)">Clear</a></div>
				</div>
				<div class="um_live_post_field um_lv_article_body"><label>Article Body:</label>
					<div id="` + editorId + `"></div>
					<textarea name="custom_meta[` + index + `][article_body]" id="` + editorId + `_textarea"></textarea>
				</div>
				<div class="um_live_post_field um_lv_remove_btn">
					<button type="button" class="remove_repeater">Remove</button>
				</div>
			</div>
		`;

		$('#custom_meta_box_wrapper').append(newItem);

		// Initialize wp_editor after appending to the DOM
		wp.editor.initialize(editorId + '_textarea', {
			tinymce: true,
			quicktags: true,
			mediaButtons: true,
			textarea_name: 'custom_meta[' + index + '][article_body]'
		});
	});
	
	$('#custom_meta_box_wrapper').on('click', '.remove_repeater', function() {
		if (confirm('Are you sure you want to remove this update?')) {
			$(this).closest('.custom_meta_box_item').remove();
		}
	});

	
});  // end jQuery(document).ready


document.addEventListener('DOMContentLoaded', function() {
	const checkbox = document.getElementById('custom_stop_live_meta');
	const addButton = document.getElementById('add_repeater');
	const removeButtons = document.querySelectorAll('.remove_repeater');

	const activecheckbox = document.getElementById('custom_checkbox_meta');

	document.getElementById('custom_checkbox_meta').addEventListener('change', function() {
		if (this.checked) {
			checkbox.checked = false;
		}
	});
	

	// Function to update button state based on checkbox
	function updateButtonState() {
		addButton.disabled = checkbox.checked;
		removeButtons.forEach(button => {
			button.disabled = checkbox.checked;
		});

		if(checkbox.checked){
			activecheckbox.checked = false;
		}
		// if(activecheckbox.checked){
		// 	console.log("active-checked:");
		// 	checkbox.checked = false;
		// }
	}

	// Update button state on page load
	updateButtonState();

	// Update button state on checkbox input (checked/unchecked)
	checkbox.addEventListener('input', updateButtonState);
});


document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('custom_meta_box_wrapper').addEventListener('click', function(event) {
        if (event.target.classList.contains('update_date_now')) {
            var metaBoxItem = event.target.closest('.custom_meta_box_item');
            if (metaBoxItem) {
                var dateInput = metaBoxItem.querySelector('input[type="datetime-local"]');
                if (dateInput) {
                    var now = new Date();
					// Format the date and time to match 'YYYY-MM-DDTHH:MM' format
					var year = now.getFullYear();
					var month = String(now.getMonth() + 1).padStart(2, '0');
					var day = String(now.getDate()).padStart(2, '0');
					var hours = String(now.getHours()).padStart(2, '0');
					var minutes = String(now.getMinutes()).padStart(2, '0');
					var seconds = String(now.getSeconds()).padStart(2, '0');
					var nowISO = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
					dateInput.value = nowISO;
                }
            }
        }
		if (event.target.classList.contains('clear_date_now')) {
            var metaBoxItem = event.target.closest('.custom_meta_box_item');
            if (metaBoxItem) {
                var dateInput = metaBoxItem.querySelector('input[type="datetime-local"]');
                if (dateInput) {
					dateInput.value = '';
                }
            }
        }
    });
});


// Post type options to add select2
jQuery(document).ready(function() {
    jQuery('#custom_post_type_option').select2();
});
