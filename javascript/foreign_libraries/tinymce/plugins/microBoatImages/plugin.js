/*
	easy ajax image picker and uploader. This plug in is made by hand and uses Jquery, Jquery UI and microboatWebApp.  
*/

tinymce.PluginManager.add('microBoatImages', function(editor, url) {
	
	function imagePicker() {
		if($('#microBoatImages').length != 0){
			return;
		}
		
		var html = window.actionhandler('<load action="iqwebframe" level="image_picker"></load>');
		window.datalader('<dialog title="Kies of upload een afbeelding" height="500" width="460" id="microBoatImages">'+html+'</dialog>');
	}
	
	editor.addButton('microBoatImages', {
		tooltip: 'Kies of upload een afbeelding',
		icon : 'image',
		text: 'Afbeelding',
		onclick: imagePicker
	});
});