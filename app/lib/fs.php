<?php

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/db.php");

const FS_BASE_PATH = __DIR__ . "/../../fs";

class FileSystemManager
{
    private string $base_path;

    public function __construct(string $base_path)
    {

        $this->base_path = rtrim($base_path, '/\\');

        if (!is_dir($this->base_path)) {

            if (!mkdir($this->base_path, 0755, true)) {
                error_log("Failed to create base directory: " . $this->base_path);
                throw new Exception("Failed to initialize FileSystemManager: Base directory could not be created.");
            }
        }

        $this->base_path = realpath($this->base_path);
        if ($this->base_path === false) {
            error_log("Failed to resolve real path for base directory: " . $base_path);
            throw new Exception("Failed to initialize FileSystemManager: Base directory path is invalid.");
        }
        $this->base_path = str_replace("\\", "/", $this->base_path);
    }

    public function createFolder(int $owner_id, string $relative_path): array
    {
        try {
            $conn = connect_db();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database connection failed', 'id' => null, 'path' => null];
        }

        $relative_path_sanitized = $this->sanitizePath($relative_path);
        $full_path = $this->base_path . '/' . $relative_path_sanitized;

        if (!$this->isPathWithinBasePath($full_path)) {
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Invalid path: attempts to create outside base directory', 'id' => null, 'path' => null];
        }

        if (file_exists($full_path)) {
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Folder or file already exists at this path', 'id' => null, 'path' => $full_path];
        }

        if (!mkdir($full_path, 0755, true)) {
            disconnect_db($conn);
            error_log("Failed to create folder on filesystem: " . $full_path);
            return ['success' => false, 'message' => 'Failed to create folder on filesystem', 'id' => null, 'path' => null];
        }

        $real_path = realpath($full_path);
        $real_path = str_replace("\\", "/", $real_path);

        if (!$this->isPathWithinBasePath($real_path, true)) {
            error_log("Folder created, but its real path escaped base directory: " . $real_path);
            rmdir($full_path);
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Security violation: created folder path is outside base directory', 'id' => null, 'path' => null];
        }

        $stmt = $conn->prepare("INSERT INTO fs (owner, type, path, size) VALUES (?, 'folder', ?, 0)");
        if (!$stmt) {
            rmdir($full_path);
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Database prepare failed', 'id' => null, 'path' => null];
        }
        $stmt->bind_param("is", $owner_id, $real_path);

        if (!$stmt->execute()) {
            rmdir($full_path);
            $error = $stmt->error;
            disconnect_db($conn, $stmt);
            error_log("Database insert failed for folder: " . $error);
            return ['success' => false, 'message' => 'Database insert failed', 'id' => null, 'path' => null];
        }

        $insert_id = $stmt->insert_id;
        disconnect_db($conn, $stmt);

        return [
            'success' => true,
            'message' => 'Folder created successfully',
            'id' => $insert_id,
            'path' => $real_path
        ];
    }

    public function createFile(int $owner_id, string $relative_path, string $content = ''): array
    {
        $file_len = strlen($content);

        if (!$this->new_file_is_within_boundary($file_len)) {
            return ['success' => false, 'message' => "File doesn't fit on the filesystem; there's too much stuff!", 'id' => null, 'path' => null];
        }

        try {
            $conn = connect_db();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database connection failed', 'id' => null, 'path' => null];
        }

        $relative_path_sanitized = $this->sanitizePath($relative_path);
        $full_path = $this->base_path . '/' . $relative_path_sanitized;


        if (!$this->isPathWithinBasePath($full_path)) {
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Invalid path: attempts to create outside base directory', 'id' => null, 'path' => null];
        }

        if (file_exists($full_path)) {
            disconnect_db($conn);
            return ['success' => false, 'message' => 'File or folder already exists at this path', 'id' => null, 'path' => $full_path];
        }

        $parent_dir = dirname($full_path);
        if (!is_dir($parent_dir)) {
            if (!mkdir($parent_dir, 0755, true)) {
                disconnect_db($conn);
                error_log("Failed to create parent directory for file: " . $parent_dir);
                return ['success' => false, 'message' => 'Failed to create parent directory', 'id' => null, 'path' => null];
            }
        }

        if (file_put_contents($full_path, $content) === false) {
            disconnect_db($conn);
            error_log("Failed to write file to filesystem: " . $full_path);
            return ['success' => false, 'message' => 'Failed to write file to filesystem', 'id' => null, 'path' => null];
        }

        $real_path = realpath($full_path);
        $real_path = str_replace("\\", "/", $real_path);

        if (!$this->isPathWithinBasePath($real_path, true)) {
            error_log("File created, but its real path escaped base directory: " . $real_path);
            unlink($full_path);
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Security violation: created file path is outside base directory', 'id' => null, 'path' => null];
        }

        $stmt = $conn->prepare("INSERT INTO fs (owner, type, path, size) VALUES (?, 'file', ?, ?)");
        if (!$stmt) {
            unlink($full_path);
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Database prepare failed', 'id' => null, 'path' => null];
        }
        $stmt->bind_param("isi", $owner_id, $real_path, $file_len);

        if (!$stmt->execute()) {
            unlink($full_path);
            $error = $stmt->error;
            disconnect_db($conn, $stmt);
            error_log("Database insert failed for file: " . $error);
            return ['success' => false, 'message' => 'Database insert failed', 'id' => null, 'path' => null];
        }

        $insert_id = $stmt->insert_id;
        disconnect_db($conn, $stmt);

        return [
            'success' => true,
            'message' => 'File created successfully',
            'id' => $insert_id,
            'path' => $real_path
        ];
    }

    public function uploadFile(int $owner_id, string $relative_path, array $uploaded_file): array
    {
        if (!isset($uploaded_file['tmp_name']) || !is_uploaded_file($uploaded_file['tmp_name'])) {
            return ['success' => false, 'message' => 'Invalid file upload or not an uploaded file', 'id' => null, 'path' => null];
        }

        if ($uploaded_file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error', 'id' => null, 'path' => null];
        }

        $file_size = $uploaded_file["size"] ?? 0;

        if (!$this->new_file_is_within_boundary($file_size)) {
            return ['success' => false, 'message' => "File doesn't fit on the filesystem; there's too much stuff!", 'id' => null, 'path' => null];
        }

        try {
            $conn = connect_db();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database connection failed', 'id' => null, 'path' => null];
        }

        $relative_path_sanitized = $this->sanitizePath($relative_path);
        $final_target_path = '';
        $intended_target_dir = $this->base_path . '/' . $relative_path_sanitized;

        if (is_dir($intended_target_dir) || empty($relative_path_sanitized)) {
            $final_target_path = $intended_target_dir . '/' . basename($uploaded_file['name']);
        } else {

            $final_target_path = $this->base_path . '/' . $relative_path_sanitized;
        }

        $full_path = $final_target_path;

        if (!$this->isPathWithinBasePath($full_path)) {
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Invalid path: attempts to upload outside base directory', 'id' => null, 'path' => null];
        }

        if (file_exists($full_path)) {
            disconnect_db($conn);

            return ['success' => false, 'message' => 'A file or folder already exists at the target path.', 'id' => null, 'path' => $full_path];
        }

        $parent_dir = dirname($full_path);
        if (!is_dir($parent_dir)) {
            if (!mkdir($parent_dir, 0755, true)) {
                disconnect_db($conn);
                error_log("Failed to create parent directory for uploaded file: " . $parent_dir);
                return ['success' => false, 'message' => 'Failed to create parent directory', 'id' => null, 'path' => null];
            }
        }

        if (!move_uploaded_file($uploaded_file['tmp_name'], $full_path)) {
            disconnect_db($conn);
            error_log("Failed to move uploaded file from " . $uploaded_file['tmp_name'] . " to " . $full_path);
            return ['success' => false, 'message' => 'Failed to move uploaded file', 'id' => null, 'path' => null];
        }

        $real_path = realpath($full_path);
        $real_path = str_replace("\\", "/", $real_path);

        if (!$this->isPathWithinBasePath($real_path, true)) {
            error_log("Uploaded file, but its real path escaped base directory: " . $real_path);
            unlink($full_path);
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Security violation: uploaded file path is outside base directory', 'id' => null, 'path' => null];
        }

        $stmt = $conn->prepare("INSERT INTO fs (owner, type, path, size) VALUES (?, 'file', ?, ?)");
        if (!$stmt) {
            unlink($full_path);
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Database prepare failed', 'id' => null, 'path' => null];
        }
        $stmt->bind_param("isi", $owner_id, $real_path, $file_size);

        if (!$stmt->execute()) {
            unlink($full_path);
            $error = $stmt->error;
            disconnect_db($conn, $stmt);
            error_log("Database insert failed for uploaded file: " . $error);
            return ['success' => false, 'message' => 'Database insert failed', 'id' => null, 'path' => null];
        }

        $insert_id = $stmt->insert_id;
        disconnect_db($conn, $stmt);

        return [
            'success' => true,
            'message' => 'File uploaded successfully',
            'id' => $insert_id,
            'path' => $real_path
        ];
    }
    private function sanitizePath(string $path): string
    {

        $path = trim($path, '/\\');
        $path = preg_replace('#[/\\\\]+#', '/', $path);
        $path = str_replace('../', '', $path);
        $path = str_replace('/..', '', $path);
        $path = str_replace('..', '', $path);

        return $path;
    }
    private function isPathWithinBasePath(string $path, bool $resolve_real_path = false): bool
    {
        $checked_path = $path;
        if ($resolve_real_path) {
            $checked_path = realpath($path);
            if ($checked_path === false) {
                return false;
            }
        }
        $checked_path = str_replace("\\", "/", $checked_path);
        return str_starts_with($checked_path, $this->base_path);
    }


    private function getUploadErrorMessage(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
            case UPLOAD_ERR_FORM_SIZE:
                return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
            case UPLOAD_ERR_PARTIAL:
                return "The uploaded file was only partially uploaded.";
            case UPLOAD_ERR_NO_FILE:
                return "No file was uploaded.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing a temporary folder.";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk.";
            case UPLOAD_ERR_EXTENSION:
                return "A PHP extension stopped the file upload.";
            default:
                return "Unknown upload error.";
        }
    }


    public function downloadFile(int $fs_id, ?int $owner_id = null): void
    {
        try {
            $conn = connect_db();
        } catch (Exception $e) {
            http_response_code(500);
            exit('Database connection failed');
        }

        $stmt = null;
        if ($owner_id !== null) {
            $stmt = $conn->prepare("SELECT type, path FROM fs WHERE id = ? AND owner = ? AND type = 'file'");
            if (!$stmt) {
                http_response_code(500);
                exit('Database prepare failed');
            }
            $stmt->bind_param("ii", $fs_id, $owner_id);
        } else {
            $stmt = $conn->prepare("SELECT type, path FROM fs WHERE id = ? AND type = 'file'");
            if (!$stmt) {
                http_response_code(500);
                exit('Database prepare failed');
            }
            $stmt->bind_param("i", $fs_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            disconnect_db($conn, $stmt);
            http_response_code(404);
            exit('File not found or not owned by the specified user');
        }

        $row = $result->fetch_assoc();
        $path = $row['path'];
        disconnect_db($conn, $stmt);


        if (!$this->isPathWithinBasePath($path, true)) {
            http_response_code(403);
            exit('Access denied: File path outside allowed directory.');
        }

        if (!file_exists($path) || !is_file($path)) {
            http_response_code(404);
            exit('File does not exist on filesystem or is not a file');
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));

        readfile($path);
        exit;
    }


    public function getFileInfo(int $fs_id, ?int $owner_id = null): ?array
    {
        try {
            $conn = connect_db();
        } catch (Exception $e) {
            error_log("Database connection failed in getFileInfo");
            return null;
        }

        $stmt = null;
        if ($owner_id !== null) {
            $stmt = $conn->prepare("SELECT fs.id, owner, type, path, users.username FROM fs INNER JOIN users ON users.id = fs.owner WHERE fs.id = ? AND owner = ?");
            if (!$stmt) {
                error_log("Database prepare failed in getFileInfo (owner): " . $conn->error);
                disconnect_db($conn);
                return null;
            }
            $stmt->bind_param("ii", $fs_id, $owner_id);
        } else {
            $stmt = $conn->prepare("SELECT fs.id, owner, type, path, users.username FROM fs INNER JOIN users ON users.id = fs.owner WHERE fs.id = ?");
            if (!$stmt) {
                error_log("Database prepare failed in getFileInfo (no owner): " . $conn->error);
                disconnect_db($conn);
                return null;
            }
            $stmt->bind_param("i", $fs_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            disconnect_db($conn, $stmt);
            return null;
        }

        $data = $result->fetch_assoc();
        disconnect_db($conn, $stmt);


        if (!$this->isPathWithinBasePath($data['path'], true)) {
            error_log("Security alert: FileSystem entry ID {$fs_id} points outside base directory. Path: " . $data['path']);
            return null;
        }

        return $data;
    }

    public function readFolder(string $relative_path = '', ?int $owner_id = null): array
    {
        $relative_path_sanitized = $this->sanitizePath($relative_path);
        $full_path = $relative_path_sanitized ? $this->base_path . '/' . $relative_path_sanitized : $this->base_path;


        if (!$this->isPathWithinBasePath($full_path)) {
            return ['success' => false, 'message' => 'Invalid path: attempts to read outside base directory', 'items' => []];
        }

        $real_path = realpath($full_path);
        if ($real_path === false || !is_dir($real_path)) {
            return ['success' => false, 'message' => 'Folder does not exist or is not a directory', 'items' => []];
        }
        $real_path = str_replace("\\", "/", $real_path);


        if (!$this->isPathWithinBasePath($real_path, true)) {
            error_log("Security violation: resolved real path for folder is outside base directory: " . $real_path);
            return ['success' => false, 'message' => 'Security violation: folder path is outside base directory', 'items' => []];
        }

        try {
            $conn = connect_db();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database connection failed', 'items' => []];
        }

        $stmt = null;
        $like_pattern = $real_path . '/%';

        if ($real_path === $this->base_path) {
            $like_pattern = $real_path . '/%';
        } else {
            $like_pattern = $real_path . '/%';
        }


        if ($owner_id !== null) {
            $stmt = $conn->prepare("SELECT fs.id, users.username, owner, type, path, size FROM fs INNER JOIN users ON users.id = fs.owner WHERE owner = ? AND path LIKE ? ORDER BY type DESC, path ASC");
            if (!$stmt) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed', 'items' => []];
            }
            $stmt->bind_param("is", $owner_id, $like_pattern);
        } else {
            $stmt = $conn->prepare("SELECT fs.id, users.username, owner, type, path, size FROM fs INNER JOIN users ON users.id = fs.owner WHERE path LIKE ? ORDER BY type DESC, path ASC");
            if (!$stmt) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed', 'items' => []];
            }
            $stmt->bind_param("s", $like_pattern);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        $scanned_fs_items = [];
        while ($row = $result->fetch_assoc()) {

            if (!$this->isPathWithinBasePath($row['path'], true)) {
                error_log("Security alert: Database entry ID {$row['id']} points outside base directory. Path: " . $row['path']);
                continue;
            }


            $path_parts = explode('/', substr($row['path'], strlen($real_path) + 1));
            if (count($path_parts) === 1 || ($row['type'] === 'folder' && count($path_parts) === 1 && is_dir($row['path']))) {

                if (file_exists($row['path'])) {
                    $items[] = [
                        'id' => $row['id'],
                        'owner' => $row['owner'],
                        'type' => $row['type'],
                        'path' => $row['path'],
                        'name' => basename($row['path']),
                        'username' => basename($row['username']),
                        'relative_path' => substr($row['path'], strlen($real_path) + 1),
                        'size' => $row['size'] ?? 0
                    ];
                    $scanned_fs_items[basename($row['path'])] = true;
                } else {
                    error_log("Database entry found but file/folder missing on filesystem: " . $row['path'] . " (ID: " . $row['id'] . ")");

                }
            }
        }
        disconnect_db($conn, $stmt);





        return [
            'success' => true,
            'message' => 'Folder read successfully',
            'items' => $items,
            'folder_path' => $real_path
        ];
    }

    public function listByOwner(int $owner_id, ?string $type = null): array
    {
        try {
            $conn = connect_db();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database connection failed', 'items' => []];
        }

        $stmt = null;
        if ($type !== null && in_array($type, ['file', 'folder'])) {
            $stmt = $conn->prepare("SELECT id, owner, type, path FROM fs WHERE owner = ? AND type = ? ORDER BY type DESC, path ASC");
            if (!$stmt) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed', 'items' => []];
            }
            $stmt->bind_param("is", $owner_id, $type);
        } else {
            $stmt = $conn->prepare("SELECT id, owner, type, path FROM fs WHERE owner = ? ORDER BY type DESC, path ASC");
            if (!$stmt) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed', 'items' => []];
            }
            $stmt->bind_param("i", $owner_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            if (!$this->isPathWithinBasePath($row['path'], true)) {
                error_log("Security alert: Database entry ID {$row['id']} (owner {$owner_id}) points outside base directory. Path: " . $row['path']);
                continue;
            }

            if (file_exists($row['path'])) {
                $items[] = [
                    'id' => $row['id'],
                    'owner' => $row['owner'],
                    'type' => $row['type'],
                    'path' => $row['path'],
                    'name' => basename($row['path'])
                ];
            } else {
                error_log("Database entry found for owner {$owner_id} but file/folder missing on filesystem: " . $row['path'] . " (ID: " . $row['id'] . ")");
            }
        }

        disconnect_db($conn, $stmt);

        return [
            'success' => true,
            'message' => 'Files retrieved successfully',
            'items' => $items
        ];
    }

    public function delete(int $fs_id, ?int $owner_id = null): array
    {
        try {
            $conn = connect_db();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        $stmt = null;
        if ($owner_id !== null) {
            $stmt = $conn->prepare("SELECT type, path FROM fs WHERE id = ? AND owner = ?");
            if (!$stmt) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed'];
            }
            $stmt->bind_param("ii", $fs_id, $owner_id);
        } else {
            $stmt = $conn->prepare("SELECT type, path FROM fs WHERE id = ?");
            if (!$stmt) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed'];
            }
            $stmt->bind_param("i", $fs_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            disconnect_db($conn, $stmt);
            return ['success' => false, 'message' => 'Entry not found in database or not owned by the specified user'];
        }

        $row = $result->fetch_assoc();
        $type = $row['type'];
        $path = $row['path'];
        $stmt->close();

        if (!$this->isPathWithinBasePath($path, true)) {
            error_log("Security alert: Attempted to delete a path outside base directory. Path: " . $path . " (FS ID: " . $fs_id . ")");
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Security violation: cannot delete path outside allowed directory'];
        }

        $fs_success = false;
        if ($type === 'file') {
            if (file_exists($path) && is_file($path)) {
                $fs_success = unlink($path);
            } elseif (!file_exists($path)) {
                $fs_success = true;
                error_log("File not found on filesystem but present in DB: " . $path . " (ID: " . $fs_id . "). Proceeding with DB deletion.");
            } else {
                error_log("Path exists but is not a file: " . $path . " (ID: " . $fs_id . ")");
                return ['success' => false, 'message' => 'Path exists but is not a file, cannot delete.'];
            }
        } elseif ($type === 'folder') {
            if (is_dir($path)) {
                $files_in_dir = array_diff(scandir($path), ['.', '..']);
                if (!empty($files_in_dir)) {
                    disconnect_db($conn);
                    return ['success' => false, 'message' => 'Folder is not empty, cannot delete. Please delete contents first.'];
                }
                $fs_success = rmdir($path);
            } elseif (!file_exists($path)) {
                $fs_success = true;
                error_log("Folder not found on filesystem but present in DB: " . $path . " (ID: " . $fs_id . "). Proceeding with DB deletion.");
            } else {
                error_log("Path exists but is not a directory: " . $path . " (ID: " . $fs_id . ")");
                return ['success' => false, 'message' => 'Path exists but is not a directory, cannot delete.'];
            }
        } else {
            error_log("Unknown type '{$type}' for fs_id {$fs_id}. Assuming file not found for safety.");
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Unknown entry type in database, cannot delete.'];
        }

        if (!$fs_success) {
            disconnect_db($conn);
            error_log("Failed to delete from filesystem: " . $path);
            return ['success' => false, 'message' => 'Failed to delete from filesystem'];
        }

        $stmt_delete = $conn->prepare("DELETE FROM fs WHERE id = ?");
        if (!$stmt_delete) {
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Database prepare failed for deletion'];
        }
        $stmt_delete->bind_param("i", $fs_id);

        if (!$stmt_delete->execute()) {
            $error = $stmt_delete->error;
            error_log("Database delete failed after filesystem deletion for ID {$fs_id}: " . $error);
            disconnect_db($conn, $stmt_delete);
            return ['success' => false, 'message' => 'Database delete failed! A ghost might have just formed...'];
        }

        disconnect_db($conn, $stmt_delete);

        return ['success' => true, 'message' => 'Deleted successfully'];
    }

    public function deleteFolderRecursive(int $fs_id, ?int $owner_id = null): array
    {
        try {
            $conn = connect_db();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        $stmt = null;
        if ($owner_id !== null) {
            $stmt = $conn->prepare("SELECT type, path FROM fs WHERE id = ? AND owner = ?");
            if (!$stmt) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed'];
            }
            $stmt->bind_param("ii", $fs_id, $owner_id);
        } else {
            $stmt = $conn->prepare("SELECT type, path FROM fs WHERE id = ?");
            if (!$stmt) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed'];
            }
            $stmt->bind_param("i", $fs_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            disconnect_db($conn, $stmt);
            return ['success' => false, 'message' => 'Folder not found in database or not owned by the specified user'];
        }

        $row = $result->fetch_assoc();
        $type = $row['type'];
        $folder_path = $row['path'];
        $stmt->close();

        if ($type !== 'folder') {
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Entry is not a folder, use delete() for files.'];
        }

        if (!$this->isPathWithinBasePath($folder_path, true)) {
            error_log("Security alert: Attempted to recursively delete a path outside base directory. Path: " . $folder_path . " (FS ID: " . $fs_id . ")");
            disconnect_db($conn);
            return ['success' => false, 'message' => 'Security violation: cannot delete path outside allowed directory'];
        }

        $like_pattern = $folder_path . '%';
        $stmt_delete_children_db = null;
        if ($owner_id !== null) {
            $stmt_delete_children_db = $conn->prepare("DELETE FROM fs WHERE owner = ? AND path LIKE ?");
            if (!$stmt_delete_children_db) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed for child deletion'];
            }
            $stmt_delete_children_db->bind_param("is", $owner_id, $like_pattern);
        } else {
            $stmt_delete_children_db = $conn->prepare("DELETE FROM fs WHERE path LIKE ?");
            if (!$stmt_delete_children_db) {
                disconnect_db($conn);
                return ['success' => false, 'message' => 'Database prepare failed for child deletion'];
            }
            $stmt_delete_children_db->bind_param("s", $like_pattern);
        }


        if (!$stmt_delete_children_db->execute()) {
            disconnect_db($conn, $stmt_delete_children_db);
            return ['success' => false, 'message' => 'Database deletion of children failed'];
        }
        $stmt_delete_children_db->close();

        $fs_success = false;
        if (is_dir($folder_path)) {
            $fs_success = $this->rrmdir($folder_path);
        } elseif (!file_exists($folder_path)) {
            $fs_success = true;
        }

        if (!$fs_success) {
            disconnect_db($conn);
            error_log("Failed to recursively delete from filesystem: " . $folder_path);
            return ['success' => false, 'message' => 'Failed to recursively delete folder from filesystem'];
        }

        disconnect_db($conn);

        return ['success' => true, 'message' => 'Folder and its contents deleted successfully'];
    }

    private function rrmdir(string $dir): bool
    {
        if (!is_dir($dir)) {
            return true;
        }

        if (!$this->isPathWithinBasePath($dir, true)) {
            error_log("Security alert: Attempted recursive deletion outside base directory: " . $dir);
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                if (!$this->rrmdir($path)) {
                    return false;
                }
            } else {
                if (!unlink($path)) {
                    error_log("Failed to delete file during recursive folder deletion: " . $path);
                    return false;
                }
            }
        }
        return rmdir($dir);
    }

    public function get_fs_size()
    {
        try {
            $conn = connect_db();

            $statement = $conn->prepare("SELECT SUM(size) FROM fs");
            $statement->execute();
            $statement->bind_result($size);
            $statement->fetch();

            return $size;
        } catch (Exception $e) {
            return -1;
        } finally {
            disconnect_db($conn, $statement);
        }
    }

    public function new_file_is_within_boundary(int $new_file_size)
    {
        $fs_size = $this->get_fs_size();

        return $fs_size + $new_file_size <= FS_MAX_SIZE;
    }
}

function getParentPath(
    string $path,
    string $separator = '/',
    bool $return_null_for_root = false
): ?string {
    $path = str_replace(['\\', '/'], $separator, $path);

    if ($path !== $separator && str_ends_with($path, $separator)) {
        $path = rtrim($path, $separator);
    }

    if (empty($path) || $path === $separator) {
        return $return_null_for_root ? null : $path;
    }

    $parent = dirname($path);

    if ($parent === '.') {
        return $return_null_for_root ? null : '';
    } elseif ($parent === $path) {
        return $return_null_for_root ? null : $path;
    }

    return str_replace(['\\', '/'], $separator, $parent);
}


$sizeUnits = ["bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

function formatBytes(int $bytes): string
{
    global $sizeUnits;
    $l = 0;
    $n = $bytes;

    while ($n >= 1024 && ++$l) {
        $n /= 1024;
    }

    return number_format($n, $n < 10 && $l > 0 ? 1 : 0, ".") . " " . $sizeUnits[$l];
}

$fs = new FileSystemManager(FS_BASE_PATH);