<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/edata_ubmi/config/koneksi.php";

$id_user = $_SESSION['id_user'] ?? null;
if (!$id_user) {
    echo "User belum login.";
    exit;
}

// ✅ Ganti user.* menjadi t.*, dan nama tabelnya menjadi tuser
$query = mysqli_query($conn, "SELECT t.*, cabang.nama_cabang 
                              FROM tuser t
                              JOIN cabang ON t.id_cabang = cabang.id_cabang 
                              WHERE t.id_user = '$id_user'");

$data_user = mysqli_fetch_assoc($query);

$nama_user = $data_user['nama_user'] ?? '';
$jabatan = $data_user['jabatan'] ?? '';
$nama_cabang = $data_user['nama_cabang'] ?? '';
?>

<!-- Start Sidebar Area -->
<div class="sidebar-area" id="sidebar-area">
    <div class="logo position-relative">
        <a
            href="../kasir/dashboard_kasir.php"
            class="d-block text-decoration-none position-relative">
            <img src="/edata_ubmi/assets/images/logo-icon.png" alt="logo-icon" />
            <span class="logo-text fw-bold text-dark">TASKSIGHT</span>
        </a>
        <button
            class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y"
            id="sidebar-burger-menu">
            <i data-feather="x"></i>
        </button>
    </div>

    <aside
        id="layout-menu"
        class="layout-menu menu-vertical menu active"
        data-simplebar>
        <ul class="menu-inner">
            <!-- Main Section -->
            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">MAIN</span>
            </li>
            <li class="menu-item open">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">dashboard</span>
                    <span class="title">Dashboard</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="../mantri/target_baru.php" class="menu-link">
                            <span class="material-symbols-outlined menu-icon">inventory_2</span>
                            Target Baru
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="../mantri/target_berjalan.php" class="menu-link">
                            <span class="material-symbols-outlined menu-icon">payments</span>
                            Target Berjalan
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="../mantri/data_statistik.php" class="menu-link">
                            <span class="material-symbols-outlined menu-icon">build</span>
                            Data Statistik
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="../page/database_nasabah.php" class="menu-link">
                            <span class="material-symbols-outlined menu-icon">database</span>
                            Input Database
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="../mantri/catatan.php" class="menu-link">
                            <span class="material-symbols-outlined menu-icon">book</span>
                            Catatan
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="../page/sisa_saldo.php" class="menu-link">
                            <span class="material-symbols-outlined menu-icon">account_balance_wallet</span>
                            Sisa Saldo
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="../mantri/rencana.php" class="menu-link">
                            <span class="material-symbols-outlined menu-icon">Note_Stack</span>
                            Rencana
                        </a>
                    </li>
                    <li class="menu-item mb-0">
                        <a href="../mantri/kalkulasi_program.php" class="menu-link">
                            <span class="material-symbols-outlined menu-icon">calculate</span>
                            Kalkulasi Program
                        </a>
                    </li>
                </ul>
            </li>


        </ul>
    </aside>

</div>
<!-- End Sidebar Area -->

<!-- Start Main Content Area -->
<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <!-- Start Header Area -->
        <header
            class="header-area bg-white mb-4 rounded-bottom-15"
            id="header-area">
            <div class="row align-items-center">
                <div class="col-lg-4 col-sm-6">
                    <div class="left-header-content">
                        <ul
                            class="d-flex align-items-center ps-0 mb-0 list-unstyled justify-content-center justify-content-sm-start">
                            <li>
                                <button
                                    class="header-burger-menu bg-transparent p-0 border-0"
                                    id="header-burger-menu">
                                    <span class="material-symbols-outlined">menu</span>
                                </button>
                            </li>
                            <li>
                                <form class="src-form position-relative">
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Search here....." />
                                    <button
                                        type="submit"
                                        class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0">
                                        <span class="material-symbols-outlined">search</span>
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>
                </div>

                <div class="col-lg-8 col-sm-6">
                    <div class="right-header-content mt-2 mt-sm-0">
                        <ul
                            class="d-flex align-items-center justify-content-center justify-content-sm-end ps-0 mb-0 list-unstyled">
                            <li class="header-right-item">
                                <div class="dropdown admin-profile">
                                    <div
                                        class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <div class="flex-shrink-0">
                                            <img
                                                class="rounded-circle wh-40 administrator"
                                                src="/edata_ubmi/assets/images/administrator.jpg"
                                                alt="admin" />
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div
                                                class="d-flex align-items-center justify-content-between">
                                                <div class="d-none d-xxl-block">
                                                    <div class="d-flex align-content-center">
                                                        <h3><?= htmlspecialchars($_SESSION['nama_user']) ?></h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="dropdown-menu border-0 bg-white dropdown-menu-end">
                                        <div class="d-flex align-items-center info">
                                            <div class="flex-shrink-0">
                                                <img
                                                    class="rounded-circle wh-30 administrator"
                                                    src="/edata_ubmi/assets/images/administrator.jpg"
                                                    alt="admin" />
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <?php echo "$nama_user ($jabatan) - $nama_cabang"; ?>
                                            </div>
                                        </div>
                                        <ul class="admin-link ps-0 mb-0 list-unstyled">

                                            <li>
                                                <a
                                                    class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                                    href="/edata_ubmi/logout.php">
                                                    <i class="material-symbols-outlined">logout</i>

                                                    <span class="ms-2">Logout</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>