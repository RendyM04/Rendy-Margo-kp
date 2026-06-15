// PT. Rinjani Inti Karya Solusi - Main Script

document.addEventListener('DOMContentLoaded', function () {

    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // Modal open/close
    document.querySelectorAll('[data-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const modalId = btn.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('active');
        });
    });

    document.querySelectorAll('.modal-close, [data-dismiss="modal"]').forEach(function (el) {
        el.addEventListener('click', function () {
            const modal = el.closest('.modal-overlay');
            if (modal) modal.classList.remove('active');
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) overlay.classList.remove('active');
        });
    });

    // Live search filter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const val = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    }

    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Yakin ingin menghapus data ini?')) e.preventDefault();
        });
    });

    // Draw donut chart
    const canvas = document.getElementById('proyekChart');
    if (canvas) {
        drawDonutChart(canvas);
    }

    // Auto close alert
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });
});

function drawDonutChart(canvas) {
    const ctx = canvas.getContext('2d');
    const cx = canvas.width / 2;
    const cy = canvas.height / 2;
    const r = Math.min(cx, cy) - 20;
    const hole = r * 0.55;

    // Data from PHP (passed via data attributes)
    const berjalan = parseInt(canvas.dataset.berjalan || 60);
    const selesai = parseInt(canvas.dataset.selesai || 30);
    const tertunda = parseInt(canvas.dataset.tertunda || 10);
    const total = berjalan + selesai + tertunda || 1;

    const slices = [
        { value: berjalan / total, color: '#2563eb' },
        { value: selesai / total, color: '#10b981' },
        { value: tertunda / total, color: '#ef4444' }
    ];

    let startAngle = -Math.PI / 2;
    slices.forEach(function (s) {
        const endAngle = startAngle + s.value * 2 * Math.PI;
        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.arc(cx, cy, r, startAngle, endAngle);
        ctx.closePath();
        ctx.fillStyle = s.color;
        ctx.fill();
        startAngle = endAngle;
    });

    // Hole
    ctx.beginPath();
    ctx.arc(cx, cy, hole, 0, 2 * Math.PI);
    ctx.fillStyle = '#ffffff';
    ctx.fill();
}

function openEditModal(data) {
    for (let key in data) {
        const el = document.getElementById('edit_' + key);
        if (el) el.value = data[key];
    }
    document.getElementById('editModal').classList.add('active');
}
