$(document).ready(function () {
$.msg = function msg(msg,type) {
    $('div.msgs div:hidden').remove();
    var types = Array;
    types[0] = 'error';
    types[1] = 'ok';
    types[2] = 'debug';
    var html = "\t\t<div class=\""+types[type]+"\" style=\"display: none;\">\n";
    html += "\t\t\t"+msg+"\n";
    html += "\t\t</div>";
    var i=0;
    $('div.msgs').append(html);
    $('div.msgs div:last-child').slideDown('slow');
    if($('div.msgs div:visible').size() >3) {
        target = $('div.msgs div:visible').size() - 3;
        $('div.msgs div:visible').each(function (index) {
            if(index < target) {
                $(this).slideUp('slow');
            }
        });
    }
}
});
