<?php
session_start();
include '../dashboard/koneks.php';

// Ambil data dari form
$id_update     = $_POST['id_siswa_update'];
$fullname      = $_POST['fullname'];
$address       = $_POST['address'];
$phone_number  = $_POST['country_code'] . $_POST['phone_number'];
$gender        = $_POST['gender'];
$username      = $_POST['username'];
$password      = $_POST['password'];

// Inisialisasi validasi
$allowedPhotoExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
$error = false;
$errfullname = false;
$erraddress = false;
$errphone = false;

// Validasi nama lengkap
if (!preg_match('/^[a-zA-Z\s.]+$/', $fullname) || preg_match('/[<>;\'"()|&%*$^]/', $fullname)) {
    $error = true;
    $errfullname = true;
}

// Validasi alamat
if (preg_match('/[<>;\'"()|&%*$^]/', $address)) {
    $error = true;
    $erraddress = true;
}

// Validasi nomor telepon
if (strlen($phone_number) > 15) {
    $error = true;
    $errphone = true;
}

// Jika validasi gagal, kembali ke profil.php
if ($error) {
    $_SESSION['error'] = "Gagal memperbarui profil. Mohon periksa kembali:";
    if ($errfullname) {
        $_SESSION['error'] .= "<br> - Nama hanya boleh berisi huruf, spasi, dan titik.";
    }
    if ($erraddress) {
        $_SESSION['error'] .= "<br> - Alamat tidak boleh mengandung karakter berbahaya.";
    }
    if ($errphone) {
        $_SESSION['error'] .= "<br> - Nomor telepon maksimal 15 digit.";
    }
    header("Location: profil.php");
    exit;
}

// Proses upload foto jika ada
$photoFileName = '';
if (!empty($_FILES['photo']['name'])) {
    $uploadedExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    if (in_array($uploadedExtension, $allowedPhotoExtensions)) {
        $targetDirectory = "../assets/images/siswa/";
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }
        $currentTime = date('Ymd_His');
        $photoFileName = $fullname . '_' . $currentTime . '.' . $uploadedExtension;
        $targetFilePath = $targetDirectory . $photoFileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
            // Hapus foto lama jika ada
            $getPhoto = mysqli_query($con, "SELECT photo_filename FROM siswa WHERE id = '$id_update'");
            if ($getPhoto && mysqli_num_rows($getPhoto) > 0) {
                $old = mysqli_fetch_assoc($getPhoto)['photo_filename'];
                $oldPath = $targetDirectory . $old;
                if (file_exists($oldPath) && is_file($oldPath)) {
                    unlink($oldPath);
                }
            }
        } else {
            $_SESSION['error'] = "Gagal mengunggah foto.";
            header("Location: profil.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Format foto tidak diperbolehkan.";
        header("Location: profil.php");
        exit;
    }
}

// Password MD5 jika ada
$setPassword = '';
if (!empty($password)) {
    $hashed = md5($password);
    $setPassword = ", password = '$hashed'";
}

// Tambahkan jika ada foto
$setPhoto = '';
if (!empty($photoFileName)) {
    $setPhoto = ", photo_filename = '$photoFileName'";
}

// Query update
$update = mysqli_query($con, "UPDATE siswa SET 
    fullname = '$fullname',
    address = '$address',
    phone_number = '$phone_number',
    gender = '$gender',
    username = '$username'
    $setPassword
    $setPhoto
    WHERE id = '$id_update'");

if ($update) {
    $_SESSION['success'] = "Profil berhasil diperbarui.";
} else {
    $_SESSION['error'] = "Terjadi kesalahan saat memperbarui data.";
}

header("Location: profil.php");
exit;
?>
