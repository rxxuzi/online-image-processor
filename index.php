<?php
function isValidImagePath($path) {
    return file_exists($path) && is_file($path);
}

function cleanupTmpFiles($directory = "tmp", $maxFiles = 4) {
    $files = array_diff(scandir($directory), array('.', '..'));
    if (count($files) <= $maxFiles) return;

    // ファイルを最終変更日でソート
    usort($files, function($a, $b) use ($directory) {
        return filemtime("$directory/$a") - filemtime("$directory/$b");
    });

    $toRemove = count($files) - $maxFiles;
    foreach ($files as $file) {
        if ($toRemove <= 0) return;
//        echo "Removing: $directory/$file<br>"; // 追加するデバッグ行
        if (is_file("$directory/$file")) {
            unlink("$directory/$file");
            $toRemove--;
        }
    }
}


session_start();
cleanupTmpFiles();
$uploaded_image = isset($_SESSION['uploaded_image']) ? $_SESSION['uploaded_image'] : '';
$converted_image = 'tmp/converted_' . basename($uploaded_image);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Online Image Processor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Online Image Processor</h1>
<form action="convert.php" method="post" enctype="multipart/form-data">
    <div class="file-upload">
        <label for="image" class="file-upload_label">Select image</label>
        <input type="file" name="image" id="image" class="file-upload_input" required>
    </div>
    <div class="select-container">
        <label>
            <select name="conversion_type" id="conversion-type">
                <option value="grayscale">Gray Scale</option>
                <option value="binary">Binarisation</option>
                <option value="edge_enhance">Edge Enhancement</option>
                <option value="edge_detect">Edge Detection</option>
                <option value="noise_reduction">Noise Reduction</option>
            </select>
        </label>
    </div>
    <div class="file-conversion">
        <input type="submit" value="Conversion">
    </div>
    <input type="hidden" name="uploaded_image_path" value="<?php echo $uploaded_image; ?>">
</form>

<div class="image-wrapper">
    <div class="image-container" id="preview-container" style="<?php echo isValidImagePath($uploaded_image) ? '' : 'display: none;'; ?>">
        <h2>Before:</h2>
        <img id="preview" src="<?php echo isValidImagePath($uploaded_image) ? $uploaded_image : ''; ?>" alt="変換前の画像" />
    </div>
    <?php if (isValidImagePath($converted_image)): ?>
        <div class="image-container">
            <h2>After:</h2>
            <img id="converted" src="<?php echo $converted_image; ?>" alt="変換後の画像" />
        </div>
    <?php endif; ?>
</div>

<script src="script.js"></script>
</body>
</html>

<?php
unset($_SESSION['uploaded_image']);
unset($_SESSION['converted_image']);
?>

