$('document').ready(function () {
    //Build times array
    function buildTimes(time){
        if ( typeof buildTimes.times == 'undefined' ) {
            buildTimes.times = new Array;
        }
        buildTimes.times.push(time);
    }

    //Request formated dates
    function getDates() {
        getDates.formated = new Array;
        var get="?";
        $(buildTimes.times).each( function (i,time) {
            if(i != 0) {
                get += '&';
            }
            get += 'time[]=' + time + '';
        });
        //Get json
        
        $.getJSON('/api/showdate'+get,function(data) {
            $(data.times).each(function(i,line) {
                $('.showdate[data-date='+line.key+']').text(line.text);
            });
        });
    }

    //show formated dates
    function showDate(time) {
        
    }

    //Get dates to be formated
    $('.showdate').each(function (i) {
        $(this).html('<img src="/gfx/ajax/loading.gif">');
        buildTimes($(this).data('date'));
    });
    //Get formated dates
    getDates();
});
