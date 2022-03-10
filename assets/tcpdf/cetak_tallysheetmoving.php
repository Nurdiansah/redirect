<?php

function fetch_data()
{
  //output
  ob_end_clean();
  include "../../fungsi/koneksi.php";

  if (isset($_GET['id'])) {
    $idJoborder = $_GET['id'];
  }
  $output = '';

  $sql = "SELECT deskripsi_cargo_as, keterangan_as, no_po_as, nm_barang_as, p_cargo, l_cargo, t_cargo, lokasi_awal, lokasi_akhir, satuan_cargo, sum(st.qty_cargo_as) as qty_cargo,sum(st.volume_cargo_as) as volume_cargo_as, sum(st.ton_cargo_as) as ton_cargo_as, sum(st.revton_cargo_as) as revton_cargo_as
          FROM detail_moving ds
          JOIN actual_moving st
          ON ds.id_cargo = st.id_cargo
          WHERE ds.id_moving = '$idJoborder'
          GROUP BY st.id_cargo";
  $result = mysqli_query($koneksi, $sql);
  // $rowCount = mysqli_num_rows();
  $i = 1;
  $totalVolume = 0;
  $totalTon = 0;
  $totalRevton = 0;

  while ($row = mysqli_fetch_array($result)) {

    $output .= "<tr>
                  <td style='text-align: center;'> $i </td>
                  <td style='text-align: center;'>" . $row['deskripsi_cargo_as'] . "</td>
                  <td style='text-align: center;'>" . $row['lokasi_awal'] . "-" . $row['lokasi_akhir'] . "</td>
                  <td style='text-align: center;'>" . $row['no_po_as'] . "</td>
                  <td style='text-align: center;'>" . $row['nm_barang_as'] . "</td>
                  <td style='text-align: center;'>" . $row['satuan_cargo'] . "</td>
                  <td style='text-align: center;'>" . $row['qty_cargo'] . "</td>
                  <td style='text-align: center;'>" . $row['p_cargo'] . "</td>
                  <td style='text-align: center;'>" . $row['l_cargo'] . "</td>
                  <td style='text-align: center;'>" . $row['t_cargo'] . "</td>
                  <td style='text-align: center;'>" . $row['volume_cargo_as'] . "</td>
                  <td style='text-align: center;'>" . $row['ton_cargo_as'] . "</td>
                  <td style='text-align: center;'>" . $row['revton_cargo_as'] . "</td>
                </tr>";
    $i++;

    $totalVolume += $row['volume_cargo_as'];
    $totalTon += $row['ton_cargo_as'];
    $totalRevton += $row['revton_cargo_as'];
  }
  $output .= "<tr >
                  <td style='text-align: center; colspan=\"10\";'><h3><b>Total</b> </h3></td>
                  <td style='text-align: center; '><h3>" . $totalVolume . "</h3></td>
                  <td style='text-align: center; '><h3>" . $totalTon . "</h3></td>
                  <td style='text-align: center; '><h3>" . $totalRevton . "</h3></td>
                  
                </tr>";
  return $output;
}

require_once 'library/tcpdf.php';
$obj_pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8, false');
$obj_pdf->SetCreator(PDF_CREATOR);
$obj_pdf->SetTitle("Tally Sheet Internal Moving");
$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
$obj_pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$obj_pdf->setHeaderFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$obj_pdf->SetDefaultMonospacedFont('helvetica');
$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$obj_pdf->SetMargins(5, 5, 5);
$obj_pdf->setPrintHeader(false);
$obj_pdf->setPrintFooter(true);
$obj_pdf->setAutoPageBreak(true, PDF_MARGIN_BOTTOM);
$obj_pdf->SetFont('helvetica', '', 11);
$obj_pdf->AddPage();
// $style = array('width' => 0.7, 'dash' => '2, 2, 2, 2', 'phase' => 0);
// $obj_pdf->Line(5, 50, 290, 50, $style);

//content
$content = '';
include "../../fungsi/koneksi.php";
include "../../fungsi/fungsi.php";

if (isset($_GET['id'])) {
  $idJoborder = $_GET['id'];
}

$sqlJO = "SELECT * FROM moving m
                    JOIN client c
                    ON m.id_client = c.id_client
                    WHERE id_moving = '$idJoborder'";
$data = mysqli_query($koneksi, $sqlJO);
$rowData = mysqli_fetch_array($data);

$idChekcer = $rowData['id_checker'];

$qeuryChecker = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$idChekcer'");

$dataCheker = mysqli_fetch_array($qeuryChecker);

// $content .= '<p align="center" style="width: 520px;"><font style="font-size: 15px; color: #4169E1;"><b>TALLY SHEET</b></font></p>';
$content .= '<table border="0" cellspacing="0" cellpadding="3" style="color: #4169E1">
    <tr>
      <th style="width: 350px;"><img src="library/img/enc.png" style="width: 180px; height: 40px;"></th>
      <th style="width: 460px;"><p><font style="font-size: 15px; color: #4169E1;"><b>TALLY SHEET</b></font></p></th>
    </tr>
    
    <tr>  
      <td style="width: 70px;">Date</td>
      <td style="width: 280px;">= &nbsp;' . formatTanggal($rowData['finish_kegiatan']) . '</td>
      <td rowspan="3" style="text-align: right; width: 460px;"><img src="../../gambar/logo-client/' . $rowData['logo_client'] . '" style="width: 130px; height: 85px;"></td>
    </tr>
    <tr>  
      <td>Time</td>
      <td>= &nbsp;' . $rowData['durasi'] . '</td>
    </tr>
    <tr>  
      <td>Equipment</td>
      <td>= &nbsp;' . $rowData['nm_trailer'] . '</td>
    </tr>
    </table> ';

$content .= '
    <table border="1" color="#4169E1" cellpadding="1" align="center" vertical-align="middle">
      <tr>
        <th rowspan="2"; text-align: center; style="vertical-align: bottom; width: 30px;" >NO</th>
        <th rowspan="2"; style="vertical-align: middle; width: 90px;" >Descriptin Of Work</th>
        <th rowspan="2"; style="vertical-align: middle; width: 120px;" >Location<br>(from-to)</th>
    		<th rowspan="2"; style="vertical-align: middle; width: 50px;" >MTD /<br>PO#</th>
        <th rowspan="2"; style="vertical-align: middle; width: 120px;" >Nama Barang</th>
        <th rowspan="2"; style="vertical-align: middle; width: 60px;" >Package</th>
        <th rowspan="2"; style="vertical-align: middle; width: 30px;" >QTY</th>
        <th colspan="3"; style="vertical-align: middle; width: 120px;">Dimention</th>
        <th rowspan="2"; style="vertical-align: middle; width: 50px;" >Volume<br>M<sup>3</sup></th>
        <th rowspan="2"; style="vertical-align: middle; width: 50px;" >Ton</th>
        <th rowspan="2"; style="vertical-align: middle; width: 50px;" >Ton/M<sup>3</sup></th>
        <th rowspan="2"; style="vertical-align: middle; width: 35px;" >Sign</th>
      </tr>
      <tr>
        <th>P</th>
        <th>L</th>
        <th>T</th>
      </tr>';
$content .= fetch_data();
$content .= '</table> <br><br><br>';

$content .= '
              <table border="0" cellpadding="3" style="color: #4169E1">
                <tr>
                  <th style="text-align: center; width: 200px;">Operator<br><br><br><br><br>(............................................)</th>
                  <th style="text-align: center; width: 400px;">Checker<br><br><br><br><br>('.$dataCheker['nama'].')</th>
                  <th style="text-align: center; width: 200px;">Supervisor<br><br><br><br><br>(............................................)</th>
                </tr>
              </table>';

$obj_pdf->writeHTML($content);
$obj_pdf->Output($idJoborder . '-TallySheetInternalMoving-.pdf', 'I');
