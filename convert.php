<?php

// Use gd Library

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {

    $imageData = file_get_contents($_FILES['image']['tmp_name']);
    $image = imagecreatefromstring($imageData);

    // 画像が正しく読み込まれたかどうかの確認
    if (!$image) {
        die('画像の読み込みに失敗しました。');
    }

    $conversion_type = $_POST['conversion_type'];

    switch ($conversion_type) {
        case 'grayscale':
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            break;

        case 'binary':
            // グレースケールに変換
            imagefilter($image, IMG_FILTER_GRAYSCALE);

            // 二値化処理
            $width = imagesx($image);
            $height = imagesy($image);
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    $pixel = imagecolorat($image, $x, $y);
                    $rgb = imagecolorsforindex($image, $pixel);
                    $gray = ($rgb['red'] + $rgb['green'] + $rgb['blue']) / 3;

                    if ($gray > 127) {
                        $color = imagecolorallocate($image, 255, 255, 255); // 白
                    } else {
                        $color = imagecolorallocate($image, 0, 0, 0); // 黒
                    }

                    imagesetpixel($image, $x, $y, $color);
                }
            }
            break;

        case 'edge_enhance':
            // エッジ強調のコード
            imagefilter($image, IMG_FILTER_EDGEDETECT);
            imagefilter($image, IMG_FILTER_CONTRAST, -50); // これはコントラストを強調するための例です。
            break;

        case 'edge_detect':
            // エッジ検出のコード
            imagefilter($image, IMG_FILTER_EDGEDETECT);
            break;

        case 'noise_reduction':
            // ノイズ除去のコード
            imagefilter($image, IMG_FILTER_SMOOTH, 8); // 8は平滑化の度合いです。調整が必要な場合は変更してください。
            break;
    }

    // アップロードされた画像を保存
    $uploaded_filename = 'tmp/' . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], $uploaded_filename);

// 変換された画像を保存
    $output_filename = 'tmp/converted_' . $_FILES['image']['name'];
    imagepng($image, $output_filename);


// index.php にリダイレクトして結果を表示 (クエリパラメータを使用しないシンプルなリダイレクト)
    session_start();
    // アップロードされた画像のパスをセッション変数に保存
    $_SESSION['uploaded_image'] = 'tmp/' . $_FILES['image']['name'];
    $_SESSION['converted_image'] = $output_filename;
    header('Location: index.php');


}

function clear_old_files($dir, $hours = 1) {
    if (!is_dir($dir)) {
        return;
    }

    $iterator = new DirectoryIterator($dir);
    $now = time();

    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile() && ($now - $fileinfo->getCTime()) >= ($hours * 3600)) {
            unlink($fileinfo->getRealPath());
        }
    }
}

// 一時フォルダ内の1時間以上古いファイルを削除
clear_old_files('tmp');


