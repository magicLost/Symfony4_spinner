$(function(){
    //settings wrapper div
    var winHeight = $(window).height();

    var mainDiv = $('#wrapper');

    var widthWrapper = $('#wrapper').width();

    mainDiv.css({
        'min-height': winHeight * 0.7
    });


    //LOCALE DIV
    const div_change_locale = $('div#div_locale_form');

    div_change_locale.css({
        'width': widthWrapper / 13,
        'float': 'right',
        'display': 'block'
    });

    const select = div_change_locale.find('select');

    select.on('change', function (event) {

        select.parent().submit();

    });
    //END LOCALE DIV


    $('div#wrapper').css({
        'display': 'block'
    });



});

