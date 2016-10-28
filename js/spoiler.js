$(document).ready(function() {
    $(".spoiler").css('cursor','pointer');
    $(".spoiler").click(function () {
        var hide = "[name=spoiler_"+$(this).attr('name')+"]";
        if($(hide).css('display') == 'none') {
            $(hide).show('fast');
            $(hide).attr('colspan', $(hide).attr('colspan'));
        } else {
            $(hide).hide('fast');
        }
    });
});
