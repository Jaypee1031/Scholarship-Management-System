$(document).ready(function() {
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 25,
            order: []
        });
    }

    // Toggle conditional fields in evaluation
    $('input[name="decision"]').on('change', function() {
        var val = $(this).val();
        if (val === 'Disqualified') {
            $('#disqualifyReasonBox').removeClass('d-none');
            $('#revisionBox').addClass('d-none');
        } else if (val === 'For Revision') {
            $('#revisionBox').removeClass('d-none');
            $('#disqualifyReasonBox').addClass('d-none');
        } else {
            $('#disqualifyReasonBox').addClass('d-none');
            $('#revisionBox').addClass('d-none');
        }
    });
});
