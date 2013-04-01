window.addEvent('domready', function() {
var myAccordion = new Accordion($('footer'), '#footer h2', '#footer .content', {
opacity:true,
display: -1,
duration: 300,
alwaysHide: true,

		onActive: function(menuheader, menucontent){
			menuheader.setStyle('font-weight', 'bold');
		},
		onBackground: function(menuheader, menucontent){
			menuheader.setStyle('font-weight', 'bold');
		}
});
});