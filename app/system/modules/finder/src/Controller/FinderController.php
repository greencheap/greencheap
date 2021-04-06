<?php

namespace GreenCheap\Finder\Controller;

use GreenCheap\Application as App;
use GreenCheap\Finder\Event\FileAccessEvent;
use GreenCheap\Kernel\Exception\ForbiddenException;
use GreenCheap\Routing\Annotation\Request;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * Class FinderController
 * @package GreenCheap\Finder\Controller
 */
class FinderController
{
    /**
     * @Request({"path"})
     * @param $path
     * @return array
     */
    public function indexAction($path): array
    {
        if (!($dir = $this->getPath())) {
            return $this->error(__("Invalid path."));
        }

        if (!is_dir($dir) || "-" === ($mode = $this->getMode($dir))) {
            throw new ForbiddenException(__("Permission denied."));
        }

        $data = array_fill_keys(["items"], []);
        $data["mode"] = $mode;

        $finder = App::finder();

        $finder->sort(function ($a, $b) {
            return $b->getRealpath() > $a->getRealpath() ? -1 : 1;
        });

        foreach ($finder->depth(0)->in($dir) as $file) {
            if ("-" === ($mode = $this->getMode($file->getPathname()))) {
                continue;
            }

            $info = [
                "name" => $file->getFilename(),
                "mime" => "application/" . ($file->isDir() ? "folder" : "file"),
                "path" => $this->normalizePath($path . "/" . $file->getFilename()),
                "url" => ltrim(App::url()->getStatic($file->getPathname(), [], "base"), "/"),
                "writable" => $mode == "w",
            ];

            if (!$file->isDir()) {
                $info = array_merge($info, [
                    "size" => $this->formatFileSize($file->getSize()),
                    "lastmodified" => date(\DateTime::ATOM, $file->getMTime()),
                ]);
            }

            $data["items"][] = $info;
        }

        return $data;
    }

    /**
     * @Request({"name"})
     * @param $name
     * @return array
     */
    public function createFolderAction($name): array
    {
        if (!$this->isValidFilename($name)) {
            return $this->error(__("Invalid file name."));
        }

        if (!($path = $this->getPath($name))) {
            return $this->error(__("Invalid path."));
        }

        if (file_exists($this->getPath($name))) {
            return $this->error(__("Folder already exists."));
        }

        if ("w" !== $this->getMode(dirname($path))) {
            throw new ForbiddenException(__("Permission denied."));
        }

        try {
            App::file()->makeDir($path);

            return $this->success(__("Directory created."));
        } catch (\Exception $e) {
            return $this->error(__("Unable to create directory."));
        }
    }

    /**
     * @Request({"oldname", "newname"})
     * @param $oldname
     * @param $newname
     * @return array
     */
    public function renameAction($oldname, $newname): array
    {
        if (!$this->isValidFilename($newname)) {
            return $this->error(__("Invalid file name."));
        }

        if (!($source = $this->getPath($oldname)) or !($target = $this->getPath($newname))) {
            return $this->error(__("Invalid path."));
        }

        if ("w" !== $this->getMode($source) || file_exists($target) || "w" !== $this->getMode(dirname($target))) {
            throw new ForbiddenException(__("Permission denied."));
        }

        if (!rename($source, $target)) {
            return $this->error(__("Unable to rename."));
        }

        return $this->success(__("Renamed."));
    }

    /**
     * @Request({"names": "array"})
     * @param $names
     * @return array
     */
    public function removeFilesAction($names): array
    {
        foreach ($names as $name) {
            if (!($path = $this->getPath($name))) {
                return $this->error(__("Invalid path."));
            }

            if ("w" !== $this->getMode($path)) {
                throw new ForbiddenException(__("Permission denied."));
            }

            try {
                App::file()->delete($path);
            } catch (\Exception $e) {
                return $this->error(__("Unable to remove."));
            }
        }

        return $this->success(__("Removed selected."));
    }

    /**
     * @return array
     */
    public function uploadAction(): array
    {
        try {
            if (!($path = $this->getPath())) {
                return $this->error(__("Invalid path."));
            }

            if (!is_dir($path) || "w" !== ($mode = $this->getMode($path))) {
                throw new ForbiddenException(__("Permission denied."));
            }

            $files = App::request()->files->get("files");

            if (!$files) {
                return $this->error(__("No files uploaded."));
            }

            foreach ($files as $file) {
                if (!$file->isValid()) {
                    return $this->error(sprintf(__("Uploaded file invalid. (%s)"), $file->getErrorMessage()));
                }

                if (!$this->isValidFilename($file->getClientOriginalName())) {
                    return $this->error(__("Invalid file name."));
                }

                $file->move($path, $file->getClientOriginalName());
            }

            return $this->success(__("Upload complete."));
        } catch (\Exception $e) {
            return $this->error(__("Unable to upload.") . "<br>" . $e->getMessage());
        }
    }

    /**
     * @param $path
     * @return string
     */
    protected function getMode($path): string
    {
        $mode = App::trigger(new FileAccessEvent("system.finder"))->mode($path);

        if ("w" == $mode && !is_writable($path)) {
            $mode = "r";
        }

        if ("r" == $mode && !is_readable($path)) {
            $mode = "-";
        }

        return $mode;
    }

    /**
     * @param $size
     * @return string
     */
    protected function formatFileSize($size): string
    {
        if ($size == 0) {
            return __("n/a");
        }

        $sizes = [__("%d Bytes"), __("%d  KB"), __("%d  MB"), __("%d  GB"), __("%d TB"), __("%d PB"), __("%d EB"), __("%d ZB"), __("%d YB")];
        $size = round($size / pow(1024, $i = floor(log($size, 1024))), 2);
        return sprintf($sizes[$i], $size);
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected function getPath($path = ""): mixed
    {
        $root = strtr(App::path(), "\\", "/");
        $path = $this->normalizePath($root . "/" . App::request()->get("root") . "/" . App::request()->get("path") . "/" . $path);

        return 0 === strpos($path, $root) ? $path : false;
    }

    /**
     * Normalizes the given path
     *
     * @param string $path
     * @return string
     */
    protected function normalizePath(string $path): string
    {
        $path = str_replace(["\\", "//"], "/", $path);
        $prefix = preg_match("|^(?P<prefix>([a-zA-Z]+:)?//?)|", $path, $matches) ? $matches["prefix"] : "";
        $path = substr($path, strlen($prefix));
        $parts = array_filter(explode("/", $path), "strlen");
        $tokens = [];

        foreach ($parts as $part) {
            if (".." === $part) {
                array_pop($tokens);
            } elseif ("." !== $part) {
                array_push($tokens, $part);
            }
        }

        return $prefix . implode("/", $tokens);
    }

    /**
     * @param $name
     * @return bool
     */
    protected function isValidFilename($name): bool
    {
        if (empty($name)) {
            return false;
        }

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = App::module("system/finder")->config["extensions"];
        if (!empty($extension) && !in_array($extension, explode(",", $allowed))) {
            return false;
        }

        if (defined("PHP_WINDOWS_VERSION_MAJOR")) {
            return !preg_match('#[\\/:"*?<>|]#', $name);
        }

        return !str_contains($name, "/");
    }

    /**
     * @param $message
     * @return array
     */
    #[Pure]
    protected function success($message): array
    {
        return compact("message");
    }

    /**
     * @param $message
     * @return array
     */
    #[ArrayShape(['error' => "bool", 'message' => ""])]
    protected function error($message): array
    {
        return ["error" => true, "message" => $message];
    }
}
