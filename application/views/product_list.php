<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Master Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f4f6f9; }
    .navbar-brand { font-weight: 600; }
    .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    .badge-status {
        font-size: .78rem;
        font-weight: 600;
        padding: .4em .65em;
    }
    .badge-habis   { background-color: #dc3545; }
    .badge-menipis { background-color: #fd7e14; }
    .badge-cukup   { background-color: #0d6efd; }
    .badge-banyak  { background-color: #198754; }
    #tableLoading { display: none; }
    .is-invalid ~ .invalid-feedback { display: block; }
    #kodeStatus { font-size: .8rem; margin-top: .25rem; display: block; }
    #kodeStatus.text-danger, #kodeStatus.text-success { display: block; }
    tr.row-deactivated { color: #adb5bd; background-color: #f8f9fa; }
    tr.row-deactivated .badge { opacity: .6; }
    tr.row-deactivated td { color: #adb5bd; }
    .active-icon.bi-check-circle-fill { color: #198754; }
    .active-icon.bi-x-circle-fill { color: #adb5bd; }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0">
            <i class="bi bi-box-seam me-2"></i>Master Product
        </span>
        <div class="d-flex align-items-center gap-3">
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
                <div class="input-group" style="max-width: 340px;">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari kode atau nama produk...">
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success" id="btnDownload" title="Download data">
                            <i class="bi bi-download me-1"></i>Download
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnDownloadSettings" title="Pengaturan download">
                            <i class="bi bi-gear"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnAdd">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Product
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Stock</th>
                            <th>Status</th>
                            <th class="text-center" style="width:70px;">Aktif</th>
                            <th style="width:130px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <tr id="tableLoading">
                            <td colspan="9" class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm me-2"></div>Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center mt-2 gap-2">
                <div class="text-muted small" id="paginationInfo"></div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <label for="pageSizeSelect" class="text-muted small mb-0">Tampilkan</label>
                        <select id="pageSizeSelect" class="form-select form-select-sm" style="width:auto;">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                    <nav aria-label="Product pagination">
                        <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="productForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Tambah Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="id_product" name="id_product">

                    <div class="mb-3">
                        <label class="form-label">Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="i_product" name="i_product" autocomplete="off" required>
                        <span id="kodeStatus"></span>
                        <div class="invalid-feedback">Kode produk wajib diisi.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="e_product" name="e_product" required>
                        <div class="invalid-feedback">Nama produk wajib diisi.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_category" name="id_category" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id_category']; ?>"><?= htmlspecialchars($cat['e_category']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Kategori wajib dipilih.</div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="v_price" name="v_price" min="0" step="0.01" required>
                            <div class="invalid-feedback">Harga wajib diisi dan tidak boleh negatif.</div>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Stock <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="n_stock" name="n_stock" min="0" step="1" required>
                            <div class="invalid-feedback">Stock wajib diisi dan tidak boleh negatif.</div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="saveSpinner"></span>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus product <strong id="deleteProductName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="deleteSpinner"></span>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-arrow-counterclockwise text-success me-2"></i>Konfirmasi Restore</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin mengaktifkan kembali product <strong id="restoreProductName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnConfirmRestore">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="restoreSpinner"></span>Restore
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Choose columns to download -->
<div class="modal fade" id="downloadColumnsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-download me-2"></i>Download Data Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">Pilih kolom yang ingin disertakan dalam file download:</p>
                <div class="d-flex justify-content-between mb-2">
                    <button type="button" class="btn btn-link btn-sm p-0" id="btnSelectAllCols">Pilih Semua</button>
                    <button type="button" class="btn btn-link btn-sm p-0" id="btnClearAllCols">Hapus Semua</button>
                </div>
                <div id="downloadColumnList" class="row row-cols-2 g-2 mb-3"></div>

                <div class="alert alert-secondary small py-2 mb-0" id="downloadScopeInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnConfirmDownload">
                    <i class="bi bi-download me-1"></i>Download
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Download settings -->
<div class="modal fade" id="downloadSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="downloadSettingsForm">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-gear me-2"></i>Pengaturan Download</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Format File</label>
                        <select class="form-select" id="settingFormat">
                            <option value="csv">CSV (.csv)</option>
                            <option value="excel">Excel (.xls)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="delimiterWrap">
                        <label class="form-label">Pemisah Kolom (CSV)</label>
                        <select class="form-select" id="settingDelimiter">
                            <option value=",">Koma ( , )</option>
                            <option value=";">Titik koma ( ; )</option>
                            <option value="\t">Tab</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data yang di-download</label>
                        <select class="form-select" id="settingScope">
                            <option value="filtered">Semua hasil pencarian saat ini</option>
                            <option value="page">Hanya baris di halaman saat ini</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama File (prefix)</label>
                        <input type="text" class="form-control" id="settingFilename" placeholder="products">
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="settingIncludeInactive">
                        <label class="form-check-label" for="settingIncludeInactive">
                            Sertakan produk yang dinonaktifkan (Deactivated)
                        </label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function () {

    var BASE_URL   = '<?= site_url('product'); ?>/';
    var productModal = new bootstrap.Modal(document.getElementById('productModal'));
    var deleteModal   = new bootstrap.Modal(document.getElementById('deleteModal'));
    var restoreModal  = new bootstrap.Modal(document.getElementById('restoreModal'));
    var deleteTargetId = null;
    var restoreTargetId = null;
    var searchTimer = null;
    var kodeCheckTimer = null;
    var kodeIsDuplicate = false;

    var PAGE_SIZE = 10;
    var currentPage = 1;
    var allRows = [];

    function showAlert(message, type) {
        var html = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        $('#alertPlaceholder').html(html);
    }

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function statusBadgeClass(status) {
        switch (status) {
            case 'Habis':   return 'badge-habis';
            case 'Menipis': return 'badge-menipis';
            case 'Cukup':   return 'badge-cukup';
            default:        return 'badge-banyak';
        }
    }

    function escapeHtml(str) {
        return $('<div>').text(str == null ? '' : str).html();
    }

    function loadProducts(search) {
        $('#tableLoading').show();
        $.ajax({
            url: BASE_URL + 'list_data',
            method: 'POST',
            data: { search: search || '' },
            dataType: 'json'
        }).done(function (res) {
            allRows = (res && res.data) || [];
            currentPage = 1;
            renderTable();
        }).fail(function () {
            allRows = [];
            $('#productTableBody').html('<tr><td colspan="9" class="text-center text-danger py-4">Gagal memuat data.</td></tr>');
            $('#paginationControls').empty();
            $('#paginationInfo').text('');
        }).always(function () {
            $('#tableLoading').hide();
        });
    }

    function renderTable() {
        var $body = $('#productTableBody');
        $body.empty();

        var totalRows = allRows.length;
        var isAll = (PAGE_SIZE === 'all');
        var effectiveSize = isAll ? Math.max(totalRows, 1) : PAGE_SIZE;
        var totalPages = isAll ? 1 : Math.max(1, Math.ceil(totalRows / effectiveSize));
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        if (totalRows === 0) {
            $body.append('<tr><td colspan="9" class="text-center text-muted py-4">Data produk tidak ditemukan.</td></tr>');
            $('#paginationControls').empty();
            $('#paginationInfo').text('');
            return;
        }

        var start = isAll ? 0 : (currentPage - 1) * effectiveSize;
        var pageRows = isAll ? allRows : allRows.slice(start, start + effectiveSize);

        pageRows.forEach(function (row, idx) {
            var isActive = (row.f_active === 't');
            var tr = $('<tr>');
            if (!isActive) tr.addClass('row-deactivated');

            tr.append('<td>' + (start + idx + 1) + '</td>');
            tr.append('<td>' + escapeHtml(row.i_product) + '</td>');
            tr.append('<td>' + escapeHtml(row.e_product) + '</td>');
            tr.append('<td>' + escapeHtml(row.e_category || '-') + '</td>');
            tr.append('<td class="text-end">' + formatRupiah(row.v_price) + '</td>');
            tr.append('<td class="text-end">' + row.n_stock + '</td>');
            tr.append('<td><span class="badge badge-status ' + statusBadgeClass(row.status) + '">' + row.status + '</span></td>');
            tr.append(
                '<td class="text-center">' +
                    (isActive
                        ? '<i class="bi bi-check-circle-fill active-icon" title="Aktif"></i>'
                        : '<i class="bi bi-x-circle-fill active-icon" title="Deactivated"></i>') +
                '</td>'
            );

            if (isActive) {
                tr.append(
                    '<td>' +
                        '<button type="button" class="btn btn-sm btn-outline-primary btn-edit me-1" data-id="' + row.id_product + '"><i class="bi bi-pencil-square"></i></button>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="' + row.id_product + '" data-name="' + escapeHtml(row.e_product) + '"><i class="bi bi-trash"></i></button>' +
                    '</td>'
                );
            } else {
                tr.append(
                    '<td>' +
                        '<button type="button" class="btn btn-sm btn-outline-success btn-restore" data-id="' + row.id_product + '" data-name="' + escapeHtml(row.e_product) + '" title="Restore Produk"><i class="bi bi-arrow-counterclockwise"></i></button>' +
                    '</td>'
                );
            }

            $body.append(tr);
        });

        var infoText = 'Menampilkan ' + (start + 1) + '-' + (start + pageRows.length) + ' dari ' + totalRows + ' produk';
        $('#paginationInfo').text(infoText);

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        var $pg = $('#paginationControls');
        $pg.empty();

        if (totalPages <= 1) return;

        function pageItem(label, page, disabled, active) {
            var li = $('<li class="page-item">');
            if (disabled) li.addClass('disabled');
            if (active) li.addClass('active');
            var a = $('<a class="page-link" href="#">').text(label).attr('data-page', page);
            li.append(a);
            return li;
        }

        $pg.append(pageItem('Prev', currentPage - 1, currentPage === 1, false));

        for (var p = 1; p <= totalPages; p++) {
            $pg.append(pageItem(String(p), p, false, p === currentPage));
        }

        $pg.append(pageItem('Next', currentPage + 1, currentPage === totalPages, false));
    }

    $('#paginationControls').on('click', 'a.page-link', function (e) {
        e.preventDefault();
        if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
        var page = parseInt($(this).attr('data-page'), 10);
        if (isNaN(page)) return;
        currentPage = page;
        renderTable();
    });

    $('#pageSizeSelect').on('change', function () {
        var val = $(this).val();
        PAGE_SIZE = (val === 'all') ? 'all' : parseInt(val, 10);
        currentPage = 1;
        renderTable();
    });

    $('#searchInput').on('keyup', function () {
        var val = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () { loadProducts(val); }, 350);
    });

    function resetForm() {
        $('#productForm')[0].reset();
        $('#productForm .is-invalid').removeClass('is-invalid');
        $('#id_product').val('');
        $('#i_product').prop('readonly', false);
        $('#kodeStatus').text('').removeClass('text-danger text-success');
        kodeIsDuplicate = false;
    }

    $('#btnAdd').on('click', function () {
        resetForm();
        $('#productModalLabel').text('Tambah Product');
        productModal.show();
    });

    $('#productTableBody').on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        $.ajax({
            url: BASE_URL + 'get/' + id,
            method: 'GET',
            dataType: 'json'
        }).done(function (res) {
            if (!res.status) {
                showAlert(res.message || 'Produk tidak ditemukan.', 'danger');
                return;
            }
            resetForm();
            var p = res.data;
            $('#productModalLabel').text('Edit Product');
            $('#id_product').val(p.id_product);
            $('#i_product').val(p.i_product).prop('readonly', true);
            $('#e_product').val(p.e_product);
            $('#id_category').val(p.id_category);
            $('#v_price').val(p.v_price);
            $('#n_stock').val(p.n_stock);
            productModal.show();
        }).fail(function () {
            showAlert('Gagal mengambil data produk.', 'danger');
        });
    });

    $('#i_product').on('keyup', function () {
        if ($(this).prop('readonly')) return;
        var kode = $(this).val().trim();
        clearTimeout(kodeCheckTimer);

        if (kode === '') {
            $('#kodeStatus').text('').removeClass('text-danger text-success');
            kodeIsDuplicate = false;
            return;
        }

        kodeCheckTimer = setTimeout(function () {
            $.ajax({
                url: BASE_URL + 'check_kode',
                method: 'POST',
                data: { i_product: kode, id_product: $('#id_product').val() },
                dataType: 'json'
            }).done(function (res) {
                if (res.exists) {
                    kodeIsDuplicate = true;
                    var msg = res.is_active ? 'Kode product sudah digunakan' : 'Kode product sudah digunakan (Deactivated)';
                    $('#kodeStatus').text(msg).addClass('text-danger').removeClass('text-success');
                } else {
                    kodeIsDuplicate = false;
                    $('#kodeStatus').text('Kode tersedia.').addClass('text-success').removeClass('text-danger');
                }
            });
        }, 400);
    });

    function validateForm() {
        var valid = true;
        $('#productForm .is-invalid').removeClass('is-invalid');

        if (!$('#i_product').prop('readonly') && $('#i_product').val().trim() === '') {
            $('#i_product').addClass('is-invalid'); valid = false;
        }
        if ($('#e_product').val().trim() === '') {
            $('#e_product').addClass('is-invalid'); valid = false;
        }
        if ($('#id_category').val() === '') {
            $('#id_category').addClass('is-invalid'); valid = false;
        }
        var price = parseFloat($('#v_price').val());
        if ($('#v_price').val() === '' || isNaN(price) || price < 0) {
            $('#v_price').addClass('is-invalid'); valid = false;
        }
        var stock = parseFloat($('#n_stock').val());
        if ($('#n_stock').val() === '' || isNaN(stock) || stock < 0) {
            $('#n_stock').addClass('is-invalid'); valid = false;
        }
        if (kodeIsDuplicate) {
            $('#i_product').addClass('is-invalid'); valid = false;
        }
        return valid;
    }

    $('#productForm').on('submit', function (e) {
        e.preventDefault();
        if (!validateForm()) return;

        $('#btnSave').prop('disabled', true);
        $('#saveSpinner').removeClass('d-none');

        $.ajax({
            url: BASE_URL + 'save',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json'
        }).done(function (res) {
            if (res.status) {
                productModal.hide();
                showAlert(res.message, 'success');
                loadProducts($('#searchInput').val());
            } else {
                showAlert(res.message || 'Gagal menyimpan data.', 'danger');
            }
        }).fail(function (xhr) {
            var msg = 'Gagal menyimpan data.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            showAlert(msg, 'danger');
        }).always(function () {
            $('#btnSave').prop('disabled', false);
            $('#saveSpinner').addClass('d-none');
        });
    });

    $('#productTableBody').on('click', '.btn-delete', function () {
        deleteTargetId = $(this).data('id');
        $('#deleteProductName').text($(this).data('name'));
        deleteModal.show();
    });

    $('#btnConfirmDelete').on('click', function () {
        if (!deleteTargetId) return;
        $('#btnConfirmDelete').prop('disabled', true);
        $('#deleteSpinner').removeClass('d-none');

        $.ajax({
            url: BASE_URL + 'delete/' + deleteTargetId,
            method: 'POST',
            dataType: 'json'
        }).done(function (res) {
            deleteModal.hide();
            showAlert(res.message, res.status ? 'success' : 'danger');
            if (res.status) loadProducts($('#searchInput').val());
        }).fail(function () {
            deleteModal.hide();
            showAlert('Gagal menghapus data.', 'danger');
        }).always(function () {
            $('#btnConfirmDelete').prop('disabled', false);
            $('#deleteSpinner').addClass('d-none');
            deleteTargetId = null;
        });
    });

    $('#productTableBody').on('click', '.btn-restore', function () {
        restoreTargetId = $(this).data('id');
        $('#restoreProductName').text($(this).data('name'));
        restoreModal.show();
    });

    $('#btnConfirmRestore').on('click', function () {
        if (!restoreTargetId) return;
        $('#btnConfirmRestore').prop('disabled', true);
        $('#restoreSpinner').removeClass('d-none');

        $.ajax({
            url: BASE_URL + 'restore/' + restoreTargetId,
            method: 'POST',
            dataType: 'json'
        }).done(function (res) {
            restoreModal.hide();
            showAlert(res.message, res.status ? 'success' : 'danger');
            if (res.status) loadProducts($('#searchInput').val());
        }).fail(function () {
            restoreModal.hide();
            showAlert('Gagal mengaktifkan kembali data.', 'danger');
        }).always(function () {
            $('#btnConfirmRestore').prop('disabled', false);
            $('#restoreSpinner').addClass('d-none');
            restoreTargetId = null;
        });
    });

    // ===================== DOWNLOAD FEATURE =====================

    var DOWNLOAD_SETTINGS_KEY = 'productDownloadSettings';
    var DOWNLOAD_COLUMNS_KEY  = 'productDownloadColumns';

    var DOWNLOAD_COLUMNS = [
        { key: 'i_product',  label: 'Kode Produk' },
        { key: 'e_product',  label: 'Nama Produk' },
        { key: 'e_category', label: 'Kategori' },
        { key: 'v_price',    label: 'Harga' },
        { key: 'n_stock',    label: 'Stock' },
        { key: 'status',     label: 'Status Stock' },
        { key: 'f_active',   label: 'Status Aktif' }
    ];

    var DEFAULT_DOWNLOAD_SETTINGS = {
        format: 'csv',
        delimiter: ',',
        scope: 'filtered',
        filenamePrefix: 'products',
        includeInactive: false
    };

    var downloadColumnsModal  = new bootstrap.Modal(document.getElementById('downloadColumnsModal'));
    var downloadSettingsModal = new bootstrap.Modal(document.getElementById('downloadSettingsModal'));

    function getDownloadSettings() {
        try {
            var raw = localStorage.getItem(DOWNLOAD_SETTINGS_KEY);
            if (!raw) return $.extend({}, DEFAULT_DOWNLOAD_SETTINGS);
            return $.extend({}, DEFAULT_DOWNLOAD_SETTINGS, JSON.parse(raw));
        } catch (e) {
            return $.extend({}, DEFAULT_DOWNLOAD_SETTINGS);
        }
    }

    function saveDownloadSettings(settings) {
        try {
            localStorage.setItem(DOWNLOAD_SETTINGS_KEY, JSON.stringify(settings));
        } catch (e) { /* localStorage unavailable, ignore */ }
    }

    function getSelectedColumns() {
        try {
            var raw = localStorage.getItem(DOWNLOAD_COLUMNS_KEY);
            if (!raw) return DOWNLOAD_COLUMNS.map(function (c) { return c.key; });
            var parsed = JSON.parse(raw);
            return Array.isArray(parsed) && parsed.length ? parsed : DOWNLOAD_COLUMNS.map(function (c) { return c.key; });
        } catch (e) {
            return DOWNLOAD_COLUMNS.map(function (c) { return c.key; });
        }
    }

    function saveSelectedColumns(cols) {
        try {
            localStorage.setItem(DOWNLOAD_COLUMNS_KEY, JSON.stringify(cols));
        } catch (e) { /* ignore */ }
    }

    function populateSettingsForm() {
        var s = getDownloadSettings();
        $('#settingFormat').val(s.format);
        $('#settingDelimiter').val(s.delimiter);
        $('#settingScope').val(s.scope);
        $('#settingFilename').val(s.filenamePrefix);
        $('#settingIncludeInactive').prop('checked', !!s.includeInactive);
        toggleDelimiterVisibility();
    }

    function toggleDelimiterVisibility() {
        $('#delimiterWrap').toggle($('#settingFormat').val() === 'csv');
    }

    $('#settingFormat').on('change', toggleDelimiterVisibility);

    $('#btnDownloadSettings').on('click', function () {
        populateSettingsForm();
        downloadSettingsModal.show();
    });

    $('#downloadSettingsForm').on('submit', function (e) {
        e.preventDefault();
        var settings = {
            format: $('#settingFormat').val(),
            delimiter: $('#settingDelimiter').val(),
            scope: $('#settingScope').val(),
            filenamePrefix: ($('#settingFilename').val() || 'products').trim(),
            includeInactive: $('#settingIncludeInactive').is(':checked')
        };
        saveDownloadSettings(settings);
        downloadSettingsModal.hide();
        showAlert('Pengaturan download berhasil disimpan.', 'success');
    });

    function populateColumnList() {
        var selected = getSelectedColumns();
        var $list = $('#downloadColumnList');
        $list.empty();

        DOWNLOAD_COLUMNS.forEach(function (col) {
            var checked = selected.indexOf(col.key) !== -1;
            var id = 'col_' + col.key;
            var col$ = $(
                '<div class="col">' +
                    '<div class="form-check">' +
                        '<input class="form-check-input dl-col-check" type="checkbox" value="' + col.key + '" id="' + id + '"' + (checked ? ' checked' : '') + '>' +
                        '<label class="form-check-label" for="' + id + '">' + col.label + '</label>' +
                    '</div>' +
                '</div>'
            );
            $list.append(col$);
        });

        var s = getDownloadSettings();
        var scopeText = s.scope === 'page'
            ? 'File akan berisi baris pada halaman tabel yang sedang tampil.'
            : 'File akan berisi seluruh data sesuai pencarian saat ini (semua halaman).';
        scopeText += s.includeInactive ? ' Produk non-aktif ikut disertakan.' : ' Produk non-aktif tidak disertakan.';
        scopeText += ' Ubah lewat tombol pengaturan (ikon gear).';
        $('#downloadScopeInfo').text(scopeText);
    }

    $('#btnDownload').on('click', function () {
        populateColumnList();
        downloadColumnsModal.show();
    });

    $('#btnSelectAllCols').on('click', function () {
        $('.dl-col-check').prop('checked', true);
    });

    $('#btnClearAllCols').on('click', function () {
        $('.dl-col-check').prop('checked', false);
    });

    function csvEscape(value, delimiter) {
        value = value === null || value === undefined ? '' : String(value);
        var needsQuote = value.indexOf('"') !== -1 || value.indexOf('\n') !== -1 || value.indexOf(delimiter) !== -1;
        value = value.replace(/"/g, '""');
        return needsQuote ? '"' + value + '"' : value;
    }

    function formatCellValue(row, key) {
        switch (key) {
            case 'v_price':  return Number(row.v_price || 0);
            case 'n_stock':  return row.n_stock;
            case 'f_active': return (row.f_active === 't') ? 'Aktif' : 'Deactivated';
            default:         return row[key] != null ? row[key] : '';
        }
    }

    function buildRowsForDownload(settings) {
        var rows = (settings.scope === 'page')
            ? getCurrentPageRows()
            : allRows.slice();

        if (!settings.includeInactive) {
            rows = rows.filter(function (r) { return r.f_active === 't'; });
        }
        return rows;
    }

    function getCurrentPageRows() {
        var isAll = (PAGE_SIZE === 'all');
        var effectiveSize = isAll ? Math.max(allRows.length, 1) : PAGE_SIZE;
        var start = isAll ? 0 : (currentPage - 1) * effectiveSize;
        return isAll ? allRows.slice() : allRows.slice(start, start + effectiveSize);
    }

    function triggerBlobDownload(blob, filename) {
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    }

    function downloadAsCsv(cols, rows, settings) {
        var delimiter = settings.delimiter === '\\t' ? '\t' : settings.delimiter;
        var lines = [];
        lines.push(cols.map(function (c) { return csvEscape(c.label, delimiter); }).join(delimiter));

        rows.forEach(function (row) {
            var line = cols.map(function (c) {
                return csvEscape(formatCellValue(row, c.key), delimiter);
            }).join(delimiter);
            lines.push(line);
        });

        var csvContent = '\uFEFF' + lines.join('\r\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        triggerBlobDownload(blob, settings.filenamePrefix + '.csv');
    }

    function downloadAsExcel(cols, rows, settings) {
        var html = '<table border="1"><thead><tr>';
        cols.forEach(function (c) { html += '<th>' + escapeHtml(c.label) + '</th>'; });
        html += '</tr></thead><tbody>';

        rows.forEach(function (row) {
            html += '<tr>';
            cols.forEach(function (c) {
                html += '<td>' + escapeHtml(formatCellValue(row, c.key)) + '</td>';
            });
            html += '</tr>';
        });
        html += '</tbody></table>';

        var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        triggerBlobDownload(blob, settings.filenamePrefix + '.xls');
    }

    $('#btnConfirmDownload').on('click', function () {
        var selectedKeys = $('.dl-col-check:checked').map(function () { return $(this).val(); }).get();

        if (selectedKeys.length === 0) {
            showAlert('Pilih minimal satu kolom untuk di-download.', 'warning');
            return;
        }

        saveSelectedColumns(selectedKeys);

        var cols = DOWNLOAD_COLUMNS.filter(function (c) { return selectedKeys.indexOf(c.key) !== -1; });
        var settings = getDownloadSettings();
        var rows = buildRowsForDownload(settings);

        if (rows.length === 0) {
            showAlert('Tidak ada data untuk di-download.', 'warning');
            return;
        }

        if (settings.format === 'excel') {
            downloadAsExcel(cols, rows, settings);
        } else {
            downloadAsCsv(cols, rows, settings);
        }

        downloadColumnsModal.hide();
        showAlert('Download dimulai (' + rows.length + ' baris).', 'success');
    });

    // ===================== END DOWNLOAD FEATURE =====================

    loadProducts('');
});
</script>
</body>
</html>