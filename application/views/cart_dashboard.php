<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Pembelian Konsumen</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f4f6f9; }
    .navbar-brand { font-weight: 600; }
    .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    #tableLoading { display: none; }
    .badge-user { background-color: #0d6efd; font-weight: 600; }
    tr.user-group-start td { border-top: 2px solid #dee2e6; }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0">
            <i class="bi bi-cart-check me-2"></i>Dashboard Pembelian Konsumen
        </span>
        <div class="d-flex align-items-center gap-3">
            <a href="<?= site_url('product'); ?>" class="btn btn-sm btn-outline-light">
                <i class="bi bi-box-seam me-1"></i>Master Product
            </a>
            <span class="text-light small">
                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($username ?: '-'); ?>
            </span>
            <a href="<?= site_url('auth/logout'); ?>" class="btn btn-sm btn-outline-light">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 pb-5">

    <div id="alertPlaceholder"></div>

    <div class="card">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Barang yang Mau Dibeli Setiap Konsumen</h5>
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari username atau produk...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">No</th>
                            <th>Konsumen</th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Subtotal</th>
                            <th>Terakhir Diubah</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody">
                        <tr id="tableLoading">
                            <td colspan="9" class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm me-2"></div>Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-muted small mt-2" id="summaryInfo"></div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function () {

    var BASE_URL = '<?= site_url('cart'); ?>/';
    var allRows  = [];
    var searchTimer = null;

    function showAlert(message, type) {
        var html = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        $('#alertPlaceholder').html(html);
    }

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function escapeHtml(str) {
        return $('<div>').text(str == null ? '' : str).html();
    }

    function formatDate(str) {
        if (!str) return '-';
        var d = new Date(str.replace(' ', 'T'));
        if (isNaN(d.getTime())) return str;
        return d.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function loadDashboard() {
        $('#tableLoading').show();
        $.ajax({
            url: BASE_URL + 'dashboard_data',
            method: 'POST',
            dataType: 'json'
        }).done(function (res) {
            allRows = (res && res.data) || [];
            renderTable($('#searchInput').val());
        }).fail(function () {
            allRows = [];
            $('#cartTableBody').html('<tr><td colspan="9" class="text-center text-danger py-4">Gagal memuat data.</td></tr>');
            $('#summaryInfo').text('');
        }).always(function () {
            $('#tableLoading').hide();
        });
    }

    function renderTable(search) {
        var $body = $('#cartTableBody');
        $body.empty();

        var rows = allRows;
        if (search) {
            var q = search.toLowerCase();
            rows = allRows.filter(function (r) {
                return (r.i_username || '').toLowerCase().indexOf(q) !== -1 ||
                       (r.i_product  || '').toLowerCase().indexOf(q) !== -1 ||
                       (r.e_product  || '').toLowerCase().indexOf(q) !== -1;
            });
        }

        if (rows.length === 0) {
            $body.append('<tr><td colspan="9" class="text-center text-muted py-4">Belum ada konsumen yang memilih barang untuk dibeli.</td></tr>');
            $('#summaryInfo').text('');
            return;
        }

        var grandTotal = 0;
        var userSet = {};
        var lastUser = null;

        rows.forEach(function (row, idx) {
            userSet[row.id_user] = true;
            var subtotal = row.v_price * row.n_qty;
            grandTotal += subtotal;

            var tr = $('<tr>');
            if (row.id_user !== lastUser) tr.addClass('user-group-start');
            lastUser = row.id_user;

            tr.append('<td>' + (idx + 1) + '</td>');
            tr.append('<td><span class="badge badge-user"><i class="bi bi-person-fill me-1"></i>' + escapeHtml(row.i_username) + '</span></td>');
            tr.append('<td>' + escapeHtml(row.i_product) + '</td>');
            tr.append('<td>' + escapeHtml(row.e_product) + '</td>');
            tr.append('<td>' + escapeHtml(row.e_category || '-') + '</td>');
            tr.append('<td class="text-end">' + formatRupiah(row.v_price) + '</td>');
            tr.append('<td class="text-end">' + row.n_qty + '</td>');
            tr.append('<td class="text-end">' + formatRupiah(subtotal) + '</td>');
            tr.append('<td>' + formatDate(row.dt_updated) + '</td>');

            $body.append(tr);
        });

        var userCount = Object.keys(userSet).length;
        $('#summaryInfo').text(userCount + ' konsumen, ' + rows.length + ' baris barang, total ' + formatRupiah(grandTotal) + '.');
    }

    $('#searchInput').on('keyup', function () {
        var val = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () { renderTable(val); }, 250);
    });

    loadDashboard();
});
</script>
</body>
</html>
