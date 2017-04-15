(function (global, $) {
    "use strict";

    $(function () {
        var workStart = $("#_work-start");
        var workEnd = $("#_work-end");
        var workLunchStart = $("#_work-lunch-start");
        var workLunchEnd = $("#_work-lunch-end");

        workStart.attr("readOnly", "readOnly");
        workEnd.attr("readOnly", "readOnly");
        workLunchStart.attr("readOnly", "readOnly");
        workLunchEnd.attr("readOnly", "readOnly");

        var workedHours = $("#_worked-hours");
        workedHours.append("<div class=\"form-group\"><label for=\"_work-worked-hours\">Odpr. hod.</label><input type=\"text\" id=\"_work-worked-hours\" class=\"form-control\" value=\"" + workedHours.data("workedHours") + "\" disabled></div>");

        var workWorkedHours = $("#_work-worked-hours");

        var workedHoursSlider = $("#worked-hours-slider");
        var workLunchSlider = $("#work-lunch-slider");

        workedHoursSlider.slider({
            range: true,
            min: 0,
            max: 1410,
            step: 30,
            values: [global.tc.time2Minutes(workStart.val()), global.tc.time2Minutes(workEnd.val())],
            slide: function( event, ui ) {
                var lStart = global.tc.time2Minutes(workLunchStart.val());
                var lEnd = global.tc.time2Minutes(workLunchEnd.val());
                if (ui.values[0] > lStart || ui.values[1] < lEnd) {
                    return false;
                }

                var lunchMinutes = lEnd - lStart;
                var workedTime = ui.values[1] - ui.values[0] - lunchMinutes;
                if (workedTime < 30) {
                    return false;
                }

                workStart.val(global.tc.minutes2Time(ui.values[0]));
                workEnd.val(global.tc.minutes2Time(ui.values[1]));

                workWorkedHours.val(global.tc.minutes2TimeWithComma(workedTime));
            }
        });

        workLunchSlider.slider({
            range: true,
            min: 0,
            max: 1410,
            step: 30,
            values: [global.tc.time2Minutes(workLunchStart.val()), global.tc.time2Minutes(workLunchEnd.val())],
            slide: function( event, ui ) {
                var wStart = global.tc.time2Minutes(workStart.val());
                var wEnd = global.tc.time2Minutes(workEnd.val());
                if (ui.values[0] > ui.values[1] || wStart > ui.values[0] || wEnd < ui.values[1]) {
                    return false;
                }

                var lunchMinutes = ui.values[1] - ui.values[0];
                var workedTime = wEnd - wStart - lunchMinutes;
                if (workedTime < 0) {
                    return false;
                }

                workLunchStart.val(global.tc.minutes2Time(ui.values[0]));
                workLunchEnd.val(global.tc.minutes2Time(ui.values[1]));

                workWorkedHours.val(global.tc.minutes2TimeWithComma(workedTime));
            }
        });


        // -----


        var nullTimeButton = $("#_null-time-button");
        nullTimeButton.on("click", function (e) {
            workStart.val("0:00");
            workEnd.val("0:00");
            workLunchStart.val("0:00");
            workLunchEnd.val("0:00");
            workWorkedHours.val("0");

            workedHoursSlider.slider("values", 0, 0);
            workedHoursSlider.slider("values", 1, 0);
            workLunchSlider.slider("values", 0, 0);
            workLunchSlider.slider("values", 1, 0);
        });

        var lunchNullTimeButton = $("#_lunch-null-time-button");
        lunchNullTimeButton.on("click", function (e) {
            workLunchStart.val("0:00");
            workLunchEnd.val("0:00");

            workLunchSlider.slider("values", 0, 0);
            workLunchSlider.slider("values", 1, 0);

            var wStartMinutes = global.tc.time2Minutes(workStart.val());
            var wEndMinutes = global.tc.time2Minutes(workEnd.val());
            var _workedHours = wEndMinutes - wStartMinutes;

            workWorkedHours.val(global.tc.minutes2TimeWithComma(_workedHours));
        });
    });

}(window, window.jQuery));