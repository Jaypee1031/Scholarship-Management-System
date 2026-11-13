document.addEventListener("DOMContentLoaded", function() {
    // Auto dismiss alerts
    setTimeout(function() {
        var alerts = document.querySelectorAll(".alert-dismissible");
        alerts.forEach(function(a) {
            try {
                var bsAlert = new bootstrap.Alert(a);
                bsAlert.close();
            } catch(e) {}
        });
    }, 6000);

    // Sidebar toggle for mobile
    var sidebarToggle = document.getElementById("sidebarToggle");
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function() {
            var sidebar = document.getElementById("sidebar");
            if (sidebar) sidebar.classList.toggle("d-none");
        });
    }
});
