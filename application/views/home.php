<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f4f6f9; }
    .navbar-brand { font-weight: 600; }
    .home-card {
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        transition: transform .15s ease, box-shadow .15s ease;
        height: 100%;
    }
    .home-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.12);
    }
    .home-card .card-body { text-align: center; padding: 2.5rem 1.5rem; }
    .home-card .home-icon { font-size: 2.75rem; color: #0d6efd; margin-bottom: 1rem; }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0">
            <i class="bi bi-house-door me-2"></i>Home
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

    <h5 class="mb-4"><i class="bi bi-grid-3x3-gap me-2"></i>Mau lihat apa hari ini?</h5>

    <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-4">
            <a href="<?= site_url('product'); ?>" class="text-decoration-none">
                <div class="card home-card">
                    <div class="card-body">
                        <i class="bi bi-box-seam home-icon"></i>
                        <h5 class="text-dark">Product List</h5>
                        <p class="text-muted mb-0">Kelola data master product.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="<?= site_url('cart'); ?>" class="text-decoration-none">
                <div class="card home-card">
                    <div class="card-body">
                        <i class="bi bi-cart-check home-icon"></i>
                        <h5 class="text-dark">Data Pembelian Konsumen</h5>
                        <p class="text-muted mb-0">Lihat dashboard daftar beli konsumen per user.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

</div>

</body>
</html>
