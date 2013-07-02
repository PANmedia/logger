<?php

namespace ProG\Logger\Profiler;

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

        $json['profiler'] = [
            'queries'             => $this->console->getQueries(),
            'timers'              => $this->console->getTimers(),
            'memory_measurements' => $this->console->getMemoryMeasurements()
        ];

        $this->json = $json;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return json_encode($this->json);
    }
}
