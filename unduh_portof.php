<?php
require('library/fpdf.php');
require_once 'db.php';

// Periksa apakah sesi user_id ada
if (!isset($_SESSION['user_id'])) {
  die("User not logged in");
}

$user_id = $_SESSION['user_id'];

// Ambil data portofolio dari database
$sql = "SELECT nama_lengkap, email, tanggal_lahir, no_telepon, jenjang_pendidikan, jurusan, deskripsi, link_portofolio, upload_karya 
        FROM portofolio WHERE id_pengguna = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
  die("Gagal load portofolio!");
}

class PDF extends FPDF
{
  // Header
  function Header()
  {
    $this->SetFont('Arial', 'B', 20);
    $this->SetTextColor(50, 50, 50);
    $this->Cell(0, 10, 'Portofolio', 0, 1, 'C');
    $this->SetDrawColor(200, 200, 200);
    $this->Line(10, 20, 200, 20);
    $this->Ln(10);
  }

  // Footer
  function Footer()
  {
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->SetTextColor(150, 150, 150);
    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
  }

  // Tambahkan Section
  function SectionTitle($title)
  {
    $this->SetFont('Arial', 'B', 12);
    $this->SetTextColor(0, 102, 204);
    $this->Cell(0, 10, $title, 0, 1, 'L');
    $this->Ln(5);
  }

  // Tambahkan Konten
  function SectionContent($content)
  {
    $this->SetFont('Arial', '', 11);
    $this->SetTextColor(50, 50, 50);
    $this->MultiCell(0, 7, $content);
    $this->Ln(5);
  }
}

// Inisialisasi FPDF
$pdf = new PDF();
$pdf->AddPage();

// Menampilkan data pengguna
$pdf->SectionTitle('Informasi Pribadi');
$pdf->SectionContent("Nama Lengkap: " . $data['nama_lengkap']);
$pdf->SectionContent("Email: " . $data['email']);
$pdf->SectionContent("Tanggal Lahir: " . $data['tanggal_lahir']);
$pdf->SectionContent("No. Telepon: " . $data['no_telepon']);

// Menampilkan data pendidikan
$pdf->SectionTitle('Pendidikan');
$pdf->SectionContent("Jenjang Pendidikan: " . $data['jenjang_pendidikan']);
$pdf->SectionContent("Jurusan: " . $data['jurusan']);

// Menampilkan deskripsi
$pdf->SectionTitle('Deskripsi');
$pdf->SectionContent($data['deskripsi']);

// Menampilkan link portofolio
$pdf->SectionTitle('Link Portofolio');
$pdf->SetFont('Arial', 'U', 11);
$pdf->SetTextColor(0, 0, 255);
$pdf->Cell(0, 10, $data['link_portofolio'], 0, 1, 'L', false, $data['link_portofolio']);
$pdf->Ln(5);

// Menampilkan link portofolio
$pdf->SectionTitle('Karya');
$pdf->SetFont('Arial', 'U', 11);
$pdf->SetTextColor(0, 0, 255);
$pdf->Cell(0, 10, "http://" . $_SERVER['HTTP_HOST']. "/user/uploads/karya/" .  $data['upload_karya'], 0, 1, 'L', false, "http://" . $_SERVER['HTTP_HOST']. "/user/uploads/karya/" .  $data['upload_karya']);
$pdf->Ln(5);

// Output PDF
$filename = "portofolio.pdf";
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Output PDF untuk diunduh
$pdf->Output('D', $filename);
