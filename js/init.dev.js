	jQuery(document).ready( function($) {

		$('#colors').sortable({
			items: 'div.col',
			placeholder: 'col colph',
			forcePlaceholderSize: true,
			stop: function( event, ui) {

				$('#colors').css(
					'backgroundColor', $('div.col:last').css('backgroundColor')
				);

				updateURL();
			}
		});

		getURL = function () {
			url = '/';
			$('.hex input').each( function( index ) {
				url += $(this).val() + '/';
			});
			$('#save').attr( 'href', url );

			return url;
		}

		getimgURL = function () {
			url = getURL();
 			$('#image').attr( 'href', '/png/500x500' + url );
			return url;
		}

		updateURL = function () {
			var stateObj = { foo: "bar" };
			history.pushState(stateObj, "Colors", getURL() );
			getimgURL();
		}

		getURL();

		$('.col input').numeric({allow:'abcdef'});

		$('.col .hex input').live( 'change', function () {

			updateURL();

			var color = $(this).val();
			if ( color.length == 3 || color.length == 6 ) {
			
				if ( $(this).parent('p').parent('div.col').index() === $('div.col').length ) {
					$('#colors').animate( {
						backgroundColor: '#' +color
						}, 500 );
				}
				$(this).parent().parent('div').animate( {
					backgroundColor: '#' +color
					}, 500 );
			}

		});
		$('#add').click( function () {
			last = $('.col:last');

			html = last.html();
			style = last.attr('style');
			last.after( '<div class="col" style="' + style + '">' + html + '</div>' );

			total = $('.col').length;
			//$(this).append( total );
			var width = 100/total;
			$('.col').each( function () {
				$(this).width( width + '%' );
				sliders( $(this) );
			});

			updateURL();
			return false;
		});

		$('.del').live( 'click', function () {
			$(this).parent('p').parent('div').remove();

			total = $('.col').length;
			var width = 100/total;
			$('.col').each( function () {
				$(this).width( width + '%' );
			});

			updateURL();
			return false;
		});

		$('#gobtn').click( function () {
			$('#colorform').submit();
			return false;
		});

    sliders = function( th ) {
        var th = th;
        hex = th.css('backgroundColor');
        rgb = hex.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
        var red = rgb[1];
        var green = rgb[2];
        var blue = rgb[3];
        //$('body').prepend( rgb[1] +','+ rgb[2] +','+ rgb[3] );

        th.find('.r').val( red );
        th.find('.r').parent('span').siblings('input').val( red );
        th.find('.g').val( green );
        th.find('.g').parent('span').siblings('input').val( green );
        th.find('.b').val( blue );
        th.find('.b').parent('span').siblings('input').val( blue );

        $('.ch').live('change', function() {
    
            $(this).parent('span').siblings('input').val( $(this).val() );

            r = parseInt( th.find('.r').val() ).toString( 16 );
            if (r.length == 1) r = '0' + r;

            g = parseInt( th.find('.g').val() ).toString( 16 );
            if (g.length == 1) g = '0' + g;
        
            b = parseInt( th.find('.b').val() ).toString( 16 );
            if (b.length == 1) b = '0' + b;
        
            x = r + g + b;

            th.find('.hex').children('input').val( x );
            updateURL();
            th.css( 'backgroundColor',  '#' +x );

        });

        $('.ch_val').live('keyup', function() {
    
            $(this).siblings('span').children('input').val( $(this).val() );

            r = parseInt( th.find('.r').val() ).toString( 16 );
            if (r.length == 1) r = '0' + r;

            g = parseInt( th.find('.g').val() ).toString( 16 );
            if (g.length == 1) g = '0' + g;
        
            b = parseInt( th.find('.b').val() ).toString( 16 );
            if (b.length == 1) b = '0' + b;
        
            x = r + g + b;

            th.find('.hex').children('input').val( x );
            updateURL();
            th.css( 'backgroundColor',  '#' +x );

        });

    }
    
    $('.col').each( function() {
    	sliders( $(this) );
    });


	});
