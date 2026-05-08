@extends('admin.layouts.master', ['is_active_parent' => 'home', 'is_active' => 'bookings'])
@section('title')
    {{ __('admin.global.bookings') }} - Calendar
@endsection

@push('styles')
    <style>
        #calendar {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* .fc-event,
                    .fc-event *,
                    .fc-daygrid-event,
                    .fc-timegrid-event,
                    .fc-list-event,
                    .fc-event-main,
                    .fc-event-title,
                    .fc-event-time {
                        cursor: pointer !important;
                        padding: 2px 4px;
                        font-size: 12px;
                    } */

        .fc a[data-navlink],
        .fc .fc-event,
        .fc .fc-event *,
        .fc .fc-event-main,
        .fc .fc-event-main *,
        .fc .fc-daygrid-event,
        .fc .fc-daygrid-event *,
        .fc .fc-timegrid-event,
        .fc .fc-timegrid-event *,
        .fc .fc-list-event,
        .fc .fc-list-event *,
        .fc .fc-event-title,
        .fc .fc-event-time,
        .fc .fc-daygrid-event-harness,
        .fc .fc-daygrid-event-harness *,
        .fc-h-event,
        .fc-h-event * {
            cursor: pointer !important;
        }

        .fc-daygrid-event {
            white-space: normal !important;
        }

        /* تفاصيل الحجز في الـ tooltip */
        .booking-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .booking-detail-row:last-child {
            border-bottom: none;
        }

        .booking-detail-label {
            font-weight: 600;
            color: #555;
            min-width: 100px;
        }

        .booking-detail-value {
            text-align: right;
            color: #333;
        }

        .fc .fc-toolbar-title {
            font-size: 1.4em;
            font-weight: 600;
        }

        .fc .fc-button-primary {
            background-color: #009ef7;
            border-color: #009ef7;
        }

        .fc .fc-button-primary:hover {
            background-color: #0095e8;
            border-color: #0095e8;
        }

        .fc .fc-button-primary.fc-button-active {
            background-color: #0078c8;
            border-color: #0078c8;
        }

        /* عرض أفضل في العرض الأسبوعي واليومي */
        .fc-timegrid-event .fc-event-main {
            padding: 4px;
            font-size: 11px;
            line-height: 1.4;
        }

        .fc-timegrid-event .event-details {
            font-size: 10px;
            opacity: 0.9;
            margin-top: 2px;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="page-content-header">
                        <div class="row justify-content-between">
                            <div class="col-3">
                                <h2 class="table-title">{{ __('admin.global.bookings') }} - Calendar</h2>
                            </div>
                            {{-- <div class="col-9">
                                <div class="d-flex justify-content-end gap-3">
                                    <a class="btn btn-primary" href="{{ route('admin.bookings.index') }}">
                                        <i class="fas fa-list me-2"></i>
                                        List View
                                    </a>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    <div class="card card-flush">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-calendar-check me-2 text-white"></i>
                        Booking Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="bookingDetailBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="cancelBookingBtn">
                        <i class="fas fa-trash me-2"></i> Cancel Booking
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventsUrl = "{{ route('admin.bookings.events') }}";
            var destroyUrlTemplate = "{{ route('admin.bookings.destroy', ':id') }}";
            var csrfToken = "{{ csrf_token() }}";
            var currentBookingId = null;

            console.log('Events URL:', eventsUrl); // للتأكد من الرابط

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'en',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                    list: 'List'
                },
                // جلب الأحداث عبر AJAX
                events: function(info, successCallback, failureCallback) {
                    fetch(eventsUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(function(response) {
                            console.log('Response status:', response.status);
                            return response.json();
                        })
                        .then(function(data) {
                            console.log('Events data:', data);
                            successCallback(data);
                        })
                        .catch(function(error) {
                            console.error('Error fetching events:', error);
                            failureCallback(error);
                        });
                },
                editable: false,
                selectable: true,
                allDaySlot: false,

                dayMaxEvents: false, // عرض جميع الحجوزات بدون "more"
                dayMaxEventRows: false,
                eventDisplay: 'block',

                // تخصيص محتوى الحدث في كل عرض
                eventContent: function(arg) {
                    var props = arg.event.extendedProps;
                    var view = arg.view.type;

                    if (view === 'timeGridWeek' || view === 'timeGridDay') {
                        // عرض تفصيلي في الأسبوعي واليومي
                        var html = '<div style="padding:2px 4px;line-height:1.3;">';
                        html += '<div><strong>' + arg.event.title + '</strong></div>';
                        html += '<div class="event-details">';
                        // if (props.email) html += '<div>📧 ' + props.email + '</div>';
                        // if (props.phone) html += '<div>📱 ' + props.phone + '</div>';
                        // if (props.persons) html += '<div>👥 ' + props.persons + ' persons</div>';
                        html += '</div></div>';
                        return {
                            html: html
                        };
                    }

                    // العرض الشهري - عنوان فقط
                    return {
                        html: '<div style="padding:1px 4px;font-size:11px;"><strong>' + arg.event
                            .title + '</strong></div>'
                    };
                },

                // عند الضغط على حدث - عرض Modal بكافة التفاصيل
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    currentBookingId = info.event.id;
                    var props = info.event.extendedProps;
                    var startDate = info.event.start;
                    var formattedDate = startDate ? startDate.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) : '-';

                    var html = '';
                    html += buildDetailRow('👤 Name: ', props.name);
                    html += buildDetailRow('📧 Email: ', props.email);
                    // html += buildDetailRow('📱 Phone', props.phone);
                    html += buildDetailRow('💇 Service: ', props.service);
                    // html += buildDetailRow('👥 Persons', props.persons);
                    html += buildDetailRow('📅 Date: ', formattedDate);
                    html += buildDetailRow('🕐 Time: ', props.time);
                    // html += buildDetailRow('💬 Message', props.message);
                    // html += buildDetailRow('📊 Status', formatStatus(props.status));

                    document.getElementById('bookingDetailBody').innerHTML = html;
                    var modal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
                    modal.show();
                },

                // لون مختلف حسب الخدمة أو الحالة
                eventDidMount: function(info) {
                    var status = info.event.extendedProps.status;
                    if (status === 'confirmed') {
                        info.el.style.backgroundColor = '#50cd89';
                        info.el.style.borderColor = '#50cd89';
                    } else if (status === 'cancelled') {
                        info.el.style.backgroundColor = '#f1416c';
                        info.el.style.borderColor = '#f1416c';
                    } else {
                        info.el.style.backgroundColor = '#3a6b93';
                        info.el.style.borderColor = '#3a6b93';
                    }
                    info.el.style.color = '#fff';
                    // إجبار cursor pointer على العنصر وكل أبنائه
                    info.el.style.cursor = 'pointer';
                    var children = info.el.querySelectorAll('*');
                    children.forEach(function(child) {
                        child.style.cursor = 'pointer';
                    });
                }
            });

            calendar.render();

            // زر إلغاء الحجز
            document.getElementById('cancelBookingBtn').addEventListener('click', function() {
                if (!currentBookingId) return;

                if (!confirm('Are you sure you want to cancel this booking?')) return;

                var btn = this;
                btn.disabled = true;

                var url = destroyUrlTemplate.replace(':id', currentBookingId);

                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return {
                                ok: response.ok,
                                data: data
                            };
                        });
                    })
                    .then(function(result) {
                        if (!result.ok) {
                            alert(result.data.message || 'Failed to cancel booking');
                            return;
                        }

                        var ev = calendar.getEventById(currentBookingId);
                        if (ev) ev.remove();

                        currentBookingId = null;
                        var modalEl = document.getElementById('bookingDetailModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    })
                    .catch(function(err) {
                        console.error(err);
                        alert('Failed to cancel booking');
                    })
                    .finally(function() {
                        btn.disabled = false;
                    });
            });

            // Helper Functions
            function buildDetailRow(label, value) {
                return '<div class="booking-detail-row">' +
                    '<span class="booking-detail-label">' + label + '</span>' +
                    '<span class="booking-detail-value">' + (value || '-') + '</span>' +
                    '</div>';
            }

            function formatStatus(status) {
                if (!status) return '<span class="badge badge-light-info">N/A</span>';
                var colors = {
                    'confirmed': 'success',
                    'pending': 'warning',
                    'cancelled': 'danger',
                };
                var color = colors[status] || 'info';
                return '<span class="badge badge-light-' + color + '">' + status.charAt(0).toUpperCase() + status
                    .slice(1) + '</span>';
            }
        });
    </script>
@endpush
