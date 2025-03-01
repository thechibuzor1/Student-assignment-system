<?php
include_once('../backend/config.php');
require('libs/fpdf/fpdf.php'); // Include FPDF library

// Fetch finalized assignments
$query = "SELECT s.matricNumber, s.name AS student_name, s.email, s.cgpa, 
                 l.name AS lecturer_name, l.email AS lecturer_email 
          FROM student s
          LEFT JOIN lecturer l ON s.supervisorID = l.id
          ORDER BY l.name ASC, s.cgpa DESC";

$result = mysqli_query($connection, $query);
$assignments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $assignments[] = $row;
}

// Handle export request
if (isset($_POST['format']) && $_POST['format'] === 'excel') {
    exportToCSV($assignments);
} else {
    exportToPDF($assignments);
}

// ✅ FUNCTION TO EXPORT AS CSV (WIDER COLUMNS)
function exportToCSV($assignments)
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="assignments.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Matric Number', 'Student Name', 'Email', 'CGPA', 'Supervisor', 'Supervisor Email']);

    foreach ($assignments as $data) {
        fputcsv($output, [
            $data['matricNumber'],
            $data['student_name'],
            $data['email'],
            $data['cgpa'],
            $data['lecturer_name'] ?: 'Not Assigned',
            $data['lecturer_email'] ?: '-'
        ]);
    }
    fclose($output);
    exit();
}

// ✅ FUNCTION TO EXPORT AS PDF (FIXED TRUNCATION & WIDER COLUMNS)
function exportToPDF($assignments)
{
    $pdf = new FPDF('L', 'mm', 'A4'); // Landscape mode for better fit
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    
    // Title
    $pdf->Cell(0, 10, 'Student-Supervisor Assignments', 0, 1, 'C');
    $pdf->Ln(5);

    // Table Headers
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(35, 10, 'Matric No', 1);
    $pdf->Cell(50, 10, 'Student Name', 1);
    $pdf->Cell(60, 10, 'Email', 1);
    $pdf->Cell(20, 10, 'CGPA', 1);
    $pdf->Cell(50, 10, 'Supervisor', 1);
    $pdf->Cell(70, 10, 'Supervisor Email', 1); // Wider to fit email
    $pdf->Ln();

    // Table Data
    $pdf->SetFont('Arial', '', 9);
    foreach ($assignments as $data) {
        $pdf->Cell(35, 10, $data['matricNumber'], 1);
        $pdf->Cell(50, 10, $data['student_name'], 1);
        $pdf->Cell(60, 10, $data['email'], 1);
        $pdf->Cell(20, 10, $data['cgpa'], 1);
        $pdf->Cell(50, 10, $data['lecturer_name'] ?: 'Not Assigned', 1);
        
        // ✅ Fix Lecturer Email Cutting Issue with MultiCell
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(70, 10, $data['lecturer_email'] ?: '-', 1);
        $pdf->SetXY($x + 70, $y); // Move cursor for next row

        $pdf->Ln();
    }

    // Output PDF
    $pdf->Output('D', 'assignments.pdf');
    exit();
}
?>
