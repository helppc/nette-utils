<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\DirectoryNotFoundException;
use Nette\SmartObject;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

class Tempnam
{
    use SmartObject;

    public static float $gcProbability = 0.001;

    private string $tempDir;

    private string $namespace;

    private Cache $cache;

    public function __construct(string $tempDir, IStorage $storage, string $namespace = '_')
    {
        if (!is_dir($tempDir)) {
            throw new DirectoryNotFoundException(sprintf('Directory %s not found.', $tempDir));
        }

        $this->tempDir = $tempDir;

        $this->namespace = $namespace;
        $this->cache = new Cache($storage, strtr(__CLASS__, ['\\' => '.']));

        if (mt_rand() / mt_getrandmax() < static::$gcProbability) {
            $this->clean();
        }
    }

    public function getTempDir(): string
    {
        return $this->tempDir;
    }

    private function joinPaths(string $path, string $path2): string
    {
        if (!Strings::endsWith($path, '/')) {
            $path = $path . '/';
        }

        if (Strings::startsWith($path2, '/')) {
            $path2 = ltrim($path2, '/');
        }

        return $path . $path2;
    }

    /**
     * @param mixed $key
     * @phpstan-param mixed $key
     * @return string
     */
    private function generateKey($key): string
    {
        return $this->namespace . md5(is_scalar($key) ? (string) $key : serialize($key));
    }

    private function getFilePath(string $filename): string
    {
        return $this->joinPaths($this->tempDir, $filename);
    }

    private function putFile(string $filename, string $data): string
    {
        $path = $this->getFilePath($filename);
        file_put_contents($path, $data);
        return $path;
    }

    public function remove(string $key): void
    {
        $keyGen = $this->generateKey($key);
        $this->cache->remove($keyGen);
        @unlink($this->getFilePath($keyGen));
    }

    public function load(string $key, \DateTimeInterface $updatedAt = null): ?string
    {
        $keyGen = $this->generateKey($key);
        $updateDate = $this->cache->load($keyGen);

        if ($updateDate === null || $updateDate != $updatedAt) {
            if ($updateDate) {
                $this->remove($key);
            }
            return null;
        }

        return $this->getFilePath($keyGen);
    }

    public function save(string $key, string $data, \DateTimeInterface $updatedAt = null): string
    {
        $keyGen = $this->generateKey($key);
        $path = $this->putFile($keyGen, $data);
        $this->cache->save($keyGen, $updatedAt);
        return $path;
    }

    private function clean(): void
    {
        foreach (Finder::find($this->namespace . '*')->from($this->tempDir)->childFirst() as $entry) {
            $path = (string) $entry;
            if ($entry->isDir()) {
                //We dont use dirs, ignore
                continue;
            }

            $updateDate = $this->cache->load($entry->getFilename());
            if ($updateDate === null) {
                @unlink($path);
            }
        }

        return;
    }
}