function showDate(time){
	var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
		diff = (((new Date()).getTime() - date.getTime()) / 1000),
		
		minutes_diff = Math.floor(diff / 60);
		hours_diff = Math.floor(minutes_diff / 60);
		day_diff = Math.floor(diff / 86400);
		month_diff = Math.round(day_diff / 30.5);
		year_diff = Math.floor(month_diff / 12);
			
	if ( isNaN(day_diff) || day_diff < 0)
		return;
    /*if(year_diff >0) {
         text = year_diff + " year" + ((year_diff != 1) ? "s" : '');
         if(month_diff > (12*year_diff)) {
            text += " and " + (month_diff-(12*year_diff)) + " month" + (((month_diff-(12*year_diff)) != 1) ? "s" : '');
         }
         text += " ago";
    }*/ 
    if(year_diff == 0 && month_diff >0) {
         text = month_diff + " month" + ((month_diff != 1) ? "s" : '');
         if(day_diff > (30*month_diff)) {
           text += " and " + (day_diff-(30*month_diff)) + " day" + (((day_diff-(30*month_diff)) != 1) ? "s" : '');
         }
         text += " ago";
    }
    if(year_diff == 0 && month_diff == 0 && day_diff > 0) {
         /*if(day_diff == 1) {
            text = 'Yesterday ' + date.getHours() + ':' + date.getMinutes();
         } else {*/
             text = day_diff + " days";
             text += " ago";
         }
    }
    if(hours_diff < 24) {
        text = hours_diff + " hour" + ((hours_diff != 1) ? "s" : '');
        if((minutes_diff-(60*hours_diff))>0) {
            text += " and " + (minutes_diff -(60*hours_diff)) + " minute" + (((minutes_diff-(60*hours_diff)) != 1) ? "s" : '');
        }
        text += " ago";
    }
    if(minutes_diff < 60) {
        text = minutes_diff + " minute" + ((minutes_diff != 1) ? "s" : '');
        text += " ago";
    }
    if(diff < 60) {
        text = Math.floor(diff) + " second" + ((Math.floor(diff) != 1) ? "s" : '');
    }
    return text;
}
$('document').ready(function () {
    $('.showdate').each(function (i) {
        $(this).text(showDate($(this).data('date')));
    });
});
