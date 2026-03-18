function verify(url, text) {
	if (text == '') text = WPWallSettings.del_comfirm;
	if (confirm(text)) {
		document.location = url
	}
	return void(0)
}
jQuery(document).ready(function($) {
	if (WPWallSettings.expand_box != 'on') $("#wall_post").css("display", "none");
	$("#wall_post_toggle").click(function() {
		$("#wall_post").slideToggle("fast");
		$("#wall_post #wpwall_comment").focus();
		$("#wall_post #wpwall_author").focus()
	});
	$('#wallform').ajaxForm({
		target: '#wallcomments',
		success: function(responseText, statusText) {
			dukeToast(WPWallSettings.thanks_message, 'success', 0, true);
			$('#submit_wall_post').attr('value', WPWallSettings.submit);
			$("#wall_post").hide("fast");
			$("#wall_post #wpwall_comment").val('')
		},
		error: function(request) {
			if (request.responseText.search(/<title>WordPress &rsaquo;/) != -1) {
				var data = $(request.responseText).filter(".wp-die-message").html();
				dukeToast(data, 'warning', 0, true);
			$('#submit_wall_post').attr('value', WPWallSettings.submit);
			$("#wall_post").hide("fast");
			} else {
				dukeToast(WPWallSettings.err_message, 'danger', 0, true);
			}
			$('#submit_wall_post').attr('value', WPWallSettings.submit);
		},
		beforeSubmit: function(formData, jqForm, options) {
			for (var i = 0; i < 3; i++) {
				if (!formData[i].value) {
	                dukeToast(WPWallSettings.required_message, 'warning', 0, true);
					return false
				}
			}
			$('#submit_wall_post').attr('value', WPWallSettings.wait)
		}
	});
	$('.wallnav #img_left').click(function() {
		var page = $('#wallcomments #page_left');
		var wallform = $('#wallform');
		if (wallform[0]) $('#wallcomments').fadeOut(300).load(wallform[0].action + '?refresh=' + page[0].value);
		$('#wallcomments').fadeIn(100)
	});
	$('.wallnav #img_right').click(function() {
		var page = $('#wallcomments #page_right');
		var wallform = $('#wallform');
		if (wallform[0]) $('#wallcomments').fadeOut(300).load(wallform[0].action + '?refresh=' + page[0].value);
		$('#wallcomments').fadeIn(100)
	});
	refreshtime = parseInt(WPWallSettings.refreshtime);
	if (refreshtime) timeoutID = setInterval(refresh, (refreshtime < 5000) ? 5000 : refreshtime);

	function refresh() {
		var wallform = $('#wallform');
		var page = $('#wallcomments #wallpage');
		if (wallform[0]) $('#wallcomments').fadeOut(300).load(wallform[0].action + '?refresh=' + page[0].value)
	}
});