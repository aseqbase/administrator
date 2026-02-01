<?php namespace MiMFa\Library;

class Storage {
    public readonly string $RootAddress;
    public readonly string $RootUrl;

    public function __construct($rootAddress, $rootUrl) {
        $this->RootAddress = Local::GetAbsoluteAddress($rootAddress);
        $this->RootUrl = Local::GetAbsoluteUrl($rootUrl);
    }

    public function GetAbsoluteUrl(string $path): string {
        return Local::GetAbsoluteUrl($this->GetRelativeUrl( $path));
    }
    public function GetRelativeUrl(string $path): string {
        return Local::GetRelativeUrl($this->RootUrl.normalizeUrl(substr($path, strlen($this->RootAddress))));
    }

    public function GetItems() {
        return Local::GetDirectoryItems($this->RootAddress);
    }

    public function CreateFolder(string $name) {
        return Local::CreateDirectory(rtrim($this->RootAddress . Local::SanitizeName($name), DIRECTORY_SEPARATOR));
    }
    public function CreateFile(string $name) {
        return Local::CreateFile(rtrim($this->RootAddress . Local::SanitizeName($name), DIRECTORY_SEPARATOR));
    }

    public function Upload(string $targetFolder, string $fileName, string $contents, string $mimeType): array {
        $san = $this->SanitizeName($fileName);
        $target = rtrim($this->normalizeDir($targetFolder), '/') . '/' . $san;
        $StoragePath = Local::Store($contents, $target);
        // metadata would be saved to DB; here return blob
        return [
            'name' => $san,
            'path' => $StoragePath,
            'mime_type' => $mimeType,
            'size' => strlen($contents),
            'is_dir' => false,
        ];
    }

    public function Compress(array $itemPaths, string $zipName, string $targetFolder): string {
        // Simple in-app zip using PHP's ZipArchive
        $zip = new \ZipArchive();
        $zipPath = rtrim($this->normalizeDir($targetFolder), '/') . '/' . $this->sanitizeName($zipName) . '.zip';
        // Ensure directory exists
        $zipDir = dirname(Local::GetAbsoluteAddress($zipPath));
        if (!is_dir($zipDir)) mkdir($zipDir, 0777, true);

        $zip->open(Local::GetAbsoluteAddress($zipPath), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($itemPaths as $p) {
            $full = Local::GetAbsoluteAddress($p);
            if (is_dir($full)) {
                $this->addDirToZip($zip, $full, rtrim($p, '/'));
            } else {
                $rel = ltrim($p, '/');
                $zip->addFile($full, $rel);
            }
        }

        $zip->close();
        return $zipPath; // Storage path of the zip
    }

    protected function AddDirToZip(\ZipArchive $zip, string $dir, string $RootInZip): void {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            $path = $file->getPathname();
            if ($file->isDir()) {
                $zip->addEmptyDir(str_replace($dir, $RootInZip, $path));
            } else {
                $zip->addFile($path, str_replace($dir, $RootInZip, $path));
            }
        }
    }

    protected function NormalizeDir(string $dir): string {
        $dir = trim($dir);
        if ($dir === '' || $dir === '/') return '/';
        return '/' . ltrim($dir, '/');
    }

    protected function SanitizeName(string $name): string {
        // Very basic sanitization; adjust per your policy
        $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
        return $name;
    }
}