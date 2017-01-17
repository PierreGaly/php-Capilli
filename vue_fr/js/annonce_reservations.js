$(function()
{
	var dataSources = new Array();
	
	$('.cal_dates').each(function()
	{
		dataSources.push({
			name: $(this).val().substr(16),
			startDate: new Date($(this).val().substr(0,4), $(this).val().substr(4,2) - 1, $(this).val().substr(6,2)),
			endDate: new Date($(this).val().substr(8,4), $(this).val().substr(12,2) - 1, $(this).val().substr(14,2))
		});
	});
	
	$('#calendar').calendar({
			dataSource: dataSources,
			style: 'border',
			enableContextMenu: true,
			mouseOnDay: function(e) {
				if(e.events.length > 0) {
					var content = '';
					
					$('.popover').popover('hide');
					
					for(var i in e.events)
					{
						content += '<div class="event-tooltip-content">'
										+ '<div class="event-name" style="color:' + e.events[i].color + '">' + $('#text_transaction_' + e.events[i].name).html() + '</div>'
									+ '</div>';
					}
					
					$(e.element).popover({
						trigger: 'manual',
						container: 'body',
						html:true,
						content: content
					});
					
					$(e.element).popover().on("mouseenter", function ()
					{
						var _this = this;
						
						$(this).popover("show");
						
						$(".popover").on("mouseleave", function ()
						{
							$(_this).popover('hide');
						});
					}).on("mouseleave", function()
					{
						var _this = this;
						
						setTimeout(function ()
						{
							if (!$(".popover:hover").length)
								$(_this).popover("hide")
						}, 50);
					});
					
					$(e.element).popover('show');
				}
			},
			mouseOutDay: function(e) {
				
				 
			},
			language: 'fr'
		});
		
		$('#update-current-year').click(function() {
			$('#calendar').data('calendar').setYear($('#current-year').val());
		});
		
		$('#get-current-year').click(function() {
			alert($('#calendar').data('calendar').getYear());
		});
		
		$('#update-min-date').click(function() {
			$('#calendar').data('calendar').setMinDate($('#min-date')[0].valueAsDate);
		});
		
		$('#get-min-date').click(function() {
			alert($('#calendar').data('calendar').getMinDate());
		});
		
		$('#update-max-date').click(function() {
			$('#calendar').data('calendar').setMaxDate($('#max-date')[0].valueAsDate);
		});
		
		$('#get-max-date').click(function() {
			alert($('#calendar').data('calendar').getMaxDate());
		});
		
		$('#update-style').click(function() {
			if($('#style-border').prop('checked')) {
				$('#calendar').data('calendar').setStyle('border');
			}
			else {
				$('#calendar').data('calendar').setStyle('background');
			}
		});
		
		$('#get-style').click(function() {
			alert($('#calendar').data('calendar').getStyle());
		});
		
		$('#update-overlap').click(function() {
			$('#calendar').data('calendar').setAllowOverlap($('#allow-overlap').prop('checked'));
		});
		
		$('#get-overlap').click(function() {
			alert($('#calendar').data('calendar').getAllowOverlap());
		});
		
		$('#update-range-selection').click(function() {
			$('#calendar').data('calendar').setEnableRangeSelection($('#enable-range-selection').prop('checked'));
		});
		
		$('#get-range-selection').click(function() {
			alert($('#calendar').data('calendar').getEnableRangeSelection());
		});
		
		$('#update-week-number').click(function() {
			$('#calendar').data('calendar').setDisplayWeekNumber($('#display-week-number').prop('checked'));
		});
		
		$('#get-week-number').click(function() {
			alert($('#calendar').data('calendar').getDisplayWeekNumber());
		});
		
		$('#update-language').click(function() {
			$('#calendar').data('calendar').setLanguage($('#language').val());
		});
		
		$('#get-language').click(function() {
			alert($('#calendar').data('calendar').getLanguage());
		});
});