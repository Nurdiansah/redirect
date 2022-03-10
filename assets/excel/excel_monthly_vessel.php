<?php
include "../../fungsi/koneksi.php";
include "../../fungsi/fungsi.php";

if (isset($_GET['id_client']) && isset($_GET['tahun'])) {
    $id_client = $_GET['id_client'];
    $tahun = $_GET['tahun'];
}

if ($id_client != 'all') {
    $queryClient = mysqli_query($koneksi, "SELECT * FROM client WHERE id_client = '$id_client'");
    $dataClient = mysqli_fetch_assoc($queryClient);
    $namaClient = $dataClient['nm_client2'];
}

$bulan    = date('n');

$arrayTotal[] = 0;


if ($id_client == 'all') {
    for ($i = 1; $i <= 12; $i++) {
        $queryTotal = mysqli_query($koneksi, "SELECT sum(total_cargo) AS jumlah FROM job_order WHERE month(finish_kegiatan)= $i AND year(finish_kegiatan)= $tahun ");
        $dataTotal = mysqli_fetch_assoc($queryTotal);
        $total = $dataTotal['jumlah'];

        $arrayTotal[] += $total;
    }
} else {
    for ($i = 1; $i <= 12; $i++) {
        $queryTotal = mysqli_query($koneksi, "SELECT sum(total_cargo) AS jumlah FROM job_order WHERE id_client = '$id_client' AND month(finish_kegiatan)= $i AND year(finish_kegiatan)= $tahun ");
        $dataTotal = mysqli_fetch_assoc($queryTotal);
        $total = $dataTotal['jumlah'];

        $arrayTotal[] += $total;
    }
}

// fungsi header dengan mengirimkan raw data excel
header("Content-type: application/vnd-ms-excel");

// membuat nama file ekspor "export-to-excel.xls"
header("Content-Disposition: attachment; filename=monthly_report_vessel.xls");


?>
<table border="1">
    <thead>
        <tr colspan="3">
            <h3>Monthly Report Stavedoring Vessel</h3>
        </tr>
        <?php if ($id_client == 'all') { ?>
            <tr colspan="3">
                <h3>Semua Client <?= $tahun; ?></h3>
            </tr>
        <?php } else { ?>
            <tr colspan="3">
                <h3><?= $namaClient . ' ' . $tahun ?></h3>
            </tr>
        <?php } ?>
        <tr style="background-color: coral;">
            <th>No</th>
            <th>Bulan</th>
            <th>Total Cargo</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalCargo = 0;
        for ($i = 1; $i <= 12; $i++) { ?>
            <tr>
                <th scope="row"><?= $i; ?></th>
                <td><?= bulanArray($i); ?></td>
                <td><?= $arrayTotal[$i]; ?></td>
            </tr>
        <?php
            $totalCargo += $arrayTotal[$i];
        } ?>
        <tr>
            <td colspan="2" class="text-center">
                <h3>Total</h3>
            </td>
            <td>
                <h3><?= $totalCargo; ?></h3>
            </td>
        </tr>
    </tbody>
</table>