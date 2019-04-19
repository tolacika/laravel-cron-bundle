$(document).ready(function() {
    $("#cron-bundle #create-schedule-select").change(function () {
        $("#cron-bundle #create-schedule").val($(this).val());
    });

    $("#cron-bundle #create-schedule").keyup(function () {
        $("#cron-bundle #create-schedule-select").val("");
    })
});
