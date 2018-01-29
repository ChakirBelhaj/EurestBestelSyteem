function toggle_sidebar()
{
	$('.main').toggleClass('toggled');
	$('.sidebar').toggleClass('toggled');
}

if ($('.sidebar').length) {
	if($(window).width() < 960) {
	   	toggle_sidebar();
	}
}

$(window).resize(function() {
  	if ($(window).width() > 768) {
  		if($('.sidebar').hasClass('toggled')) {
  			toggle_sidebar();
  		}
  	}
});

var hash = window.location.hash;

if (hash.indexOf('product-') !== -1) {
	$(hash).modal('toggle');
}