(function (global, $) {
    "use strict";

    $(function () {
        var workStart = $("#_work-start");
        var workEnd = $("#_work-end");
        var workLunch = $("#_work-lunch");
        var workWorkedHours = $("#_work-worked-hours");

        workStart.attr("readOnly", "readOnly");
        workEnd.attr("readOnly", "readOnly");
        workLunch.attr("readOnly", "readOnly");

        var workRangeSlider = $("#work-range-slider");
        var workLunchSlider = $("#work-lunch-slider");

        workRangeSlider.slider({
            range: true,
            min: 0,
            max: 1410,
            step: 30,
            values: [global.tc.time2Minutes(workStart.val()), global.tc.time2Minutes(workEnd.val())],
            slide: function( event, ui ) {
                var lunchMinutes = global.tc.timeWithComma2Minutes(workLunch.val());
                var workedTime = ui.values[1] - ui.values[0] - lunchMinutes;
                if (workedTime < 0) {
                    return false;
                }

                workStart.val(global.tc.minutes2Time(ui.values[0]));
                workEnd.val(global.tc.minutes2Time(ui.values[1]));

                workWorkedHours.val(global.tc.minutes2TimeWithComma(workedTime));
            }
        });

        workLunchSlider.slider({
            range: false,
            min: 0,
            max: 300,
            step: 30,
            value: global.tc.timeWithComma2Minutes(workLunch.val()),
            slide: function (event, ui) {
                var time = ui.value;
                var wsMinutes = global.tc.time2Minutes(workStart.val());
                var weMinutes = global.tc.time2Minutes(workEnd.val());

                var workedTime = weMinutes - wsMinutes - ui.value;

                if (workedTime < 0) {
                    return false;
                }

                workLunch.val(global.tc.minutes2TimeWithComma(time));

                workWorkedHours.val(global.tc.minutes2TimeWithComma(weMinutes - wsMinutes - ui.value));
            }
        });

    });

}(window, window.jQuery));