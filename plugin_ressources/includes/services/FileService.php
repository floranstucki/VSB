<?php
namespace VSB\services;

class FileService {
    public function upload($file) {
        if ($file['error'] !== 0) return false;
    
        $allowed = ['doc', 'docx', 'pdf'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) return false;
    
        $upload_dir = plugin_dir_path(__DIR__) . '../uploads/';
        $filename = uniqid() . '_' . basename($file['name']);
        $path = $upload_dir . $filename;
    
        if (move_uploaded_file($file['tmp_name'], $path)) {
            // Création manuelle de l'URL en local
            $relative_url = plugins_url('uploads/' . $filename, dirname(__DIR__));
            return $relative_url;
        }
    
        return false;
    }
    
}
