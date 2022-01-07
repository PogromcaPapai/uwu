{{-- Plik przechowujący skrypt generujący kalendarz, "wczepienie go" w inny plik wygeneruje w nim kalendarz --}}
<script>
    function resizer(trash = null) {
        if ($(window).width() < 514) {
            $('#calendar').fullCalendar('changeView', 'listWeek');
            $('.fc-right').hide()
        } else {
            $('#calendar').fullCalendar('changeView', 'month');
            $('.fc-right').show()
        }
    }
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var calendar = $('#calendar').fullCalendar({
            editable: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,listWeek'
            },
            events: '/full-calender',
            selectable: true,
            selectHelper: true,
            windowResize: resizer,
            select: function(start, end, allDay) {
                var start = $.fullCalendar.formatDate(start, 'Y-MM-DD HH:mm:ss');

                var end = $.fullCalendar.formatDate(end, 'Y-MM-DD HH:mm:ss');

                $(location).attr("href", `/events/add/calendar?start=${start}&end=${end}`);
            },
            editable: true,
            eventAllow: function(drop, event) {
                return event.is_admin;
            },
            eventResize: function(event, delta) {
                var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD HH:mm:ss');
                var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD HH:mm:ss');
                var id = event.id;
                $.ajax({
                    url: "/full-calender/action",
                    type: "POST",
                    data: {
                        start: start,
                        end: end,
                        id: id,
                    },
                    success: function(response) {
                        calendar.fullCalendar('refetchEvents');
                    }
                })
            },
            eventDrop: function(event, delta) {
                var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD HH:mm:ss');
                var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD HH:mm:ss');
                var id = event.id;
                $.ajax({
                    url: "/full-calender/action",
                    type: "POST",
                    data: {
                        start: start,
                        end: end,
                        id: id,
                    },
                    success: function(response) {
                        calendar.fullCalendar('refetchEvents');
                    }
                })
            },

            eventClick: function(event) {
                var id = event.id;
                $(location).attr("href", `/events/edit/${id}`);
            }
        });

        resizer()
    });
</script>
<div id='calendar'></div>
