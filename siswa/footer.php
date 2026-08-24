<div class="py-6 px-6 text-center">
    <p class="mb-0 fs-4">Design and Developed by <a href="https://www.instagram.com/sekolahsantamariafatima_?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank"
            class="pe-1 text-primary text-decoration-underline">SaMarFa</a></p>
</div>
</div>
</div>
</div>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>
<script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.js"></script>
<script src="../assets/js/dashboard.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select-book').select2({
        theme: 'bootstrap-5', // Pakai tema Bootstrap 5
        placeholder: "Pilih Buku...",
        allowClear: true,
        width: '100%' // Agar lebar penuh seperti input lainnya
    });
});
</script>

<script>
    const selectBook = document.querySelector('.select-book');
    const bookInfo = document.getElementById('book-info');
    const infoYear = document.getElementById('info-year');
    const infoPublisher = document.getElementById('info-publisher');
    const infoRack = document.getElementById('info-rack');

    selectBook.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value !== "") {
            bookInfo.style.display = 'block';
            infoYear.textContent = selectedOption.getAttribute('data-year');
            infoPublisher.textContent = selectedOption.getAttribute('data-publisher');
            infoRack.textContent = selectedOption.getAttribute('data-rack');
        } else {
            bookInfo.style.display = 'none';
        }
    });
</script>



<script>
// Function to display the selected photo
function previewPhoto(input) {
    var previewContainer = document.getElementById('preview-container');
    var preview = document.getElementById('preview');

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    }
}

// Attach the previewPhoto function to the change event of the photo input
document.getElementById('photo').addEventListener('change', function() {
    previewPhoto(this);
});

// Function to display the selected photo
function previewImage(input) {
    var imagePreviewContainer = input.parentElement.querySelector('.image-preview-container');
    var imagePreview = imagePreviewContainer.querySelector('.image-preview');

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            imagePreview.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>

</html>