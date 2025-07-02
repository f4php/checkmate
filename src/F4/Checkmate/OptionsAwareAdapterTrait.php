<?php
declare(strict_types=1);

namespace F4\Checkmate;

trait OptionsAwareAdapterTrait
{
    protected array $options = [];
    public function getOption(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }
    public function setOption(string $name, mixed $value): mixed
    {
        return $this->options[$name] = $value;
    }
    public function withOption(string $name, mixed $value): static
    {
        $this->options[$name] = $value;
        return $this;
    }
    public function withOptions(array $options): static
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
        return $this;
    }
}
