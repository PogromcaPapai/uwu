
function format_by_id(node, info) {
    for (let key in info) {
        node.getElementsById(key).forEach(element => {
            element.innerText = info[key];
        })
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
                var node = $("template#warn > div").cloneNode(true);
                $(`#infos-${attend_id}`).append(format_by_id(node, d));
            });
        }
    );

    // Get forecast
    $.get(
        `/api/forecast/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            var node = $("template#forecast > div").cloneNode(true);
            $(`#infos-${attend_id}`).append(format_by_id(node, data['forecast']));
        }
    );

    // Get current
    $.get(
        `/api/current/attend/${attend_id}`,
        function (data, status) {
            if (status != "success")
                return;
            var node = $("template#current > div").cloneNode(true);
            $(`#infos-${attend_id}`).append(format_by_id(node, data));
        }
    );
}