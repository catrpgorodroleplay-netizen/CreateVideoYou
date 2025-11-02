<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? '';

if ($action === 'upload') {
    // Создаем папки если их нет
    if (!is_dir('videos')) mkdir('videos');
    if (!is_dir('data')) mkdir('data');
    
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (isset($_FILES['video'])) {
        $videoFile = $_FILES['video'];
        $filename = time() . '_' . basename($videoFile['name']);
        $filepath = 'videos/' . $filename;
        
        if (move_uploaded_file($videoFile['tmp_name'], $filepath)) {
            // Сохраняем в базу данных (JSON файл)
            $dbFile = 'data/videos.json';
            $videos = [];
            
            if (file_exists($dbFile)) {
                $videos = json_decode(file_get_contents($dbFile), true) ?? [];
            }
            
            $newVideo = [
                'id' => count($videos) + 1,
                'title' => $title,
                'description' => $description,
                'filename' => $filename,
                'views' => 0,
                'upload_date' => date('Y-m-d H:i:s')
            ];
            
            $videos[] = $newVideo;
            file_put_contents($dbFile, json_encode($videos));
            
            echo json_encode(['success' => true, 'video' => $newVideo]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка загрузки файла']);
        }
    }
} elseif ($action === 'get_videos') {
    // Получаем все видео
    $dbFile = 'data/videos.json';
    
    if (file_exists($dbFile)) {
        $videos = json_decode(file_get_contents($dbFile), true) ?? [];
        echo json_encode($videos);
    } else {
        echo json_encode([]);
    }
}
?>
