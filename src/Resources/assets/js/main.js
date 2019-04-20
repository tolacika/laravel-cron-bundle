$(document).ready(function() {
    $("#cron-bundle #create-schedule-select").change(function () {
        $("#cron-bundle #create-schedule").val($(this).val());
    });

    $("#cron-bundle #create-schedule").keyup(function () {
        $("#cron-bundle #create-schedule-select").val("");
    });

    $('#outputModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var reportId = button.data('report-id');
        var url = $('meta[name="report-output-url"]').attr('content');

        var modal = $(this);
        $.ajax(url + "/" + reportId).done(function(output) {
            modal.find('.modal-body samp').text(output);
        });

        //var modal = $(this);
        //modal.find('.modal-title').text('New message to ' + recipient);
        //modal.find('.modal-body input').val(recipient);
    }).on('hidden.bs.modal', function() {
        $(this).find('.modal-body samp').text('Loading...');
    });
});
