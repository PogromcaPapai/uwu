
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
            if (status != 200)
                return;
            data.forEach(d => {
                var node = $("template#warn").content.cloneNode(true);
                $(`#infos-${attend_id}`).append(format_by_id(node, d));
            });
        }
    );

    // Get forecast
    $.get(
        `/api/forecast/attend/${attend_id}`,
        function (data, status) {
            if (status != 200)
                return;
            data.forEach(d => {
                var node = $("template#forecast").content.cloneNode(true);
                $(`#infos-${attend_id}`).append(format_by_id(node, d));
            });
        }
    );

    // Get current
    $.get(
        `/api/current/attend/${attend_id}`,
        function (data, status) {
            if (status != 200)
                return;
            data.forEach(d => {
                var node = $("template#current").content.cloneNode(true);
                $(`#infos-${attend_id}`).append(format_by_id(node, d));
            });
        }
    );
}