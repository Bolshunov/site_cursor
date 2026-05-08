$(document).ready(function(){
	$('[data-tab]').click(function(e){
		e.preventDefault();
		var $section = $(this).parents('section');
		$section.find('[data-tab]').removeClass('is-active');
		$(this).addClass('is-active');
		$section.find('[data-tab-content]').hide();
		$section.find('[data-tab-content="'+ $(this).attr('data-tab') +'"]').show();
	}).filter(':first').click();

	$('[data-filter-cases]').click(function(e){
		e.preventDefault();
		var $section = $(this).parents('section');
		$section.find('[data-filter-cases]').removeClass('is-active');
		$(this).addClass('is-active');
		$section.find('[data-category-cases]').hide();
		$section.find('[data-category-cases="'+ $(this).attr('data-filter-cases') +'"]').show();
		
		if($(this).attr('data-filter-cases') == 'all') $section.find('[data-category-cases]').show();
		
	}).filter(':first').click();
});