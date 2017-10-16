$(function	()	{
	//Resize graph when toggle side menu
	$('.navbar-toggle').click(function()	{
		setTimeout(function() {
			donutChart.redraw();
			lineChart.redraw();
			barChart.redraw();

			$.plot($('#placeholder'), [init], options);
		},500);
	});

	$('.size-toggle').click(function()	{
		//resize morris chart
		setTimeout(function() {
			donutChart.redraw();
			lineChart.redraw();
			barChart.redraw();

			$.plot($('#placeholder'), [init], options);
		},500);
	});

	//Refresh statistic widget
	$('.refresh-button').click(function() {
		var _overlayDiv = $(this).parent().children('.loading-overlay');
		_overlayDiv.addClass('active');

		setTimeout(function() {
			_overlayDiv.removeClass('active');
		}, 2000);

		return false;
	});

	$(window).resize(function(e)	{
		//resize morris chart
		setTimeout(function() {
			//donutChart.redraw();
			//lineChart.redraw();
			//barChart.redraw();

			//$.plot($('#placeholder'), [init], options);
		},500);
	});
});
