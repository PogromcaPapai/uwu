
function format_by_id(attend_id, info) {
    for (let key in info) {
        $(`#infos-${attend_id} #${key}`).text(info[key]);
    }
}

window.weather_getter = (attend_id) => {
    // Get alerts
    $.get(
        `/api/alerts/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            data.forEach(d => {
                $(`#infos-${attend_id}`).append($("div#warn > div").clone());
                format_by_id(attend_id, d);
            });
        }
    );

    // Get forecast
    $.get(
        `/api/forecast/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            $(`#infos-${attend_id}`).append($("div#forecast > div").clone());
            format_by_id(attend_id, data['forecast']);
        }
    );

    // Get current
    $.get(
        `/api/current/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            $(`#infos-${attend_id}`).append($("div#current > div").clone());
            format_by_id(attend_id, data);
        }
    );
}