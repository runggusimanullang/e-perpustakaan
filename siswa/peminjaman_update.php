<?php 
session_start();
include '../dashboard/koneks.php';
$borrower_id= $_SESSION['siswa_id'];
$id = $_POST['id'];
$book_id = $_POST['book_id'];
$loan_date = $_POST['loan_date'];
$return_date = $_POST['return_date'];

mysqli_query($con, "UPDATE borrowing SET book_id ='$book_id', loan_date='$loan_date', return_date='$return_date' where id='$id'");
header("location:peminjaman.php");