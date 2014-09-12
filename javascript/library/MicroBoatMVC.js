$(document).ready(function () {
	
	$('body').on('click', '.authorize', function(){
		microBoatUserAuthorize();
	});
	
});

function microBoatUserAuthorize(){
	$.cookie('returnURL', document.URL, { path: '/' });
	window.location.href = actionUrl+'/user/login/';
}