<?php
$filename = isset($_GET['file']) ? basename($_GET['file']) : null;

// Validasi nama file dan pastikan hanya file .pdf
if (!$filename || pathinfo($filename, PATHINFO_EXTENSION) !== 'pdf') {
    echo "File tidak valid atau tidak ditemukan.";
    exit;
}

// Path sistem (untuk pengecekan file) dan path publik (untuk browser)
$serverPath = realpath(__DIR__ . '/../assets/pdf/' . $filename);
$publicPath = '../assets/pdf/' . rawurlencode($filename);

// Cek apakah file ada secara fisik di server
if (!$serverPath || !file_exists($serverPath)) {
    echo "File tidak ditemukan di server.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pratinjau PDF</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        #toolbar {
            padding: 10px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #pdf-render {
            flex: 1;
            overflow: auto;
            background: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        canvas {
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div id="toolbar">
        <button onclick="prevPage()">⬅ Prev</button>
        <button onclick="nextPage()">Next ➡</button>
        <span>Page: <span id="page-num">1</span> / <span id="page-count">1</span></span>
        <button onclick="zoomOut()">➖ Zoom Out</button>
        <button onclick="zoomIn()">➕ Zoom In</button>
    </div>

    <div id="pdf-render">
        <canvas id="pdf-canvas"></canvas>
    </div>

    <script>
        const url = '<?= $publicPath ?>';
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');

        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.5;

        function renderPage(num) {
            pageRendering = true;
            pdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);
                renderTask.promise.then(() => {
                    pageRendering = false;
                    document.getElementById('page-num').textContent = num;

                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function prevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        function nextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }

        function zoomIn() {
            scale += 0.25;
            queueRenderPage(pageNum);
        }

        function zoomOut() {
            if (scale > 0.5) {
                scale -= 0.25;
                queueRenderPage(pageNum);
            }
        }

        // Load PDF
        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        pdfjsLib.getDocument(url).promise.then(pdf => {
            pdfDoc = pdf;
            document.getElementById('page-count').textContent = pdfDoc.numPages;
            renderPage(pageNum);
        }).catch(error => {
            document.getElementById('pdf-render').innerHTML =
                '<p style="color:red;text-align:center;">Gagal memuat PDF: ' + error.message + '</p>';
        });
    </script>

</body>
</html>
