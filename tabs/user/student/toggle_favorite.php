<?php
session_start();
include '../../../database/db_connect.php';

$account_id = $_SESSION['account']['id'] ?? null;
if (!$account_id) { http_response_code(403); exit('Not logged in'); }

$ebook_id = intval($_POST['ebook_id'] ?? 0);
if (!$ebook_id) exit('Invalid ebook ID');

// Check if favorite exists
$res = $conn->prepare("SELECT id FROM account_ebook_favorite WHERE account_id=? AND ebook_id=?");
$res->bind_param("ii", $account_id, $ebook_id);
$res->execute();
$res->store_result();

if ($res->num_rows > 0) {
    $del = $conn->prepare("DELETE FROM account_ebook_favorite WHERE account_id=? AND ebook_id=?");
    $del->bind_param("ii", $account_id, $ebook_id);
    $del->execute();
    $del->close();
    echo 'removed';
} else {
    $ins = $conn->prepare("INSERT INTO account_ebook_favorite (account_id, ebook_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    $ins->bind_param("ii", $account_id, $ebook_id);
    $ins->execute();
    $ins->close();
    echo 'added';
}

$res->close();
