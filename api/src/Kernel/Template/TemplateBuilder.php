<?php

declare(strict_types=1);

namespace Camagru\Kernel\Template;

use Camagru\Kernel\Exception\FileException;
use Camagru\Kernel\Exception\FileNotFoundException;
use Camagru\Kernel\Exception\FileExtensionNotCorrectException;
use Camagru\Kernel\Exception\RenderException;

class TemplateBuilder
{
    private string $path;

    private string $suffix;

    private string $extension;

    private static $instance;

    private string $template;

    private string $section;

    private string $layout;

    private array $sections = [];

    private array $variables = [];

    public function __construct()
    {
        global $config;
        [
            'path' => $this->path,
            'suffix' => $this->suffix,
            'extension' => $this->extension,
        ] = $config['views'];

        if (!($this->path)) {
            throw new \RuntimeException($this->path.' not found.');
        }

        if (!is_dir($this->path)) {
            mkdir($this->path, 0755);
        }
    }


    public static function build(): static
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function template(string $templateName): static
    {
        $ext = pathinfo($templateName, PATHINFO_EXTENSION);
        if (!$ext) {
            $templateName .= '.'.$this->suffix;
        }

        $this->template = $this->path.DIRECTORY_SEPARATOR.$templateName;

        return $this;
    }

    public function variables(array $variables): static
    {
        $this->variables = $variables;

        return $this;
    }

    private function sections(array $sections): self
    {
        $this->sections = $sections;

        return $this;
    }

    public function validate(): static
    {
        if (!$this->template) {
            throw new FileException('View not found.');
        }

        if (!file_exists($this->template) || !is_file($this->template)) {
            throw new FileNotFoundException($this->template);
        }

        if ($this->extension !== pathinfo($this->template, PATHINFO_EXTENSION)) {
            throw new FileExtensionNotCorrectException($this->template);    
        }

        return $this;
    }

    /**
     * @throws RenderException
     */
    public function parse(): string
    {
        try {
            $level = ob_get_level();

            ob_start();
            (function() {
                include $this->template;
            })();
            $content = ob_get_clean();

            if (isset($this->layout)) {
                return (new self())
                    ->template($this->layout)
                    ->variables($this->variables)
                    ->sections($this->sections)
                    ->validate()
                    ->parse();
            }

            return $content;
        } catch (\Throwable $exception) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw new RenderException('Render error. '.$exception->getMessage());
        }
    }

    private function include(string $name): void
    {
        include_once $this->path.DIRECTORY_SEPARATOR.$name;
    }

    private function assets(string $name): string
    {
        return '/public'.DIRECTORY_SEPARATOR.$name;
    }

    private function v(string $name)
    {
        if (!isset($this->variables[$name])) {
            return null;
        }

        return $this->variables[$name];
    }

    private function e($string): string
    {
        static $flags;

        if (!isset($flags)) {
            $flags = ENT_QUOTES | (defined('ENT_SUBSTITUTE') ? ENT_SUBSTITUTE : 0);
        }

        return htmlspecialchars((string)$string ?? '', $flags, 'UTF-8');
    }

    private function section(string $name): void
    {
        $this->section = $name;
        ob_start();
    }

    private function endsection(): void
    {
        $this->sections[$this->section] = ob_get_clean();
    }

    private function yield(string $name): void
    {
        echo $this->sections[$name] ?? '';
    }

    private function extends(string $template): void
    {
        $this->layout = $template;
    }
}
