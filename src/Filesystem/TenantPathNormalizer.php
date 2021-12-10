<?php
/**
 * Created by PhpStorm.
 * User: loong
 * Date: 12/8/21
 * Time: 3:55 PM
 */

namespace Stancl\Tenancy\Filesystem;


class TenantPathNormalizer implements \League\Flysystem\PathNormalizer
{
    public function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        if( tenancy()->initialized ){
            $separator = '';
            if( substr($path,0,1) != '/' ){
                $separator = '/';
            }
            $path = '/' . config('tenancy.filesystem.suffix_base') . tenancy()->tenant->getTenantKey() . $separator . $path;
        }
        $this->rejectFunkyWhiteSpace($path);

        return $this->normalizeRelativePath($path);
    }

    private function rejectFunkyWhiteSpace(string $path): void
    {
        if (preg_match('#\p{C}+#u', $path)) {
            throw \League\Flysystem\CorruptedPathDetected::forPath($path);
        }
    }

    private function normalizeRelativePath(string $path): string
    {
        $parts = [];

        foreach (explode('/', $path) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;

                case '..':
                    if (empty($parts)) {
                        throw \League\Flysystem\PathTraversalDetected::forPath($path);
                    }
                    array_pop($parts);
                    break;

                default:
                    $parts[] = $part;
                    break;
            }
        }

        return implode('/', $parts);
    }
}