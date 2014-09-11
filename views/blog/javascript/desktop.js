$(document).ready(function(){
	
	var windowHeight = $(window).height();
	
	if(windowHeight > $('#mainColumn').height()){
		$('#mainColumn').css('min-height', (windowHeight - $('header.main').height() - $('footer.main').height()) + 'px');
		var old = window.scrollY;
		/*window.setInterval(function(){
			window.scrollTo(0,0);
			window.scrollTo(0,old);
		}, 500);*/
	}
	
});