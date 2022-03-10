<?php

function fetch_data()
{
  //output
  ob_end_clean();
  include "../../fungsi/koneksi.php";

  if (isset($_GET['alat_berat']) && isset($_GET['client']) && isset($_GET['tgl_awal']) && isset($_GET['tgl_akhir'])) {
    $alat_berat = $_GET['alat_berat'];
    $client = $_GET['client'];
    $tgl_awal = $_GET['tgl_awal'];
    $tgl_akhir = $_GET['tgl_akhir'];
  }
  $output = '';

  $sql = "SELECT * FROM actual_jobordertruck aj
          LEFT JOIN job_ordertruck jt
            ON aj.id_jobordertruck = jt.id_jobordertruck
          INNER JOIN client cl
            ON jt.id_client = cl.id_client
          INNER JOIN alat_berat ab
            ON jt.id_alatberat = ab.id_alatberat
          WHERE DATE(created_on) BETWEEN '$tgl_awal' AND '$tgl_akhir'
          AND jt.id_alatberat = '$alat_berat' AND jt.id_client = '$client'
          ORDER BY created_on ASC";
  $result = mysqli_query($koneksi, $sql);
  // $rowCount = mysqli_num_rows();
  $i = 1;
  $totalVolume = 0;
  $totalTon = 0;
  $totalRevton = 0;

  while ($row = mysqli_fetch_assoc($result)) {

    $output .= "<tr>
                  <td style='text-align: center;'> $i </td>
                  <td style='text-align: center;'>" . $row['nm_company_at'] . "</td>
                  <td style='text-align: center;'>" . date("d/m/Y", strtotime($row['created_on'])) . "</td>
                  <td style='text-align: center;'>" . $row['production_at'] . "</td>
                  <td style='text-align: center;'>" . $row['nm_kegiatan_at'] . "</td>
                  <td style='text-align: center;'>" . $row['no_po_at'] . "</td>
                  <td style='text-align: center; width: 10px;'>" . $row['qty_cargo_at'] . "</td>
                  <td style='text-align: center;'>" . $row['satuan_cargo_at'] . "</td>
                  <td style='text-align: center;'>" . $row['p_cargo_at'] . "</td>
                  <td style='text-align: center;'>" . $row['l_cargo_at'] . "</td>
                  <td style='text-align: center;'>" . $row['t_cargo_at'] . "</td>
                  <td style='text-align: center;'>" . $row['ton_cargo_at'] . "</td>
                  <td style='text-align: center;'>" . $row['volume_cargo_at'] . "</td>
                  <td style='text-align: center;'>" . $row['revton_cargo_at'] . "</td>
                </tr>";
    $i++;

    $totalVolume += $row['ton_cargo_at'];
    $totalTon += $row['volume_cargo_at'];
    $totalRevton += $row['revton_cargo_at'];
  }
  $output .= "<tr>
                  <td style='text-align: center; colspan=\"11\";'><b>Total</b> </td>
                  <td style='text-align: center; '>" . $totalVolume . "</td>
                  <td style='text-align: center; '>" . $totalTon . "</td>
                  <td style='text-align: center; '>" . $totalRevton . "</td>
                  
                </tr>";
  return $output;
}

require_once 'library/tcpdf.php';
$obj_pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8, false');
$obj_pdf->SetCreator(PDF_CREATOR);
$obj_pdf->SetTitle("Tally Sheet Week");
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
$obj_pdf->SetFont('helvetica', '', 9);
$obj_pdf->AddPage();
// $style = array('width' => 0.7, 'dash' => '2, 2, 2, 2', 'phase' => 0);
// $obj_pdf->Line(5, 50, 290, 50, $style);

//content
$content = '';
include "../../fungsi/koneksi.php";
include "../../fungsi/fungsi.php";

if (isset($_GET['alat_berat']) && isset($_GET['client']) && isset($_GET['tgl_awal']) && isset($_GET['tgl_akhir'])) {
  $alat_berat = $_GET['alat_berat'];
  $client = $_GET['client'];
  $tgl_awal = $_GET['tgl_awal'];
  $tgl_akhir = $_GET['tgl_akhir'];
}

$sqlJO = "SELECT * FROM actual_jobordertruck aj
            LEFT JOIN job_ordertruck jt
              ON aj.id_jobordertruck = jt.id_jobordertruck
            INNER JOIN client cl
              ON jt.id_client = cl.id_client
            INNER JOIN alat_berat ab
              ON jt.id_alatberat = ab.id_alatberat
            WHERE DATE(created_on) BETWEEN '$tgl_awal' AND '$tgl_akhir'
            AND jt.id_alatberat = '$alat_berat' AND jt.id_client = '$client'
            ORDER BY created_on ASC";
$data = mysqli_query($koneksi, $sqlJO);
$rowHeader = mysqli_fetch_array($data);

// $content .= '<p align="center" style="width: 520px;"><font style="font-size: 15px; color: #4169E1;"><b>TALLY SHEET</b></font></p>';
$content .= '
  <br><br><br>
  <table border="0" cellspacing="0" cellpadding="3" >
    <tr>
      <th style="width: 320px;"><img src="library/img/enc.png" style="width: 180px; height: 40px;"></th>
      <th style="width: 160px;"><p><font style="font-size: 15px; color: ; text-align: center;"><b>TALLY SHEET</b></font></p></th>
      <td rowspan="3" style="text-align: center; width: 460px;"><img src="../../gambar/logo-client/' . $rowHeader['logo_client'] . '" style="width: 130px; height: 85px;"></td>
    </tr>
    
    <tr>  
      <td style="width: 60px;">EQUIPMENT</td>
      <td style="width: 280px;">: &nbsp;' . $rowHeader['nm_alatberat'] . '</td>
    </tr>
    <tr>  
      <td>PERIODE</td>
      <td>: &nbsp;' . date("d", strtotime($tgl_awal)) . ' s/d ' . tanggal($tgl_akhir) . '</td>
    </tr>
    </table> ';

$content .= '
    <table border="1" cellpadding="1" align="center" vertical-align="middle">
      <tr>
        <th text-align: center; style="vertical-align: bottom; width: 30px;" ><b>No</b></th>
        <th style="vertical-align: middle; width: 120px;" ><b>Company</b></th>
        <th style="vertical-align: middle; width: 60px;" ><b>Date</b></th>
    		<th style="vertical-align: middle; width: 80px;" ><b>Production<br>Drilling</b></th>
    		<th style="vertical-align: middle; width: 80px;" ><b>Loading<br>Unloading</b></th>
        <th style="vertical-align: middle; width: 70px;" ><b>PO#</b></th>
        <th colspan="2"; style="vertical-align: middle; width: 120px;"><b>Pkg (s)</b></th>
        <th colspan="3"; style="vertical-align: middle; width: 120px;" ><b>Dimention</b></th>
        <th style="vertical-align: middle; width: 40px;" ><b>Weight<br>(Ton)</b></th>
        <th style="vertical-align: middle; width: 40px;" ><b>Volume<br>(cbm)</b></th>
        <th style="vertical-align: middle; width: 40px;" ><b>Rev<br>(Ton))</b></th>
      </tr>';
$content .= fetch_data();
$content .= '</table> <br><br><br>';

$content .= '
              <table border="0" cellpadding="3">
                <tr>
                  <th style="text-align: center; width: 200px;">Prepared by<br><br><br><br><br>(............................................)</th>
                  <th style="text-align: center; width: 200px;">Acknowladge by<br><br><br><br><br>(............................................)</th>
                  <th style="text-align: center; width: 200px;">Acknowladge by<br><br><br><br><br>(............................................)</th>
                  <th style="text-align: center; width: 200px;">Approved by<br><br><br><br><br>(Hartono Purwanto)</th>
                </tr>
              </table>';

$obj_pdf->writeHTML($content);
$obj_pdf->Output('TallySheetWeek.pdf', 'I');
