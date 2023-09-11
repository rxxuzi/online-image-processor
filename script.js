document.getElementById('image').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewImg = document.getElementById('preview');
            const previewContainer = document.getElementById('preview-container');

            previewImg.src = e.target.result;
            previewContainer.style.display = "block";
        }
        reader.readAsDataURL(file);
    }
});
