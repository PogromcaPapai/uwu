
function format_by_id(attend_id, info) {
    for (let key in info) {
        $(`#infos-${attend_id} #${key}`).text(info[key]);
    }
    return node
}

window.weather_getter = (attend_id) => {
    // Get alerts
    $.get(
        `/api/alerts/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            data.forEach(d => {
                var node = $("div#warn > div").clone();
                $(`#infos-${attend_id}`).append(format_by_id(attend_id, d));
            });
        }
    );

    // Get forecast
    $.get(
        `/api/forecast/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            var node = $("div#forecast > div").clone();
            $(`#infos-${attend_id}`).append(format_by_id(node, data['forecast']));
        }
    );

    // Get current
    $.get(
        `/api/current/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            var node = $("div#current > div").clone();
            $(`#infos-${attend_id}`).append(format_by_id(node, data));
        }
    );
}