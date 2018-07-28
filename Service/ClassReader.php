<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Service;

/**
 * Class ClassReader.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ClassReader
{
    public function read(string $filename): array
    {
        $content = file_get_contents($filename);

        $classes = [];
        $tokens = token_get_all($content);
        $count = count($tokens);
        $namespace = '';

        for ($i = 0; $i < $count; ++$i) {
            if (T_NAMESPACE === $tokens[$i][0]) {
                $namespace = '';
                for ($j = $i + 1; $j < $count; ++$j) {
                    if (T_STRING === $tokens[$j][0]) {
                        $namespace .= $tokens[$j][1];
                    } elseif ('{' === $tokens[$j] || ';' === $tokens[$j]) {
                        break;
                    }
                }
            }
            if (T_CLASS === $tokens[$i][0]) {
                for ($j = $i + 1; $j < $count; ++$j) {
                    if ('{' === $tokens[$j]) {
                        $classes[] = trim($namespace.'\\'.$tokens[$i + 2][1], "\\");
                        break;
                    }
                }
            }
        }

        return $classes;
    }
}
