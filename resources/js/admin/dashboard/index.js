import Chart from 'chart.js/auto';
import Calendar from '@toast-ui/calendar';
import '@toast-ui/calendar/dist/toastui-calendar.min.css';
import 'tui-date-picker/dist/tui-date-picker.css';
import 'tui-time-picker/dist/tui-time-picker.css';

$(document).ready(function() {
    const baseUrl = window.location.href.split('/admin')[0];
    var last12Months = getLastMonths(moment());

    // Charts
    var patientGrowthChart;
    var patientsBySpecializationChart;
    var consultationsByStatusChart;
    var appointmentsByStatusChart;

    // Calendar
    var providerCalendar;
    var currentCalendarView = 'month';

    initialize();

    function initialize() {
        initPatientGrowth();
        initPatientsBySpecialization();
        initConsultationsByStatus();
        initAppointmentsByStatus();

        initProviderCalender();

        $('#dashboard-date').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": false,
        }, function(start, end, label) {
            refreshDashboardData(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'));
        });

        refreshDashboardData(moment().format('MM/DD/YYYY'), moment().format('MM/DD/YYYY'));
    }

    function initPatientGrowth() {
        const canvas = $("canvas#patientGrowth");

        if (!canvas.length) {
            return;
        } else {
            // Set custom gradient background color for patient growth
            var backgroundColor = canvas[0].getContext("2d").createLinearGradient(0, 0, 0, 225);
            backgroundColor.addColorStop(0, "rgba(215, 227, 244, 1)");
            backgroundColor.addColorStop(1, "rgba(215, 227, 244, 0)");

            var chartData = {
                labels: last12Months,
                datasets: [{
                    label: "Patients",
                    backgroundColor: backgroundColor,
                    borderColor: "#0090BA",
                    fill: true,
                }],
            };

            chartData.datasets[0]['data'] = [];

            patientGrowthChart = new Chart(canvas, {
                type: "line",
                data: chartData,
                options: {
                    maintainAspectRatio: false,
                    legend: { display: false },
                    tooltips: { intersect: false },
                    hover: { intersect: true },
                    plugins: { filler: { propagate: false } }
                }
            });
        }
    }

    function initPatientsBySpecialization() {
        const canvas = $("canvas#patientsBySpecialization");

        if (!canvas.length) {
            return;
        } else {
            patientsBySpecializationChart = new Chart(canvas, {
                type: "doughnut",
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: generateRandomColors(5),
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: !window.MSInputMethodContext,
                    maintainAspectRatio: false,
                    legend: { display: false },
                    cutoutPercentage: 75
                }
            });
        }
    }

    function initConsultationsByStatus() {
        const canvas = $("canvas#consultationsByStatus");

        if (!canvas.length) {
            return;
        } else {
            var chartData = {
                labels: last12Months,
                datasets: [
                    {
                        label: "Pending",
                        backgroundColor: "transparent",
                        borderColor: "#3333",
                        borderDash: [4, 4],
                        fill: true,
                        data: []
                    },
                    {
                        label: "Completed",
                        backgroundColor: "transparent",
                        borderColor: '#0090BA',
                        fill: true,
                        data: []
                    },
                ],
            };

            consultationsByStatusChart = new Chart(canvas, {
                type: "line",
                data: chartData,
                options: {
                    maintainAspectRatio: false,
                    legend: { display: false },
                    tooltips: { intersect: false },
                    hover: { intersect: true },
                    plugins: { filler: { propagate: false } },
                }
            });
        }
    }

    function initAppointmentsByStatus() {
        const canvas = $("canvas#appointmentsByStatus");

        if (!canvas.length) {
            return;
        } else {
            var chartData = {
                labels: last12Months,
                datasets: [
                    {
                        label: "Pending",
                        backgroundColor: "transparent",
                        borderColor: "#3333",
                        borderDash: [4, 4],
                        fill: true,
                        data: []
                    },
                    {
                        label: "Completed",
                        backgroundColor: "transparent",
                        borderColor: '#0090BA',
                        fill: true,
                        data: []
                    },
                ],
            };

            appointmentsByStatusChart = new Chart(canvas, {
                type: "line",
                data: chartData,
                options: {
                    maintainAspectRatio: false,
                    legend: { display: false },
                    tooltips: { intersect: false },
                    hover: { intersect: true },
                    plugins: { filler: { propagate: false } }
                }
            });
        }
    }

    function initProviderCalender() {
        var startDate = moment().startOf('month').format('YYYY-MM-DD');
        var endDate = moment().endOf('month').format('YYYY-MM-DD');

        providerCalendar = new Calendar('#provider-calendar', {
            usageStatistics: false,
            defaultView: currentCalendarView,
            week: {
                taskView: false,
                eventView: true,
            },
            template: {
                popupDetailAttendees({ attendees = [] }) {
                    return attendees;
                },
                popupDetailDate({ start, end }) {
                    let startDate = moment(start.toDate()).format("dddd, MMMM DD h:mm a");
                    return `${startDate}`;
                },
                time(event) {
                    const { start, end, title } = event;
                    return `<span style="color: white;">${title}</span>`;
                },
                allday(event) {
                    return `<span style="color: gray;">${event.title}</span>`;
                },            
                weekGridFooterExceed(hiddenEvents) {
                    return `+${hiddenEvents}`;
                },
            },
            calendars: [
                {
                    id: 'cancelled',
                    name: 'Cancelled',
                    backgroundColor: '#d63939',
                },
                {
                    id: 'pending',
                    name: 'Pending',
                    backgroundColor: '#667382',
                },
                {
                    id: 'completed',
                    name: 'Completed',
                    backgroundColor: '#2fb344',
                },
            ],
            useFormPopup: true,
            useDetailPopup: true,
        });

        updateCalendarLabel(currentCalendarView);
        getCalendarEvents(startDate, endDate);

        // Calendar event handlers
        $('input[name="calendarview"]').change(function() {
            currentCalendarView = $(this).val();
            providerCalendar.changeView(currentCalendarView);
            updateCalendarLabel(currentCalendarView);
        });

        $('#today').click(function() {
            providerCalendar.today();
            updateCalendarLabel(currentCalendarView);
        });

        $('#prev').click(function() {
            providerCalendar.prev();

            if( currentCalendarView !== 'day' ) {
                let start = moment(providerCalendar.getDateRangeStart().getTime()).format('YYYY-MM-DD');
                let end = moment(providerCalendar.getDateRangeEnd().getTime()).format('YYYY-MM-DD');
                getCalendarEvents(start, end);
            }

            updateCalendarLabel(currentCalendarView);
        });

        $('#next').click(function() {
            providerCalendar.next();

            if( currentCalendarView !== 'day' ) {
                let start = moment(providerCalendar.getDateRangeStart().getTime()).format('YYYY-MM-DD');
                let end = moment(providerCalendar.getDateRangeEnd().getTime()).format('YYYY-MM-DD');
                getCalendarEvents(start, end);
            }
            
            updateCalendarLabel(currentCalendarView);
        });
    }

    function refreshDashboardData(start, end) {
        $.ajax({
            url: baseUrl + '/admin/dashboard/stats',
            type: 'GET',
            data: {
                start: start,
                end: end,
            },
            dataType: 'json',
            success: function success(resp) {
                const data = resp.data;

                // Count Stats
                let stats = data.stats_count;
                $('.patient.total-count').html(stats.patient.total);
                $('.patient.percentage').html((stats.patient.percentage > 0 ? stats.patient.percentage.toFixed(2) : 0) + '%');

                $('.appointment.total-count').html(stats.appointment.total);
                $('.appointment.percentage').html((stats.appointment.percentage > 0 ? stats.appointment.percentage.toFixed(2) : 0) + '%');

                $('.consultation.total-count').html(stats.consultation.total);
                $('.consultation.percentage').html((stats.consultation.percentage > 0 ? stats.consultation.percentage.toFixed(2) : 0) + '%');

                $('.provider.total-count').html(stats.provider.total);
                $('.provider.percentage').html((stats.provider.percentage > 0 ? stats.provider.percentage.toFixed(2) : 0) + '%');

                // Patient Growth
                if (typeof patientGrowthChart !== 'undefined') {
                    patientGrowthChart.data.labels = Object.keys(data.patient_count);
                    patientGrowthChart.data.datasets[0].data = Object.values(data.patient_count);
                    patientGrowthChart.update();
                }
                
                // Patients by Specialization
                if (typeof patientsBySpecializationChart !== 'undefined') {
                    let specializations = [];
                    let specializationsPxCount = [];
    
                    data.specialization_count.forEach(item => {
                        specializations.push(item.name);
                        specializationsPxCount.push(item.patients);
                    });
    
                    if (specializationsPxCount.some(count => count > 0)) {
                        $(".pt-specialization").show();
                        $(".empty-state").addClass('d-none');
    
                        patientsBySpecializationChart.data.labels = specializations;
                        patientsBySpecializationChart.data.datasets[0].data = specializationsPxCount;
                        patientsBySpecializationChart.update();
                    } else {
                        $(".pt-specialization").hide();
                        $(".empty-state").removeClass('d-none');
                    }
                }

                // Consultations
                if (typeof consultationsByStatusChart !== 'undefined') {
                    consultationsByStatusChart.data.labels = getLastMonths(end);
                    consultationsByStatusChart.data.datasets[0].data = Object.values(data.consultation_count.pending);
                    consultationsByStatusChart.data.datasets[1].data = Object.values(data.consultation_count.completed);
                    consultationsByStatusChart.update();
                }

                // Appointments
                if (typeof appointmentsByStatusChart !== 'undefined') {
                    appointmentsByStatusChart.data.labels = getLastMonths(end);
                    appointmentsByStatusChart.data.datasets[0].data = Object.values(data.appointment_count.pending);
                    appointmentsByStatusChart.data.datasets[1].data = Object.values(data.appointment_count.completed);
                    appointmentsByStatusChart.update();
                }
            },
            error: function error(jqXHR, textStatus, errorThrown) {
                console.error('Error updating list view:', textStatus, errorThrown);
            }
        });
    }

    function getCalendarEvents(start, end) {
        $.ajax({
            url: baseUrl + '/admin/dashboard/events',
            type: 'GET',
            data: {
                start: start,
                end: end,
            },
            dataType: 'json',
            success: function success(resp) {
                providerCalendar.createEvents(resp.data);
                providerCalendar.render();
            },
            error: function error(jqXHR, textStatus, errorThrown) {
                console.error('Error updating list view:', textStatus, errorThrown);
            }
        });
    }

    function updateCalendarLabel(view) {
        switch(view) {
            case 'month':
                let month = moment(providerCalendar.getDate().getTime()).format('MMMM YYYY');
                $("#calendar-date").html(month);
                break;
            case 'week': 
                let start = moment(providerCalendar.getDateRangeStart().getTime()).format('MMM');
                let end = moment(providerCalendar.getDateRangeEnd().getTime()).format('MMM YYYY');
                $("#calendar-date").html(`${start} - ${end}`);
                break;
            case 'day':
                let date = moment(providerCalendar.getDate().getTime()).format('MMMM DD, YYYY');
                $("#calendar-date").html(date);
                break;
        }
    }

    function getLastMonths(startDate) {
        var labels = [];
        var today = moment(startDate);
        for (var i = 0; i < 12; i++) {
            labels.unshift(today.format('MMM'));
            today.subtract(1, 'months').startOf('month');
        }
        return labels;
    }

    function generateRandomColors(count) {
        var scale = chroma.scale(['#0dc8ff', '#0090ba']).mode('lab').colors(count);
        return scale;
    }
});