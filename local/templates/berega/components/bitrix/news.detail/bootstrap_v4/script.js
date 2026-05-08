$(document).ready(function(){
	$('.js-article-body h2').each(function(i, el){
		var title = $(el).attr('id', 'sect-article-' + i).text();
		$('.js-nav-article').append('<li><a href="#sect-article-'+ i +'">'+ title +'</a></li>');
	});
	$('.js-nav-article a').click(function(e){
		e.preventDefault();
		var href = $(this).attr('href');
		console.log($(href));
		$('html, body').animate({
			scrollTop: $(href).offset().top
		}, 1000);
	});
});
