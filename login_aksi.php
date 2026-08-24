<?php
session_start();
include 'dashboard/koneks.php';

if (isset($_POST['login'])) {
    $username = $_POST['user'];
    $password = md5($_POST['pass']);
    $level = $_POST['level'];
    if($level=="Siswa"){
         // Validasi input
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Username dan password wajib diisi.";
            header("Location: index.php");
            exit();
        }

    // Query untuk cek user
        $stmt = $con->prepare("SELECT * FROM siswa WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

    // Cek hasil
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

        // Simpan session
            $_SESSION['siswa_id'] = $user['id'];
            $_SESSION['siswa_nama'] = $user['fullname'];
            $_SESSION['siswa_username'] = $user['username'];
            $_SESSION['success'] = "login_siswa";

            header("Location: siswa/index.php");
            exit();
        } else {
            $_SESSION['error'] = "Username atau password salah!";
            header("Location: login.php");
            exit();
        }
    }elseif ($level=="Admin") {
        $query = "SELECT * FROM admin where username='$username' and password='$password'";
        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) > 0) {
            // Loop melalui hasil query
            while ($row = mysqli_fetch_assoc($result)) {
                $username = $row['username'];
                $password = $row['password'];
                $enteredPassword = md5($_POST['pass']);
                                 
                    $_SESSION['loggedin'] = true;                
                    $_SESSION['login'] = true;
                    $_SESSION['admin_id'] = $row['id'];
                    $query = "SELECT * FROM admin WHERE id = $row[id];";
                    $result = mysqli_query($con, $query);
                    $user = mysqli_fetch_assoc($result);
                    $name = $user['fullname'];
                    $_SESSION['success'] = "Selamat datang kembali sodara $name";
                    unset($_SESSION['error']);
                    header('Location: dashboard/home/');
                    exit();

            }
        } else {
          $_SESSION['error'] = "Username atau password salah!";
          header("Location: login.php");
          exit();
        }
    }

   
} else {
    $_SESSION['error'] = "Akses langsung tidak diperbolehkan!";
    header("Location: login.php");
    exit();
}
?>
