function verify(url, text) {	if (text == '') text = WPWallSettings.del_comfirm;	if (confirm(text)) {		document.location = url	}	return void(0)}jQuery(document).ready(function($) {	if (WPWallSettings.expand_box != 'on') $("#wall_post").css("display", "none");	$("#wall_post_toggle").click(function() {		$("#wall_post").slideToggle("fast");		$("#wall_post #wpwall_comment").focus();		$("#wall_post #wpwall_author").focus()	});	$('#wallform').ajaxForm({		target: '#wallcomments',		success: function(responseText, statusText) {//			$('#wallresponse').html('<span class="wall-success">' + 'Thank you for your comment!' + '</span>');			toastr.success(WPWallSettings.thanks_message, '<i class="iconfont icon icon--sm ico-status"></i> ' + WPWallSettings.success,{positionClass:"toast-bottom-right"});			$('#submit_wall_post').attr('value', WPWallSettings.submit);			$("#wall_post").hide("fast");			$("#wall_post #wpwall_comment").val('')		},		error: function(request) {			console.log(request)			if (request.responseText.search(/<title>WordPress&rsaquo;/) != -1) {				var data = $(request.responseText).filter(".wp-die-message").html();//				$('#wallresponse').html('<span class="wall-error">' + data + '</span>');				toastr.warning(data, '<i class="iconfont icon icon--sm ico-status"></i> ' + WPWallSettings.notice,{positionClass:"toast-bottom-right"});			$('#submit_wall_post').attr('value', WPWallSettings.submit);			$("#wall_post").hide("fast");			$("#wall_post #wpwall_author").val('');			$("#wall_post #wpwall_email").val('')			$("#wall_post #wpwall_comment").val('')			} else {//				$('#wallresponse').html('<span class="wall-error">An error occurred, please notify the administrator.</span>')				toastr.error( WPWallSettings.err_message, '<i class="iconfont icon icon--sm ico-status"></i> ' + WPWallSettings.error,{positionClass:"toast-bottom-right"} );			}			$('#submit_wall_post').attr('value', WPWallSettings.submit);			$("#wall_post #wpwall_author").val('');			$("#wall_post #wpwall_email").val('');			$("#wall_post #wpwall_comment").val('')		},		beforeSubmit: function(formData, jqForm, options) {			$('#wallresponse').empty();			for (var i = 0; i < formData.length; i++) {				if (!formData[i].value) {//					$('#wallresponse').html('<span class="wall-error">' + 'Please fill in the required fields.' + '</span');					toastr.warning( WPWallSettings.required_message, '<i class="iconfont icon icon--sm ico-status"></i> ' + WPWallSettings.notice,{positionClass:"toast-bottom-right"} );					return false				}			}			$('#submit_wall_post').attr('value', WPWallSettings.wait)		}	});	$('.wallnav #img_left').click(function() {		var page = $('#wallcomments #page_left');		var wallform = $('#wallform');		if (wallform[0]) $('#wallcomments').load(wallform[0].action + '?refresh=' + page[0].value)	});	$('.wallnav #img_right').click(function() {		var page = $('#wallcomments #page_right');		var wallform = $('#wallform');		if (wallform[0]) $('#wallcomments').load(wallform[0].action + '?refresh=' + page[0].value)	});	refreshtime = parseInt(WPWallSettings.refreshtime);	if (refreshtime) timeoutID = setInterval(refresh, (refreshtime < 5000) ? 5000 : refreshtime);	function refresh() {		var wallform = $('#wallform');		var page = $('#wallcomments #wallpage');		if (wallform[0]) $('#wallcomments').load(wallform[0].action + '?refresh=' + page[0].value)	}});