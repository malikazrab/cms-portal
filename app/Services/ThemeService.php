<?php

namespace App\Services;

use App\Models\Theme;
use Illuminate\Http\UploadedFile;
use ZipArchive;

class ThemeService
{
    public function installFromZip(UploadedFile $file): array
    {
        if ($file->getClientOriginalExtension() !== 'zip') {
            return ['success' => false, 'message' => 'Only .zip files are allowed.'];
        }

        $themeName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $themeSlug = $this->generateSlug($themeName);
        $themePath = public_path('themes/' . $themeSlug);

        if (is_dir($themePath)) {
            return ['success' => false, 'message' => 'A theme with this name already exists.'];
        }

        $zip = new ZipArchive();
        if ($zip->open($file->getPathname()) !== true) {
            return ['success' => false, 'message' => 'Unable to open ZIP file.'];
        }

        mkdir($themePath, 0755, true);
        $zip->extractTo($themePath);
        $zip->close();

        $this->convertToStandardFormat($themePath, $themeSlug, $themeName);
        $themeJson = $this->getOrCreateThemeJson($themePath, $themeSlug, $themeName);

        $theme = Theme::create([
            'name' => $themeJson['name'] ?? $themeName,
            'slug' => $themeSlug,
            'version' => $themeJson['version'] ?? '1.0.0',
            'author' => $themeJson['author'] ?? 'Unknown',
            'description' => $themeJson['description'] ?? '',
            'screenshot' => 'themes/' . $themeSlug . '/' . ($themeJson['screenshot'] ?? 'screenshot.png'),
            'settings' => $themeJson['settings'] ?? [],
            'is_active' => false,
            'is_builtin' => false,
            'theme_path' => 'themes/' . $themeSlug,
        ]);

        return ['success' => true, 'message' => 'Theme installed successfully!', 'theme' => $theme];
    }

    private function convertToStandardFormat(string $themePath, string $slug, string $name): void
    {
        foreach (['templates', 'assets', 'assets/css', 'assets/js'] as $folder) {
            $folderPath = $themePath . '/' . $folder;
            if (!is_dir($folderPath)) mkdir($folderPath, 0755, true);
        }

        $allFiles = $this->findAllFiles($themePath, 'html');
        $indexFile = $this->findFile($allFiles, ['index', 'home']);

        if ($indexFile && file_exists($indexFile)) {
            $content = file_get_contents($indexFile);
            $headerContent = $this->extractHeader($content);
            $footerContent = $this->extractFooter($content);
            $mainContent = $this->extractMainContent($content);

            file_put_contents($themePath . '/templates/header.blade.php', $this->htmlToBlade($headerContent, $slug));
            file_put_contents($themePath . '/templates/footer.blade.php', $this->htmlToBlade($footerContent, $slug));
            file_put_contents($themePath . '/templates/home.blade.php', $this->htmlToBlade($mainContent, $slug));
        }

        foreach ($allFiles as $file) {
            $filename = basename($file, '.html');
            if (in_array($filename, ['index', 'home'])) continue;
            $content = file_get_contents($file);
            $pageContent = $this->extractMainContent($content);
            $templateName = $this->mapToTemplate($filename);
            file_put_contents($themePath . '/templates/' . $templateName . '.blade.php', $this->htmlToBlade($pageContent, $slug));
        }

        $allCss = array_merge(glob($themePath . '/*.css'), $this->findAllFiles($themePath, 'css'));
        foreach ($allCss as $cssFile) {
            $cssName = basename($cssFile);
            copy($cssFile, $themePath . '/assets/css/' . $cssName);
        }

        $allJs = array_merge(glob($themePath . '/*.js'), $this->findAllFiles($themePath, 'js'));
        foreach ($allJs as $jsFile) {
            $jsName = basename($jsFile);
            copy($jsFile, $themePath . '/assets/js/' . $jsName);
        }

        $this->copyFolder($themePath, 'images', $themePath . '/assets/images');
        $this->copyFolder($themePath, 'img', $themePath . '/assets/images');
        $this->copyFolder($themePath, 'fonts', $themePath . '/assets/fonts');
    }

    private function findAllFiles(string $path, string $extension): array
    {
        $files = glob($path . '/*.' . $extension);
        $subDirs = glob($path . '/*', GLOB_ONLYDIR);
        foreach ($subDirs as $dir) {
            $files = array_merge($files, $this->findAllFiles($dir, $extension));
        }
        return $files;
    }

    private function findFile(array $files, array $names): ?string
    {
        foreach ($files as $file) {
            $basename = strtolower(basename($file, '.html'));
            foreach ($names as $name) {
                if ($basename === strtolower($name)) return $file;
            }
        }
        return $files[0] ?? null;
    }

    private function extractHeader(string $html): string
    {
        if (preg_match('/<header[^>]*>(.*?)<\/header>/si', $html, $m)) return $m[0];
        if (preg_match('/<nav[^>]*>(.*?)<\/nav>/si', $html, $m)) return '<header>' . $m[0] . '</header>';
        $patterns = ['/<main[^>]*>/i', '/<section[^>]*>/i'];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m, PREG_OFFSET_CAPTURE)) {
                $before = substr($html, 0, $m[0][1]);
                if (strlen(trim(strip_tags($before))) > 10) return $before;
            }
        }
        return substr($html, 0, strlen($html) * 0.2);
    }

    private function extractFooter(string $html): string
    {
        if (preg_match('/<footer[^>]*>(.*?)<\/footer>/si', $html, $m)) return $m[0];
        $sections = preg_split('/<section[^>]*>/i', $html);
        if (count($sections) > 1) {
            $last = end($sections);
            if (strlen(trim(strip_tags($last))) > 10) return '<footer>' . $last . '</footer>';
        }
        return substr($html, strlen($html) * 0.85);
    }

    private function extractMainContent(string $html): string
    {
        $html = preg_replace('/<header[^>]*>.*?<\/header>/si', '', $html);
        $html = preg_replace('/<footer[^>]*>.*?<\/footer>/si', '', $html);
        $html = preg_replace('/<nav[^>]*>.*?<\/nav>/si', '', $html);
        return trim($html);
    }

    private function htmlToBlade(string $html, string $slug): string
    {
        return $html;
    }

    private function mapToTemplate(string $filename): string
    {
        $map = ['about' => 'page', 'contact' => 'page', 'services' => 'page', 'blog' => 'blog', 'shop' => 'page', 'faq' => 'page', 'project' => 'page', 'team' => 'page'];
        $filename = strtolower($filename);
        foreach ($map as $key => $template) {
            if (strpos($filename, $key) !== false) return $template;
        }
        return 'page';
    }

    private function copyFolder(string $basePath, string $folderName, string $destination): void
    {
        $source = $basePath . '/' . $folderName;
        if (is_dir($source)) {
            if (!is_dir($destination)) mkdir($destination, 0755, true);
            $this->recursiveCopy($source, $destination);
            return;
        }
        $found = glob($basePath . '/**/' . $folderName, GLOB_ONLYDIR);
        if (!empty($found) && is_dir($found[0])) {
            if (!is_dir($destination)) mkdir($destination, 0755, true);
            $this->recursiveCopy($found[0], $destination);
        }
    }

    private function recursiveCopy(string $source, string $dest): void
    {
        $dir = opendir($source);
        if (!is_dir($dest)) mkdir($dest, 0755, true);
        while (($file = readdir($dir)) !== false) {
            if ($file == '.' || $file == '..') continue;
            $srcFile = $source . '/' . $file;
            $dstFile = $dest . '/' . $file;
            if (is_dir($srcFile)) $this->recursiveCopy($srcFile, $dstFile);
            else copy($srcFile, $dstFile);
        }
        closedir($dir);
    }

    private function getOrCreateThemeJson(string $themePath, string $slug, string $name): array
    {
        $jsonPath = $themePath . '/theme.json';
        if (file_exists($jsonPath)) {
            return json_decode(file_get_contents($jsonPath), true) ?? [];
        }
        $themeJson = [
            'name' => $name, 'slug' => $slug, 'version' => '1.0.0', 'author' => 'Unknown',
            'description' => 'Imported theme', 'screenshot' => 'screenshot.png',
            'settings' => [
                'colors' => ['primary' => '#0ea5e9', 'secondary' => '#8b5cf6', 'accent' => '#f59e0b', 'background' => '#ffffff', 'text' => '#1e293b'],
                'fonts' => ['heading' => 'Inter, sans-serif', 'body' => 'Inter, sans-serif'],
                'layout' => ['container' => 'full-width', 'sidebar' => 'right'],
            ],
            'templates' => ['home' => 'templates/home.blade.php', 'page' => 'templates/page.blade.php', 'blog' => 'templates/blog.blade.php', 'header' => 'templates/header.blade.php', 'footer' => 'templates/footer.blade.php'],
        ];
        file_put_contents($jsonPath, json_encode($themeJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $themeJson;
    }

    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        return $slug ?: 'theme-' . uniqid();
    }

    public function scanThemesFolder(): array
    {
        $themesPath = public_path('themes');
        $folders = glob($themesPath . '/*', GLOB_ONLYDIR);
        $installed = [];
        foreach ($folders as $folder) {
            $slug = basename($folder);
            $jsonPath = $folder . '/theme.json';
            if (file_exists($jsonPath)) {
                $json = json_decode(file_get_contents($jsonPath), true);
                $theme = Theme::updateOrCreate(['slug' => $slug], [
                    'name' => $json['name'] ?? $slug, 'version' => $json['version'] ?? '1.0.0',
                    'author' => $json['author'] ?? 'Unknown', 'description' => $json['description'] ?? '',
                    'screenshot' => 'themes/' . $slug . '/' . ($json['screenshot'] ?? 'screenshot.png'),
                    'settings' => $json['settings'] ?? [], 'theme_path' => 'themes/' . $slug,
                    'is_builtin' => in_array($slug, ['default']),
                ]);
                $installed[] = $theme;
            }
        }
        return $installed;
    }
}