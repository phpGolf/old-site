$(document).ready(function() {
    //Spaces
    $('input[type=checkbox][name=space]').change(function() {
        var type = $(this).attr('class');
        var root = this;
        $('span.'+type).each(function(i,line) {
            var text = $(line).text();
            if($(root).attr('checked')) {
                $(line).data('old',$(line).text());
                $(line).html(function(i,html) {
                    html.replace(/\n/g,'<span class="debug" style="color: red;">*</span>');
                    return html.replace(/\s/g,'<span class="debug" style="color: green;">*</span>');
                });
            } else {
                $(line).text($(line).data('old'));
            }
        });
    });
});
