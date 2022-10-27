
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
        `${API_URL}/alerts/attend/${attend_id}`,
        function (data, status) {
            if (status != 200)
                return;
            data.forEach(d => {
                var node = $("template#warn").content.cloneNode(true);
                format_by_id(node, d);
                $('#infos').append(node);
            });
        }
    );

    // Get forecast
    $.get(
        `${API_URL}/forecast/attend/${attend_id}`,
        function (data, status) {
            if (status != 200)
                return;
            data.forEach(d => {
                var node = $("template#forecast").content.cloneNode(true);
                format_by_id(node, d);
                $('#infos').append(node);
            });
        }
    );

    // Get current
    $.get(
        `${API_URL}/current/attend/${attend_id}`,
        function (data, status) {
            if (status != 200)
                return;
            data.forEach(d => {
                var node = $("template#current").content.cloneNode(true);
                format_by_id(node, d);
                $('#infos').append(node);
            });
        }
    );
}