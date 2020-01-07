<?php

declare(strict_types=1);

namespace UniGen\Generator\Resolver;

use UniGen\Generator\Exception\ClassNameNotFoundException;
use UniGen\Generator\Exception\MissingResolverSourceException;
use UniGen\Util\FileReader\FileReaderException;
use UniGen\Util\FileReader\PlainFileReader;

class ClassNameResolver
{
    /**
     * @param string $path
     *
     * @return string
     *
     * @throws ClassNameNotFoundException
     * @throws MissingResolverSourceException
     */
    public function resolveFromFile(string $path): string
    {
        try {
            $content = PlainFileReader::getContent($path);
        } catch (FileReaderException $exception) {
            throw new MissingResolverSourceException(
                sprintf('Unable to load file content "%s".', $path),
                0,
                $exception
            );
        }

        return $this->resolve($content);
    }

    /**
     * @param string $content
     *
     * @return string
     *
     * @throws ClassNameNotFoundException
     */
    public function resolve(string $content): string
    {
        $tokens = token_get_all($content);
        $class = $this->extractClass($tokens);
        if ($class === null) {
            throw new ClassNameNotFoundException('Unable to extract class name.');
        }

        $namespace = $this->extractNamespace($tokens);

        return $namespace === null
            ? $class
            : $namespace . '\\' . $class;
    }

    /**
     * @param array[] $tokens
     *
     * @return string|null
     */
    private function extractClass(array $tokens): ?string
    {
        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        return $tokens[$i + 2][1];
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param array[] $tokens
     *
     * @return string|null
     */
    private function extractNamespace(array $tokens): ?string
    {
        $namespace = '';
        for ($i = 0; $i < count($tokens); $i++) {
            $tokenType = $tokens[$i][0];

            if ($tokenType === T_NAMESPACE) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }
        }

        return $namespace === '' ? null : $namespace;
    }
}
