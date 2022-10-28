
function format_by_id(id, info) {
    for (let key in info) {
        $(id).text(info[key]);
    }
}

window.weather_getter = (attend_id) => {
    // Get alerts
    $.get(
        `/api/alerts/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            data.forEach((d, i) => {
                var element = $("div#warn > div").clone();
                element[0].id = `info-warn-${attend_id}-${i}`
                $(`#infos-${attend_id}`).append(element);
                format_by_id(element[0].id, d);
            });
        }
    );

    // Get forecast
    $.get(
        `/api/forecast/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            var element = $("div#forecast > div").clone();
            element[0].id = `info-forecast-${attend_id}`
            $(`#infos-${attend_id}`).append(element);
            format_by_id(element[0].id, data['forecast']);
        }
    );

    // Get current
    $.get(
        `/api/current/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            var element = $("div#current > div").clone();
            element[0].id = `info-current-${attend_id}`
            $(`#infos-${attend_id}`).append(element);
            format_by_id(element[0].id, data);
        }
    );
}