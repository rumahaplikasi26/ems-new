<div class="card">
    <div class="card-body">
        <div class="clearfix">
            <h4 class="card-title mb-4">{{ __('ems.working_day_analytic') }}</h4>
        </div>

        <div class="text-muted text-center">
            <p class="mb-2">{{ __('ems.total_working_days') }}</p>
            <h4>{{ $total_working_days }} {{ __('ems.days') }}</h4>
            <p class="mt-3 mb-0">{{ str_replace('{date}', formatDate($this->authUser->employee->join_date, 'd F Y'), __('ems.from_to_today')) }}</p>
        </div>

        <div class="table-responsive mt-3">
            <table class="table align-middle mb-0">
                <tbody>
                    <tr>
                        <td>
                            <h5 class="font-size-14 mb-1">{{ __('ems.present') }}</h5>
                            <p class="text-muted mb-0">{{ $total_present_days }} {{ __('ems.days') }}</p>
                        </td>

                        <td>
                            <div id="chart-present" data-colors='["--bs-primary"]' class="apex-charts"></div>
                        </td>
                        <td>
                            <p class="text-muted mb-1">{{ __('ems.percentage') }}</p>
                            <h5 class="mb-0">{{ $percentage_present }} %</h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5 class="font-size-14 mb-1">{{ __('ems.absent') }}</h5>
                            <p class="text-muted mb-0">{{ $total_absent_days }} {{ __('ems.days') }}</p>
                        </td>

                        <td>
                            <div id="chart-absent" data-colors='["--bs-success"]' class="apex-charts"></div>
                        </td>
                        <td>
                            <p class="text-muted mb-1">{{ __('ems.percentage') }}</p>
                            <h5 class="mb-0">{{ $percentage_absent }} %</h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5 class="font-size-14 mb-1">{{ __('ems.leave') }}</h5>
                            <p class="text-muted mb-0">{{ $total_leave_days }} {{ __('ems.days') }}</p>
                        </td>

                        <td>
                            <div id="chart-leave" data-colors='["--bs-danger"]' class="apex-charts"></div>
                        </td>
                        <td>
                            <p class="text-muted mb-1">{{ __('ems.percentage') }}</p>
                            <h5 class="mb-0">{{ $percentage_leave }} %</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @push('js')
        <script>
            var chartPresent;
            var chartAbsent;
            var chartLeave;

            function renderChartPresent() {
                const options = {
                    series: [{{ $percentage_present }}],
                    chart: {
                        type: "radialBar",
                        width: 60,
                        height: 60,
                        sparkline: {
                            enabled: !0
                        },
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    colors: getChartColorsArray("chart-present"),
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                margin: 0,
                                size: "60%"
                            },
                            track: {
                                margin: 0
                            },
                            dataLabels: {
                                show: !1
                            },
                        },
                    },
                };

                if (chartPresent) {
                    chartPresent.destroy(); // ⛔ penting: hapus chart sebelumnya
                }

                chartPresent = new ApexCharts(document.getElementById("chart-present"), options);
                chartPresent.render();
            }

            function renderChartAbsent() {
                const options = {
                    series: [{{ $percentage_absent }}],
                    chart: {
                        type: "radialBar",
                        width: 60,
                        height: 60,
                        sparkline: {
                            enabled: !0
                        },
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    colors: getChartColorsArray("chart-absent"),
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                margin: 0,
                                size: "60%"
                            },
                            track: {
                                margin: 0
                            },
                            dataLabels: {
                                show: !1
                            },
                        },
                    },
                };

                if (chartAbsent) {
                    chartAbsent.destroy(); // ⛔ penting: hapus chart sebelumnya
                }

                chartAbsent = new ApexCharts(document.getElementById("chart-absent"), options);
                chartAbsent.render();
            }

            function renderChartLeave() {
                const options = {
                    series: [{{ $percentage_leave }}],
                    chart: {
                        type: "radialBar",
                        width: 60,
                        height: 60,
                        sparkline: {
                            enabled: !0
                        },
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    colors: getChartColorsArray("chart-leave"),
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                margin: 0,
                                size: "60%"
                            },
                            track: {
                                margin: 0
                            },
                            dataLabels: {
                                show: !1
                            },
                        },
                    },
                };

                if (chartLeave) {
                    chartLeave.destroy(); // ⛔ penting: hapus chart sebelumnya
                }

                chartLeave = new ApexCharts(document.getElementById("chart-leave"), options);
                chartLeave.render();
            }

            document.addEventListener("livewire:init", function() {
                renderChartPresent();
                renderChartAbsent();
                renderChartLeave();
            });
        </script>
    @endpush
</div>
