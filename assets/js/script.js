// Add to your schedule.php
document.querySelectorAll('.btn-danger').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to remove this availability slot?')) {
            e.preventDefault();
        }
    });
});