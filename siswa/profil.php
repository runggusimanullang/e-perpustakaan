<?php include_once("header.php"); ?>
<?php 
  $saya = $_SESSION['siswa_id'];
  $data = mysqli_query($con, "SELECT * from siswa WHERE id='$saya'");
  $d = mysqli_fetch_assoc($data);
 ?>
<h5 class="card-title fw-semibold mb-4">Forms Edit</h5>
<div class="card">
  <div class="card-body">
    <form role="form" method="post" action="profil_update.php" enctype="multipart/form-data">
      <input type="hidden" name="id_siswa_update" value="<?= $d['id'] ?>">
      <div class="mb-3">
        <label for="photo" class="form-label">Foto</label>
        <div class="image-preview-container mb-3">

          <?php if(isset($d['photo_filename'])){ ?>

            <img class="img-thumbnail rounded-circle image-preview" alt="Preview"
            src="../assets/images/siswa/<?= $d['photo_filename'] ?>"
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
        value="<?= $d['fullname']?>" required>
        <div id="fullnameHelp" class="form-text">Masukkan Nama Lengkap sesuai KTP
        </div>
      </div>
      <div class="mb-3">
        <label for="address" class="form-label">Alamat Lengkap</label>
        <textarea class="form-control" placeholder="Masukkan alamat lengkap" name="address"
        required><?= $d['address']?></textarea>
        <div id="fullnameHelp" class="form-text">Masukkan Alamat Lengkap sesuai KTP
        </div>
      </div>
      <div class="mb-3">
        <label for="phone_number" class="form-label">Nomer Telepon</label>
        <?php 
        $country_code = substr($d['phone_number'], 0, 3);
        $phone_number = substr($d['phone_number'], 3);
        ?>
        <div style="display: flex; align-items: center;">
          <select class="form-control input" name="country_code" style="width: 80px;"
          required>
          <option value="<?= $country_code ?>"><?= $country_code ?></option>
          <option value="+62">+62</option>
          <option value="+12">+12</option>
          <option value="+44">+44</option>
        </select>
        <input type="number" class="form-control" id="phone_number" style="flex: 1;"
        placeholder="Masukkan nomer telepon" name="phone_number"
        value="<?= $phone_number ?>" required>
      </div>

    </div>
    <div class="mb-3">
      <label for="address" class="form-label">Gender</label>
      <select class="form-control input" name="gender" required>
        <?php
        if($d['gender']!=="perempuan"){
          echo '<option value='.$d['gender'].'>Laki - Laki</option>';
        }else {
          echo '<option value='.$d['gender'].'>Perempuan</option>';
        }
        ?>
        <option value="laki-laki">Laki - Laki</option>
        <option value="perempuan">Perempuan</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" name="username" class="form-control" value="<?= $d['username'] ?>">
    </div>
    <div class="mb-3">
      <label for="Password" class="form-label">Password</label>
      <input type="password" name="password" class="form-control">
      <small style="color: red;">input jika akan diganti</small>
    </div>
    <button type="submit" name="update" class="btn btn-primary">Update</button>
  </form>
</div>
</div>




<?php include_once("footer.php"); ?>