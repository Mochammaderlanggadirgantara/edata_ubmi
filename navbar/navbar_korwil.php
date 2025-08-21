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
    // Ambil nama file yang sedang dibuka
    $current_page = basename($_SERVER['PHP_SELF']);

    // Fungsi untuk cek active
    function isActive($pages, $type = 'item')
    {
        global $current_page;

        // Kalau cuma satu halaman, jadikan array
        if (!is_array($pages)) {
            $pages = [$pages];
        }

        // Kalau type 'item' -> return 'active' saja
        // Kalau type 'parent' -> return 'active open' untuk menu parent
        if ($type == 'parent') {
            return in_array($current_page, $pages) ? 'active open' : '';
        } else {
            return in_array($current_page, $pages) ? 'active' : '';
        }
    }
    ?>
    

    <!-- Start Sidebar Area -->
    <div class="sidebar-area" id="sidebar-area">
        <div class="logo position-relative">
            <a
                href="../korwil_supervisor\dashboard_korwil.php"
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
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">MAIN</span>
                </li>
                <li class="menu-item open">
                    <a href="javascript:void(0);" class="menu-link menu-toggle ">
                        <span class="material-symbols-outlined menu-icon ">dashboard</span>
                        <span class="title">Dashboard</span>

                    </a>

                    <ul class="menu-sub">
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../kasir/inventaris.php" class="menu-link"> Data Inventaris </a>
                        </li>
                        <li class="menu-item <?= isActive('servis_inventaris.php') ?>">
                            <a href="../kasir/servis_inventaris.php" class="menu-link"> Data Servis Inventaris </a>
                        </li>
                        <li class="menu-item <?= isActive('database_nasabah.php') ?>">
                            <a href="../page/database_nasabah.php" class="menu-link"> Data Nasabah </a>
                        </li>
                        <li class="menu-item <?= isActive('slip_gaji.php') ?>">
                            <a href="../page/slip_gaji.php" class="menu-link"> Slip Gaji </a>
                        </li>
                        <li class="menu-item <?= isActive('tunai_babat.php') ?>">
                            <a href="../kasir/tunai_babat.php" class="menu-link"> Tunai Babat </a>
                        </li>
                        <li class="menu-item <?= isActive('data_antisipasi_masuk.php') ?>">
                            <a href="../korwil_supervisor/data_antisipasi_masuk.php" class="menu-link"> Data Antisipasi Masuk </a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/data_statistik.php" class="menu-link"> Data Statik </a>
                        </li>
                        <li class="menu-item <?= isActive('pelunasan.php') ?>">
                            <a href="../korwil_supervisor/pelunasan.php" class="menu-link"> Pelunasan 9% </a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/rekap_saldo.php" class="menu-link"> Rekap Saldo Awal </a>
                        </li>
                        <li class="menu-item <?= isActive('sisa_saldo.php') ?>">
                            <a href="../page/sisa_saldo.php" class="menu-link"> Sisa Saldo </a>
                        </li>
                        <li class="menu-item <?= isActive('tabungan_kita.php') ?>">
                            <a href="../page/tabungan_kita.php" class="menu-link"> Tabungan Kita </a>
                        </li>
                        <li class="menu-item <?= isActive('tabungan_kita.php') ?>">
                            <a href="../korwil_supervisor/laporan_km.php" class="menu-link">Laporan KM </a>
                        </li>
                        <li class="menu-item ">
                            <a href="javascript:void(0);" class="menu-link menu-toggle ">
                                <span class="material-symbols-outlined menu-icon">folder</span>
                                <span class="title">Data Target Ubmi</span>
                            </a>
                            <ul class="menu-sub custom-submenu"> <!-- tambahkan class custom-submenu -->
                                <li class="menu-item <?= isActive('data_target_ubmi.php') ?>">
                                    <a href="../korwil_supervisor/data_target_ubmi.php" class="menu-link">
                                        Target Mingguan
                                    </a>
                                </li>
                                <li class="menu-item <?= isActive('rekap_mingguan_target.php') ?>">
                                    <a href="../korwil_supervisor/rekap_mingguan_target.php" class="menu-link">
                                        Rekap Mingguan
                                    </a>
                                </li>
                                <li class="menu-item <?= isActive('global_rekap_target.php') ?>">
                                    <a href="../korwil_supervisor/global_rekap_target.php" class="menu-link">
                                        Global Rekap
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../kasir/turpas.php" class="menu-link"> Turpas Gaji</a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/kalkulasi_km.php" class="menu-link"> Kalkulasi KM </a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/kalkulasi_pimpinan.php" class="menu-link"> Kalkulasi Pimpinan </a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/kalkulasi_kas.php" class="menu-link"> Kalkulasi Kas </a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/drop_baru.php" class="menu-link"> Drop baru</a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/index_program.php" class="menu-link"> Index program</a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/evaluasi_program_mantri.php" class="menu-link">Evaluasi Program Mantri</a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/pemetaan_resort.php" class="menu-link">Pemetaan Resort</a>
                        </li>
                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="../korwil_supervisor/planning_resort.php" class="menu-link">Planning Resort</a>
                        </li>
                        
                    </ul>
                </li>
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">Add Data</span>
                </li>
                <li class="menu-item ">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">database</span>
                        <span class="title">Data Master</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?= isActive('kelola_cabang.php') ?>">
                            <a href="../korwil_supervisor/kelola_cabang.php" class="menu-link">
                                Kelola Data Cabang
                            </a>
                        </li>
                        <li class="menu-item <?= isActive('sandi_tabungan.php') ?>">
                            <a href="../korwil_supervisor/sandi_tabungan.php" class="menu-link">
                                Kelola Sandi Tabungan
                            </a>
                        </li>
                        <li class="menu-item <?= isActive('data_karyawan.php') ?>">
                            <a href="../korwil_supervisor/data_karyawan.php" class="menu-link">
                                Kelola Data karyawan
                            </a>
                        </li>

                        <li class="menu-item <?= isActive('inventaris.php') ?>">
                            <a href="trash-email.html" class="menu-link">
                                Trash
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