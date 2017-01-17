function format_prix(prix)
{
	var tmp = (Math.round(prix.replace(/,/g,'.')*100)/100).toFixed(2);
	var chaine = '';
	
	for(var i=0; i<tmp.length - 3; i++)
	{
		chaine = chaine + tmp[i];
		
		if((tmp.length - i) % 3 == 1 && tmp.length - i != 4)
			chaine = chaine + ' ';
	}
	
	return chaine + ',' + tmp.substr(tmp.length - 2);
}

function onYouTubePlayerAPIReady()
{
	var player = new YT.Player('video_iframe',
	{
		events:
		{
			'onReady': function()
			{
				$('#modal_video').on('shown.bs.modal', function()
				{
					player.playVideo();
				});
				
				$('#modal_video').on('hide.bs.modal', function()
				{
					player.pauseVideo();
				});
				
				player.playVideo();
			}
		}
	});
}

$(function()
{
	function verif_prix()
	{
		if($(this).val() != '')
		{
			var prix = $(this).val().replace(/[^\d,.]+/g,'');
			var num = prix.split(/,|\./).length;
			
			if (num > 2 || prix == '')
				prix = 0;
			else if(prix < 0)
				prix = -prix;
			
			$(this).val(format_prix(prix));
		}
	}
	
	setInterval(function()
	{
		$('.clignoter').animate({opacity:0.2},500).animate({opacity:1}, 500);
	},1200);
	
	/*
	function update_nav_pos()
	{
		if ($(document).scrollTop() > 85)
		{
			$('#navbar_head').addClass('shrink').addClass('navbar-fixed-top');
			$('body').css('padding-top', '50px');
		}
		else
		{
			$('#navbar_head').removeClass('shrink').removeClass('navbar-fixed-top');
			$('body').css('padding-top', '0');
		}
	}
	
	$(window).scroll(update_nav_pos);
	
	update_nav_pos();
	*/
	
	/*function resize_img_title()
	{
		if($(window).width() > $('#img_title').get(0).naturalWidth)
			$('#img_title').width('');
		else
			$('#img_title').width($(window).width() + 'px');
	}
	
	$(window).resize(resize_img_title);
	
	resize_img_title();*/
	
	$('#dropdown_membre').on('shown.bs.dropdown', function ()
	{
		if($('#email_connec').length)
			$('#email_connec').focus();
	});
	
	$('.input_prix').change(verif_prix);
	$('.input_prix').each(verif_prix);
	
	$('.carousel').carousel({ interval: 0 });
	
	if($('#sidentifier').length)
	{
		setInterval(function()
		{
			if($('#sidentifier').text() == 'Se connecter')
				$('#sidentifier').text('S\'inscrire');
			else
				$('#sidentifier').text('Se connecter');
		}, 1000);
	}
	
	$(window).load(function ()
	{
		$('[data-toggle="tooltip"]').tooltip();
		$('.tooltip_keep_shown').tooltip('show');
	});
	
	$( window ).resize(function()
	{
		$('.tooltip_keep_shown').tooltip('show');
	});
	
	$('.video_link').each(function()
	{
		$(this).css('background-image', 'url(\'sources/videos/' + $(this).attr('data-video') + '.jpg\')');
		$(this).attr('data-target', '#modal_video').attr('data-toggle', 'modal');
		$(this).addClass('embed-responsive').addClass('embed-responsive-16by9');
		
		$(this).html('<span class="glyphicon glyphicon-play" style="position: absolute; top: 50%; left: 50%; transform: translateY(-50%) translateX(-50%); -webkit-transform: translateY(-50%) translateX(-50%);"></span>');
	});
	
	$('.video_link').click(function()
	{
		var video_link = '//www.youtube.com/embed/' + $(this).attr('data-video') + '?enablejsapi=1&amp;html5=1';
		
		if($('#video_iframe').attr('src') != video_link)
			$('#video_iframe').attr('src', video_link);
	});
});