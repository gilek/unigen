<?php

declare(strict_types=1);

namespace UniGen\Renderer\Twig;

use Twig\Environment;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;
use UniGen\Config\Config;
use UniGen\Config\Exception\ConfigException;
use UniGen\Renderer\Exception\RendererException;
use UniGen\Renderer\RendererInterface;
use UniGen\Sut\SutInterface;

class TwigRenderer implements RendererInterface
{
    const SUT = 'sut';
    const CONFIG = 'config';

    /** @var Environment */
    private $twig;

    /** @var Config */
    private $config;

    /**
     * @param Environment $twig
     * @param Config           $config
     */
    public function __construct(Environment $twig, Config $config)
    {
        $this->twig = $twig;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function render(SutInterface $sut): string
    {
        try {
            $this->applyTemplatePath();
        } catch (LoaderError | ConfigException $exception) {
            throw new RendererException('An error occurred while set up renderer.', 0, $exception);
        }

        try {
            $content = $this->twig->render(
                basename($this->config->get('template')),
                [
                    self::SUT => $sut,
                    self::CONFIG => $this->config
                ]
            );
        } catch (Error | ConfigException $exception) {
            throw new RendererException('SUT render failed.', 0 , $exception);
        }

        return $content;
    }

    /**
     * @throws LoaderError
     * @throws ConfigException
     */
    private function applyTemplatePath(): void
    {
        /** @var FilesystemLoader $loader */
        $loader = $this->twig->getLoader();
        $loader->addPath(dirname($this->config->get('template')));
    }
}