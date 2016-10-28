$(document).ready(function() {
    //Reset
    $('input[name=reset]').click(function() {
        $('input[name=title]').attr('value','');
        $('textarea[name=text]').text('');
        $('textarea[name=text]').removeAttr('disabled');
        $('input[name=link]').removeAttr('checked');
//        $('input[name=link]').attr('checked','checked');
        $('input[name=bitly]').removeAttr('checked');
        $('input[name=twitter]').removeAttr('checked');
    });
    
    //Challenges
    $('select[name=challenge]').change(function () {
        if($('select[name=challenge] option:selected').attr('value') == 0) {
            $('input[name=link][value=challenge]').attr('disabled','disabled');
            if($('input[name=link][value=challenge]').attr('checked')) {
                $('input[name=link][value=none]').attr('checked','checked');
            }
            $('input[name=link][value=challenge]').removeAttr('checked');
        } else {
            $('input[name=link]').removeAttr('disabled');
        }
    });
    
    //Preset
    $('select[name=preset]').change(function () {
        var id = $('select[name=preset] option:selected').attr('value');
        //None
        if(id == 0) {
            $('input[name=title]').attr('value','');
            $('textarea[name=text]').text('');
            $('textarea[name=text]').removeAttr('disabled');
            $('input[name=link]').removeAttr('checked');
            $('input[name=bitly]').removeAttr('checked');
            $('input[name=twitter]').removeAttr('checked');
        }
        //New Challenge
        var challenge = $('select[name=challenge] option:selected');
        if(id == 1 && challenge.attr('value') != 0) {
            $('input[name=title]').attr('value','New challenge up!');
            $('textarea[name=text]').attr('disabled','disabled');
            $('input[name=link]').attr('checked','checked');
            $('input[name=twitter]').attr('checked','checked');
            $('input[name=bitly]').attr('checked','checked');
        }
    });
});
