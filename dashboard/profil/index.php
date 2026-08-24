<?php
session_start();

include_once("../koneks.php");
include_once("../navbar.php");
?>

<?php 
if (isset($_POST['update'])) {
    $id_update = $_POST['id_siswa_update'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $allowedPhotoExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
    $uploadedExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        // Upload photo
    $targetDirectory = "../../assets/images/admin/";


    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0755, true);
    }
        $currentTime = date('Ymd_His'); // Format: YYYYMMDD_HHMMSS
        $photoFileName = $fullname . '_' . $currentTime . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);

        $targetFilePath = $targetDirectory . $photoFileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {

            $getPhotoFilename = mysqli_query($con, "SELECT foto FROM admin WHERE id = '$id_update'");
            $row = mysqli_fetch_assoc($getPhotoFilename);
            $photoFilename = $row['foto'];
            $photoPath = "../../assets/images/admin/" . $photoFilename;
            if (file_exists($photoPath)&& is_file($photoPath)) {
                unlink($photoPath);
            }

            if($_POST['password']==""){

                $update = mysqli_query($con, "UPDATE admin SET fullname = '$fullname', email = '$email',foto='$photoFileName', username='$username' WHERE id = '$id_update'");
            }else{
               $update = mysqli_query($con, "UPDATE admin SET fullname = '$fullname', email = '$email',foto='$photoFileName', username='$username', password='$password' WHERE id = '$id_update'");                   
           }


           if ($update) {
            $_SESSION['success'] = "Berhasil mengubah data profil";
            echo '<script>window.location.href = "../profil/";</script>';
        } else {
            $_SESSION['error'] = "Gagal mengubah data barang";
        }


        }else{
            if($_POST['password']==""){

                $update = mysqli_query($con, "UPDATE admin SET fullname = '$fullname', email = '$email', username='$username' WHERE id = '$id_update'");
            }else{
               $update = mysqli_query($con, "UPDATE admin SET fullname = '$fullname', email = '$email', username='$username', password='$password' WHERE id = '$id_update'");                   
           }


           if ($update) {
            $_SESSION['success'] = "Berhasil mengubah data profil";
            echo '<script>window.location.href = "../profil/";</script>';
        } else {
            $_SESSION['error'] = "Gagal mengubah data barang";
        }
        }

    }
        ?>





        <h3 class="">Data Profil</h3>
        <div class="row">
            <div class="col-lg-12 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-body p-4">

                     <?php 
                     $id =  $_SESSION['admin_id'];
                     $result = mysqli_query($con, "SELECT * FROM admin where id='$id' ");
                     $old_data = mysqli_fetch_assoc($result);
                     ?>

                     <div class="card">
                        <div class="card-body">
                            <form role="form" method="post" action="./index.php" enctype="multipart/form-data">
                                <input type="hidden" name="id_siswa_update" value="<?= $old_data['id'] ?>">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto</label>
                                    <div class="image-preview-container mb-3">

                                        <?php if(isset($old_data['foto'])){ ?>

                                            <img class="img-thumbnail rounded-circle image-preview" alt="Preview"
                                            src="../../assets/images/admin/<?= $old_data['foto'] ?>"
                                            style="width: 200px; height: 200px;">
                                        <?php } else { ?>
                                            <img class="img-thumbnail rounded-circle image-preview" alt="Preview"
                                            src="../../assets/images/profile/user-1.jpg"
                                            style="width: 200px; height: 200px;">
                                        <?php } ?>
                                    </div>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*"
                                    onchange="previewImage(this)">
                                    <div id="photoHelp" class="form-text">Hanya menerima foto dengan ekstensi ('jpg','jpeg',
                                    'png', 'gif', 'bmp', 'webp')</div>
                                </div>
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="fullname"
                                    placeholder="Masukkan nama lengkap" name="fullname"
                                    value="<?= $old_data['fullname']?>" required>
                                    <div id="fullnameHelp" class="form-text">Masukkan Nama Lengkap sesuai KTP
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username"
                                    placeholder="Masukkan nama lengkap" name="username"
                                    value="<?= $old_data['username']?>" required>
                                    <div id="usernameHelp" class="form-text">Masukkan Username
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">email</label>
                                    <input type="text" class="form-control" id="email"
                                    placeholder="Masukkan nama lengkap" name="email"
                                    value="<?= $old_data['email']?>" required>
                                    <div id="usernameHelp" class="form-text">Masukkan Email
                                    </div>
                                </div>



                                <div class="mb-3">
                                    <label for="password" class="form-label">password</label>
                                    <input type="password" class="form-control" id="password"
                                    placeholder="Masukkan password" name="password">                                
                                </div>

                                <button type="submit" name="update" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <?php
    include_once("../footer.php");

?>