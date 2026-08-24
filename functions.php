<?php
// functions.php

function koneksi() {
    $con = mysqli_connect("localhost", "root", "", "projecteperpus");
    if (!$con) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
    return $con;
}

function query($query) {
    $con = koneksi();
    $result = mysqli_query($con, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function queryReadData($query) {
    global $con; // Mengasumsikan $con adalah koneksi database Anda
    $result = mysqli_query($con, $query);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}