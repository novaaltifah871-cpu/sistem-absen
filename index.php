
<?php
include 'koneksi.php';

// Pencarian siswa
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';

$query = "SELECT absen.*, siswa.siswa_nama, siswa.kelas_nama
          FROM absen
          INNER JOIN siswa
          ON absen.siswa_id = siswa.siswa_id";

if ($cari != '') {
    $query .= " WHERE siswa.siswa_nama LIKE ?";
}

$query .= " ORDER BY absen.tanggal DESC, absen.id_absen DESC";

if ($cari != '') {
    $stmt = mysqli_prepare($conn, $query);
    $keyword = "%$cari%";
    mysqli_stmt_bind_param($stmt, "s", $keyword);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $query);
}

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

// Menghitung rekap absensi
$rekap = mysqli_query($conn, "
    SELECT
        COUNT(*) AS total,
        SUM(status = 'Hadir') AS hadir,
        SUM(status = 'Izin') AS izin,
        SUM(status = 'Sakit') AS sakit,
        SUM(status = 'Alpa') AS alpa
    FROM absen
");

$data_rekap = mysqli_fetch_assoc($rekap);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi Siswa</title>
    <link rel="stylesheet" href="style_absen.css">
</head>
<body>

<div class="container">

    <h2>Data Absensi Siswa</h2>

    <a href="create.php" class="btn">
        + Tambah Absensi
    </a>

    <a href="index.php" class="btn btn-secondary">
        Kembali ke Data Nilai
    </a>

    <!-- Rekap Absensi -->
    <div class="rekap">

        <div class="card total">
            <h3><?= $data_rekap['total'] ?? 0; ?></h3>
            <p>Total Absensi</p>
        </div>

        <div class="card hadir">
            <h3><?= $data_rekap['hadir'] ?? 0; ?></h3>
            <p>Hadir</p>
        </div>

        <div class="card izin">
            <h3><?= $data_rekap['izin'] ?? 0; ?></h3>
            <p>Izin</p>
        </div>

        <div class="card sakit">
            <h3><?= $data_rekap['sakit'] ?? 0; ?></h3>
            <p>Sakit</p>
        </div>

        <div class="card alpa">
            <h3><?= $data_rekap['alpa'] ?? 0; ?></h3>
            <p>Alpa</p>
        </div>

    </div>

    <!-- Form Pencarian -->
    <form method="GET" class="search-form">
        <input type="text" name="cari"
               placeholder="Cari nama siswa..."
               value="<?= htmlspecialchars($cari); ?>">

        <button type="submit" class="btn">Cari</button>
        <a href="index_absen.php" class="btn btn-secondary">Reset</a>
    </form>

    <!-- Tabel Absensi -->
    <div class="table-responsive">

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Status</th>
                    
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if (mysqli_num_rows($result) > 0) {
                $no = 1;

                while ($row = mysqli_fetch_assoc($result)) {
            ?>

                <tr>
                    <td><?= $no++; ?></td>

                    <td><?= htmlspecialchars($row['siswa_nama']); ?></td>

                    <td><?= htmlspecialchars($row['kelas_nama']); ?></td>

                    <td>
                        <?= date('d-m-Y', strtotime($row['tanggal'])); ?>
                    </td>

                    <td>
                        <?= $row['jam_masuk']
                            ? htmlspecialchars($row['jam_masuk'])
                            : '-'; ?>
                    </td>

                    <td>
                        <?php
                        $class = strtolower($row['status']);
                        ?>
                        <span class="badge <?= $class; ?>">
                            <?= htmlspecialchars($row['status']); ?>
                        </span>
                    </td>

                    
                    <td class="aksi">

                        <a href="edit_absen.php?id=<?= (int)$row['id_absen']; ?>"
                           class="btn btn-warning">
                            Edit
                        </a>

                        <form action="delete_absen.php" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus absensi ini?')">

                            <input type="hidden" name="id"
                                   value="<?= (int)$row['id_absen']; ?>">

                            <button type="submit" class="btn btn-danger">
                                Hapus
                            </button>

                        </form>

                    </td>
                </tr>

            <?php
                }
            } else {
            ?>

                <tr>
                    <td colspan="8">Belum ada data absensi.</td>
                </tr>

            <?php } ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>
