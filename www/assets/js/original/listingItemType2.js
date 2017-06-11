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

        var workNullTimeButton = $("#_null-time-button");
        var lunchNullTimeButton = $("#_lunch-null-time-button");

        var workStartMinutes = global.tc.time2Minutes(workStart.val());
        var workEndMinutes = global.tc.time2Minutes(workEnd.val());
        var lunchStartMinutes = global.tc.time2Minutes(workLunchStart.val());
        var lunchEndMinutes = global.tc.time2Minutes(workLunchEnd.val());

        if (workStartMinutes === 0 && workEndMinutes === 0) {
            lunchNullTimeButton.prop("disabled", true);
        }

        if (lunchStartMinutes === 0 && lunchEndMinutes === 0) {
            lunchNullTimeButton.text("S obědem");
            lunchNullTimeButton.toggleClass("withoutLunch");
        }

        workedHoursSlider.slider({
            range: true,
            min: 0,
            max: 1410,
            step: 30,
            values: [global.tc.time2Minutes(workStart.val()), global.tc.time2Minutes(workEnd.val())],
            slide: function( event, ui ) {
                var lStart = global.tc.time2Minutes(workLunchStart.val());
                var lEnd = global.tc.time2Minutes(workLunchEnd.val());
                var wStart = ui.values[0];
                var wEnd = ui.values[1];
                if (lStart !== 0 || lEnd !== 0) {
                    if (wStart > lStart || wEnd < lEnd) {
                        return false;
                    }
                }

                var workedTime = ui.values[1] - ui.values[0] - (lEnd - lStart);
                if (workedTime < 30) {
                    lunchNullTimeButton.prop("disabled", true);
                    return false;
                }

                if (lunchNullTimeButton.prop("disabled") === true) {
                    lunchNullTimeButton.prop("disabled", false);
                }

                if (lunchNullTimeButton.prop("disabled") === true) {
                    lunchNullTimeButton.prop("disabled", false);
                }

                if (lStart === 0 && lEnd === 0) {
                    lunchNullTimeButton.text("S obědem");
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
                var lStart = ui.values[0];
                var lEnd = ui.values[1];

                if (lStart === 0 && lEnd === 0) {
                    lStart = wStart;
                    lEnd = wEnd;

                } else {
                    if (lStart > lEnd || lStart < wStart || lEnd > wEnd) {
                        return false;
                    }

                    if (lEnd - lStart < 30) {
                        return false;
                    }
                }

                var workedTime = wEnd - wStart - (lEnd - lStart);
                if (workedTime < 30) {
                    return false;
                }

                if (lunchNullTimeButton.hasClass("withoutLunch")) {
                    lunchNullTimeButton.text("Bez oběda");
                    lunchNullTimeButton.toggleClass("withoutLunch");
                }

                workLunchStart.val(global.tc.minutes2Time(lStart));
                workLunchEnd.val(global.tc.minutes2Time(lEnd));

                workWorkedHours.val(global.tc.minutes2TimeWithComma(workedTime));
            }
        });


        // -----


        workNullTimeButton.on("click", function (e) {
            lunchNullTimeButton.text("S obědem");
            lunchNullTimeButton.prop("disabled", true);
            if (!lunchNullTimeButton.hasClass("withoutLunch")) {
                lunchNullTimeButton.toggleClass("withoutLunch");
            }

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


        lunchNullTimeButton.on("click", function (e) {
            var wStartMinutes = global.tc.time2Minutes(workStart.val());
            var wEndMinutes = global.tc.time2Minutes(workEnd.val());
            var _workedHours = wEndMinutes - wStartMinutes;

            var self = $(this);

            if (wStartMinutes === 0 && wEndMinutes === 0) {
                return;
            }

            self.toggleClass("withoutLunch");
            if (self.hasClass("withoutLunch")) {
                self.text("S obědem");
                workLunchStart.val("0:00");
                workLunchEnd.val("0:00");

                workLunchSlider.slider("values", 0, 0);
                workLunchSlider.slider("values", 1, 0);

            } else {
                self.text("Bez oběda");
                workLunchStart.val(workStart.val());
                workLunchEnd.val(global.tc.minutes2Time(wEndMinutes - 30));
                _workedHours = 30;

                workLunchSlider.slider("values", 0, wStartMinutes);
                workLunchSlider.slider("values", 1, wEndMinutes - 30);
            }

            workWorkedHours.val(global.tc.minutes2TimeWithComma(_workedHours));
        });
    });

}(window, window.jQuery));