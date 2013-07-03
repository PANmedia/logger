<?php

namespace ProG\Logger\Profiler;

use ProG\Logger\ConsoleInjectionTrait;

class PrettyProfiler implements ProfilerInterface
{
    /**
     * Inject the Console object
     */
    use ConsoleInjectionTrait;

    /**
     * @var string
     */
    protected $html;

    /**
     * {@inheritdoc}
     */
    public function appendTo($content)
    {
        if (strrpos($content, '</body>') === false) {
            throw new \InvalidArgumentException(
                sprintf('%s expects to receive a string of valid HTML', __METHOD__)
            );
        }

        $this->html = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render($template = null, $jquery = true)
    {
        $jquery = (boolean) $jquery;
        $template = (is_null($template)) ? __DIR__ . '/../../../../assets/pretty-profiler-template.php' : $template;

        $data = $this->console->getDebugData();

        ob_start();
        include $template;
        $output = ob_get_contents();
        ob_end_clean();

        return (is_null($this->html)) ? $output : str_replace('</body>', $output . '</body>', $this->html);
    }
}
