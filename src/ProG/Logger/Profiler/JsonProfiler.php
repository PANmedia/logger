<?php

namespace ProG\Logger\Profiler;

use ProG\Logger\ConsoleInjectionTrait;

class JsonProfiler implements ProfilerInterface
{
    /**
     * Inject the Console object
     */
    use ConsoleInjectionTrait;

    /**
     * @var array
     */
    protected $json;

    /**
     * {@inheritdoc}
     */
    public function appendTo($content)
    {
        if (! is_string($content)) {
            throw new \InvalidArgumentException(
                sprintf('%s expects to receive a string', __METHOD__)
            );
        }

        $json = (array) json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(
                sprintf('%s expects to receive a valid JSON string', __METHOD__)
            );
        }

        $this->json = $json;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $json = [];
        $json['profiler'] = $this->console->getDebugData();

        return json_encode(
            array_merge((array) $this->json, $json)
        );
    }
}
