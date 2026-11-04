<?php
require_once 'koneksi.php';
session_start();

$pesan = "";

// PROSES INPUT TRANSAKSI GAJI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_gaji'])) {
    $id_karyawan   = $_POST['id_karyawan'];
    $bulan         = $_POST['bulan'];
    $tahun         = $_POST['tahun'];
    $tunjangan     = !empty($_POST['tunjangan']) ? intval($_POST['tunjangan']) : 0;
    $potongan      = !empty($_POST['potongan']) ? intval($_POST['potongan']) : 0;
    $tanggal_bayar = date('Y-m-d');

    try {
        // Ambil gaji pokok karyawan
        $stmtKar = $pdo->prepare("SELECT nama_karyawan, gaji_pokok FROM karyawan WHERE id_karyawan = :id");
        $stmtKar->execute([':id' => $id_karyawan]);
        $kar = $stmtKar->fetch(PDO::FETCH_ASSOC);

        if ($kar) {
            $gaji_pokok = $kar['gaji_pokok'];
            $total_gaji = ($gaji_pokok + $tunjangan) - $potongan;

            // Cek apakah gaji bulan & tahun tersebut sudah pernah dibayarkan untuk karyawan ini
            $stmtCek = $pdo->prepare("SELECT id_gaji FROM penggajian WHERE id_karyawan = :id AND bulan = :bulan AND tahun = :tahun");
            $stmtCek->execute([':id' => $id_karyawan, ':bulan' => $bulan, ':tahun' => $tahun]);

            if ($stmtCek->fetch()) {
                $pesan = "<div id='notif-pesan' style='background:#fef9e7; border-left-color:f1c40f;'><strong>Peringatan!</strong> Gaji untuk <b>" . htmlspecialchars($kar['nama_karyawan']) . "</b> periode <b>$bulan $tahun</b> sudah pernah diproses.</div>";
            } else {
                // Simpan transaksi penggajian
                $stmtInsert = $pdo->prepare("INSERT INTO penggajian (id_karyawan, bulan, tahun, tunjangan, potongan, total_gaji, tanggal_bayar) 
                                             VALUES (:id_karyawan, :bulan, :tahun, :tunjangan, :potongan, :total_gaji, :tanggal_bayar)");
                $stmtInsert->execute([
                    ':id_karyawan'   => $id_karyawan,
                    ':bulan'         => $bulan,
                    ':tahun'         => $tahun,
                    ':tunjangan'     => $tunjangan,
                    ':potongan'      => $potongan,
                    ':total_gaji'    => $total_gaji,
                    ':tanggal_bayar' => $tanggal_bayar
                ]);

                $pesan = "<div id='notif-pesan'><strong>Berhasil!</strong> Slip gaji untuk <b>" . htmlspecialchars($kar['nama_karyawan']) . "</b> berhasil disimpan. Total: Rp " . number_format($total_gaji, 0, ',', '.') . "</div>";
            }
        }
    } catch (PDOException $e) {
        $pesan = "<div id='notif-pesan' style='background:#fdedec; border-left-color:#e74c3c;'><strong>Gagal:</strong> " . $e->getMessage() . "</div>";
    }
}

// Mengammbil data karyawan untuk pilihan form
$karyawanList = $pdo->query("SELECT * FROM karyawan ORDER BY nama_karyawan ASC")->fetchAll(PDO::FETCH_ASSOC);

// Mengambil juga riwayat penggajian terbaru
$riwayatGaji = $pdo->query("SELECT penggajian.*, karyawan.nama_karyawan, karyawan.nik, karyawan.jabatan, karyawan.gaji_pokok 
                            FROM penggajian 
                            JOIN karyawan ON penggajian.id_karyawan = karyawan.id_karyawan 
                            ORDER BY id_gaji DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penggajian Karyawan</title>
    <!-- Ini adalah bagian CSS yang dipisah melalui folder terpisah juga dengan kode PHP ini -->
    <link rel="stylesheet" id="theme-css" href="../CSS/style.css">
</head>
<body>

    <!-- Ini bagian header -->
    <div class="top-bar">
        <h2>Sistem Informasi Penggajian Karyawan</h2>
        <button type="button" class="theme-toggle-btn" onclick="toggleCSS()">Tampilan yang berbeda </button>
    </div>

    <?= $pesan; ?>

    <div class="dashboard-grid">
        <!-- Form Input Penggajian -->
        <fieldset>
            <legend>Form Transaksi Penggajian</legend>
            <form method="POST" action="" onsubmit="handleFormSubmit(this)">
                <div>
                    <label for="id_karyawan">Pilih Karyawan</label>
                    <select name="id_karyawan" id="id_karyawan" required onchange="setGajiPokok(this)">
                        <option value="">-- Pilih Karyawan --</option>
                        <?php foreach ($karyawanList as $k): ?>
                            <option value="<?= $k['id_karyawan']; ?>" data-gaji="<?= $k['gaji_pokok']; ?>">
                                <?= htmlspecialchars($k['nama_karyawan'] . ' (' . $k['jabatan'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <br>

                <div>
                    <label for="gaji_pokok_view">Gaji Pokok (Rp)</label>
                    <input type="text" id="gaji_pokok_view" readonly placeholder="Otomatis terisi..." style="background-color: #eee;">
                </div>

                <br>

                <div>
                    <label for="bulan">Bulan Penggajian</label>
                    <select name="bulan" id="bulan" required>
                        <option value="">-- Pilih Bulan --</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                </div>

                <br>

                <div>
                    <label for="tahun">Tahun</label>
                    <input type="text" name="tahun" id="tahun" value="<?= date('Y'); ?>" required>
                </div>

                <br>

                <div>
                    <label for="tunjangan">Tunjangan (Rp)</label>
                    <input type="number" name="tunjangan" id="tunjangan" value="0" required>
                </div>

                <br>

                <div>
                    <label for="potongan">Potongan / Kasbon (Rp)</label>
                    <input type="number" name="potongan" id="potongan" value="0" required>
                </div>

                <br>

                <button type="submit" name="proses_gaji" id="btn-submit">Proses & Simpan Gaji</button>
            </form>
        </fieldset>

        <!-- History Penggajian-->
        <div class="table-container">
            <h3>Riwayat Penggajian Terbaru</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Periode</th>
                        <th>Gaji Bersih (Total)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayatGaji)): ?>
                        <tr>
                            <td colspan="5" align="center">Belum ada transaksi penggajian.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($riwayatGaji as $r): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $r['tanggal_bayar']; ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($r['nama_karyawan']); ?></strong><br>
                                    <small style="color: #777;"><?= $r['jabatan']; ?></small>
                                </td>
                                <td><?= $r['bulan'] . ' ' . $r['tahun']; ?></td>
                                <td><strong>Rp <?= number_format($r['total_gaji'], 0, ',', '.'); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript Pendukung -->
    <script>
    // Auto-fill Gaji Pokok berdasarkan dropdown karyawan yang dipilih
    function setGajiPokok(select) {
        const selectedOption = select.options[select.selectedIndex];
        const gaji = selectedOption.getAttribute('data-gaji');
        document.getElementById('gaji_pokok_view').value = gaji ? 'Rp ' + Number(gaji).toLocaleString('id-ID') : '';
    }

    // Toggle CSS On / Off (Menyimpan status ke localStorage)
    function toggleCSS() {
        const themeCss = document.getElementById('theme-css');
        if (themeCss.disabled) {
            themeCss.disabled = false;
            localStorage.setItem('css_status_gaji', 'enabled');
        } else {
            themeCss.disabled = true;
            localStorage.setItem('css_status_gaji', 'disabled');
        }
    }

    // Cek status CSS saat halaman dimuat
    (function checkCssState() {
        const status = localStorage.getItem('css_status_gaji');
        if (status === 'disabled') {
            document.getElementById('theme-css').disabled = true;
        }
    })();

    // Hilangin notifikasi otomatis setelah 3 detik
    window.addEventListener('DOMContentLoaded', () => {
        const notif = document.getElementById('notif-pesan');
        if (notif) {
            setTimeout(() => {
                notif.style.transition = 'opacity 0.5s ease';
                notif.style.opacity = '0';
                setTimeout(() => notif.style.display = 'none', 500);
            }, 3000);
        }
    });

    // Cegah double submit form
    function handleFormSubmit(form) {
        setTimeout(() => {
            const btn = document.getElementById('btn-submit');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Memproses...';
            }
        }, 10);
    }
    </script>
</body>
</html>