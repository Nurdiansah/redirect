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

  $sql = "SELECT * FROM actual_stevedoring ac
                  JOIN jenis_barang j
                  ON ac.id_jenis = j.id_jenis
                  WHERE ac.id_joborder = '$idJoborder'
                  AND ac.keterangan != 'Not Available'";
  $result = mysqli_query($koneksi, $sql);
  $i = 1;

  $sqlTotal = "SELECT total_cargo FROM job_order
            WHERE id_joborder = '$idJoborder'";
  $total = mysqli_query($koneksi, $sqlTotal);
  $rowTotal = mysqli_fetch_array($total);

  while ($row = mysqli_fetch_array($result)) {
    $output .= "<!-- <tbody> -->
                <tr >
                  <td style='text-align: center;'> $i </td>
                  <td style='text-align: center;'>" . tanggalWaktu($row['time_as']) . "</td>
                  <td style='text-align: center;'>" . $row['doc_no_as'] . "</td>
                  <td style='text-align: center;'>" . $row['qty_as'] . "</td>
                  <td style='text-align: center;'>" . $row['rincian_cargo'] . "</td>
                  <td style='text-align: center;'>" . $row['remarks_as'] . "</td>
                  <td style='text-align: center;'>" . $row['keterangan'] . "</td>                  
                  <td style='text-align: center;'>" . $row['m3_as'] . "</td>
                  <td style='text-align: center;'>" . $row['ton_as'] . "</td>
                  <td style='text-align: center;'>" . $row['revton_as'] . "</td>
                </tr>
                <!-- <tbody> -->";
    $i++;
  }
  $output .= "<tr>
                <td style='text-align: center; colspan=\"9\";'><b>Total Cargo</b> </td>
                <td style='text-align: center; '><b>" . $rowTotal['total_cargo'] . "</b></td>
              </tr>";
  return $output;
}

require_once 'library/tcpdf.php';
$obj_pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8, false');
$obj_pdf->SetCreator(PDF_CREATOR);
$obj_pdf->SetTitle("Time Sheet Vessel");
$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
$obj_pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$obj_pdf->setHeaderFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$obj_pdf->SetDefaultMonospacedFont('helvetica');
$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$obj_pdf->SetMargins(15, 15, 15);
$obj_pdf->setPrintHeader(false);
$obj_pdf->setPrintFooter(true);
$obj_pdf->setAutoPageBreak(true, PDF_MARGIN_BOTTOM);
$obj_pdf->SetFont('helvetica', '', 11);
$obj_pdf->AddPage();
$style = array('width' => 0.7, 'dash' => '2, 2, 2, 2', 'phase' => 0);
$obj_pdf->Line(5, 30, 290, 30, $style);

//content
$content = '';
include "../../fungsi/koneksi.php";
include "../../fungsi/fungsi.php";
if (isset($_GET['id'])) {
  $idJoborder = $_GET['id'];
}

$sqlJO = "SELECT * FROM job_order a
  INNER JOIN client b
    ON a.id_client = b.id_client
  WHERE id_joborder = '$idJoborder'";
$data = mysqli_query($koneksi, $sqlJO);
$rowData = mysqli_fetch_array($data);

$content .= '
  <p align="left" style="width: 520px;"><font style="font-size: 18px"><b>EKANURI</b></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <font style="font-size: 15px"><b>TIME SHEET</b></font>
  <br><font style="font-size: 8px">Jl. Ketel Uap I No. 1 Jakarta Utara DKI Jakarta<br>Telp :  (021) 439-02157 | Fax : (021) 385-0830</font></p>
  
  <font size="8">
  <table border="0" cellpadding="1" >
    <tr>  
      <th style="width: 60px;">Area</th>
      <th style="width: 500px;"> : ' . $rowData['sandar_kapal'] . '</th>
      <th style="width: 63px;">Tanggal</th>
      <th> : ' . tanggal($rowData['finish_kegiatan']) . '</th>
    </tr>
    <tr>  
      <th>Kapal</th>
      <th> : ' . $rowData['nm_kapal'] . '</th>
      <th>Kegiatan</th>
      <th> : ' . $rowData['nm_kegiatan'] . '</th>
    </tr>
    <tr>  
      <th>Mulai Kegiatan</th>
      <th> : ' . tanggalWaktu($rowData['mulai_kegiatan']) . '</th>
      <th>Selesai Kegiatan</th>
      <th> : ' . tanggalWaktu($rowData['finish_kegiatan']) . '</th>
    </tr>
    <tr>  
      <th>Durasi Kegiatan</th>
      <th> : ' . $rowData['durasi'] . '</th>
    </tr>
  </table> </font><br>';

$content .= '<font size="7">
    <table border="1" cellspacing="0" cellpadding="2" align="center">
    <!-- <thead> -->
      <tr>
        <th style="text-align: center; width: 35px;" ><b>NO.</b></th>
        <th style="text-align: center; width: 120px;" ><b>TIME</b></th>
        <th style="text-align: center; width: 70px;" ><b>DOC NO </b></th>
        <th style="text-align: center; width: 15" ><b>QTY</b></th>
    		<th style="text-align: center; width: 180" ><b>DESCRIPTION</b></th>
        <th style="text-align: center; width: 75px" ><b>REMARKS</b></th>
        <th style="text-align: center; width: 70px" ><b>PLACED</b></th>      
        <th style="text-align: center; width: 40px" ><b>M<sup>3</sup></b></th>
        <th style="text-align: center; width: 40px" ><b>TON</b></th>
        <th style="text-align: center; width: 55px" ><b>TON/M<sup>3</sup></b></th>
      </tr>
      <!-- </thead> -->';
$content .= fetch_data();
$content .= '</table> </font><br><br><br>';

$obj_pdf->writeHTML($content);
$obj_pdf->Output('TimeSheet-' . $idJoborder . '.pdf', 'I');
?>

<!-- <!DOCTYPE html>
<html>
  <head>
    <title>test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"/>
  </head>
  <body>
    <br>
    <div class="container">
      <h4 align="center">Generate table data in myql tcpdf</h4><br>
        <div class="table-responsive">
          <div class="col-md-11" align="right">
            <form method="POST">
              <input type="submit" name="generate_pdf" class="btn btn-success" value="Generate PDF"/>
            </form>
          </div>
          <table class="table table-bordered">
            <tr>
              <td width="50">ID</td>
              <td width="50">Name</td>
              <td width="50">Test</td>
              <td width="50">Test</td>
            </tr>
            </?php
              echo fetch_data();
            ?>
          </table>
        </div>
    </div>
  </body>
</html> -->