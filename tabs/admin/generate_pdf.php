<?php
require '../../library/fpdf/fpdf.php';
include '../../database/db_connect.php';

// Capture date filter from GET
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$toDate   = isset($_GET['to_date']) ? $_GET['to_date'] : null;

function dateFilter($column, $from, $to) {
    if ($from && $to) {
        return "WHERE $column BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
    } elseif ($from) {
        return "WHERE $column >= '$from 00:00:00'";
    } elseif ($to) {
        return "WHERE $column <= '$to 23:59:59'";
    }
    return "";
}

// Fetch counts
$ebookFilter = dateFilter('created_at', $fromDate, $toDate);
$ebookCount = $conn->query("SELECT COUNT(*) AS total FROM ebooks $ebookFilter")->fetch_assoc()['total'];

$userFilter = dateFilter('loggedin', $fromDate, $toDate);
$userCount = $conn->query("SELECT COUNT(*) AS total FROM account $userFilter")->fetch_assoc()['total'];

$guestFilter = dateFilter('created_at', $fromDate, $toDate);
$guestCount = $conn->query("SELECT COUNT(*) AS total FROM guestlog $guestFilter")->fetch_assoc()['total'];

// Research count per category
$researchFilter = dateFilter('created_at', $fromDate, $toDate);
$researchCategoriesResult = $conn->query("SELECT category, COUNT(*) AS total FROM research $researchFilter GROUP BY category");
$researchCategories = [];
while($row = $researchCategoriesResult->fetch_assoc()){
    $researchCategories[] = $row;
}

// eBook categories
$ebookCategoriesResult = $conn->query("SELECT category, COUNT(*) AS total FROM ebooks $ebookFilter GROUP BY category");
$ebookCategories = [];
while($row = $ebookCategoriesResult->fetch_assoc()){
    $ebookCategories[] = $row;
}

// eBook locations
$ebookLocationsResult = $conn->query("SELECT location, COUNT(*) AS total FROM ebooks $ebookFilter GROUP BY location");
$ebookLocations = [];
while($row = $ebookLocationsResult->fetch_assoc()){
    $ebookLocations[] = $row;
}

// Research count by Department
$researchDeptResult = $conn->query("SELECT Department, COUNT(*) AS total FROM research $researchFilter GROUP BY Department");
$researchDepartments = [];
while($row = $researchDeptResult->fetch_assoc()){
    $researchDepartments[] = $row;
}

// Initialize FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Header Title
$pdf->Cell(0,10,'OLPCC E-Library Data Summary',0,1,'C');
$pdf->SetFont('Arial','',12);
if($fromDate || $toDate){
    $pdf->Cell(0,8,'Filtered From: '.($fromDate ?: 'Start').' To: '.($toDate ?: 'End'),0,1,'C');
}
$pdf->Ln(5);

// Counts
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Counts',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,6,"Total eBooks: $ebookCount",0,1);
$pdf->Cell(0,6,"Total Users: $userCount",0,1);
$pdf->Cell(0,6,"Total Guests: $guestCount",0,1);
$pdf->Ln(5);

// Research Count per Category
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Research Count per Category',0,1);
$pdf->SetFont('Arial','',12);
foreach($researchCategories as $rc){
    $pdf->Cell(0,6,"{$rc['category']}: {$rc['total']}",0,1);
}
$pdf->Ln(5);

// eBook Categories
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'eBook Categories',0,1);
$pdf->SetFont('Arial','',12);
foreach($ebookCategories as $ec){
    $pdf->Cell(0,6,"{$ec['category']}: {$ec['total']}",0,1);
}
$pdf->Ln(5);

// eBook Locations
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'eBook Locations',0,1);
$pdf->SetFont('Arial','',12);
foreach($ebookLocations as $el){
    $pdf->Cell(0,6,"{$el['location']}: {$el['total']}",0,1);
}
$pdf->Ln(5);

// Research Count by Department
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Research Count by Department',0,1);
$pdf->SetFont('Arial','',12);
foreach($researchDepartments as $rd){
    $pdf->Cell(0,6,"{$rd['Department']}: {$rd['total']}",0,1);
}

// Output PDF
$pdf->Output('D','OLPCC_E-Library_Data_Summary.pdf');
