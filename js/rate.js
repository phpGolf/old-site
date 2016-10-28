function rate(direction) {
    var challenge = getChallenge();
    $.getJSON('/api/challenge/'+challenge+'/ajax/rate/'+direction,function (data) {
        if(data.MSG) {
            $(data.MSG.ERROR).each(function(index,msg) {
                $.msg(msg,0);
            });
            $(data.MSG.OK).each(function(index,msg) {
                $.msg(msg,1);
            });
            $(data.MSG.DEBUG).each(function(index,msg) {
                $.msg(msg,2);
            });
        }
        updateRating();
    });
}
function updateRating() {
    var challenge = getChallenge();
    $.getJSON('/api/challenge/'+challenge+'/ajax/rating',function (data) {
        $('span#rating').text(data.rating);
    });
}

function getChallenge() {
    var path = window.location.pathname;
    var patt = /\/[a-z]+\/([0-9a-z\-\+]+)/i;
    var res = path.match(patt);
    return res[1];
}

$(document).ready(function () {
    //Update rating
    updateRating();
});
