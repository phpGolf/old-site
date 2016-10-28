$(document).ready(function() {
	//Rating
	var rating = 0.5;
	
	//Images
	var filled = 'star_filled.png';
	var unfilled = 'star_unfilled.png';
	var hover = 'star_filled_yellow.png';
	
	//Fill stars
	function fillStars() {
		$('img.star').each(function(i,star) {
			$(this).data('index',(i+1));
			if((i+1) <= Math.round(rating)) {
				$(this).attr('src',filled);
			} else {
				$(this).attr('src',unfilled);
			}
		});
	}
	
	fillStars();
	
	//Hover
	$('img.star').hover(function (handlerIn) {
		var index = $(this).data('index');
		$('img.star:lt('+index+')').attr('src',hover);
		$('img.star:gt('+(index-1)+')').attr('src',unfilled);
	});
	
	//Unhover
	$('div.stars').mouseleave(function () {
		fillStars();
	});
	
	//Save result
	$('img.star').click(function() {
		rating = $(this).data('index');
		//Code to save rating
	})
});
