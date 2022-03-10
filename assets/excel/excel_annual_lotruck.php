<?php
include "../../fungsi/koneksi.php";
include "../../fungsi/fungsi.php";

if (isset($_GET['tahun'])) {
    $tahun = $_GET['tahun'];
}

// 
$arrayTotal[][] = 0;

$queryClient = mysqli_query($koneksi, "SELECT * FROM client ORDER BY nm_client ASC ");
$n = mysqli_num_rows($queryClient);

$dataClient = mysqli_fetch_assoc($queryClient);
if (mysqli_num_rows($queryClient)) {
    while ($Client = mysqli_fetch_assoc($queryClient)) :

        $id_client = $Client['id_client'];
        $nm_client = $Client['nm_client2'];
        $queryTotal = mysqli_query($koneksi, "SELECT sum(total_cargo) AS jumlah FROM job_ordertruck WHERE id_client = '$id_client'  AND year(finish_kegiatan)= $tahun ");
        $dataTotal = mysqli_fetch_assoc($queryTotal);
        $totalCargo = $dataTotal['jumlah'];
        $array1 = [
            'client' => $nm_client,
            'total_cargo' => $totalCargo
        ];

        $array2[] = $array1;
    endwhile;
}

// fungsi header dengan mengirimkan raw data excel
header("Content-type: application/vnd-ms-excel");

// membuat nama file ekspor "export-to-excel.xls"
header("Content-Disposition: attachment; filename=Annual_Report_Lotruck.xls");


?>
<table border="1">
    <thead>
        <tr colspan="3">
            <h3>Annual Report Loading Offloading Truck</h3>
        </tr>
        <tr colspan="3">
            <h3>Semua Client <?= $tahun; ?></h3>
        </tr>
        <tr style="background-color: coral;">
            <th>No</th>
            <th>Client</th>
            <th>Total Cargo</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalCargo = 0;
        for ($i = 0; $i <= $n - 2; $i++) { ?>
            <tr>
                <th scope="row"><?= $i + 1; ?></th>
                <td><?= $array2[$i]['client']; ?></td>
                <td><?= $array2[$i]['total_cargo']; ?></td>
            </tr>
        <?php
            $totalCargo += $array2[$i]['total_cargo'];
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