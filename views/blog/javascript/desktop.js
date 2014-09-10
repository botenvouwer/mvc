$(document).ready(function(){
	
	if($(window).height() > $('#mainColumn').height()){
		$('#mainColumn').css('min-height', ($(window).height() - $('header.main').height() - $('footer.main').height()) + 'px');
	}
	
});