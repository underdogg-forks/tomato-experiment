<?php

namespace TomatoPHP\FilamentAccounts;

use Illuminate\Contracts\Support\Arrayable;

class FilamentAccountsManager implements Arrayable
{
    /**
     * @param  array<string, mixed>  $configuration
     */
    public function __construct(protected array $configuration = [])
    {
    }

    /**
     * Get the configured features for the inline package.
     *
     * @return array<string, mixed>
     */
    public function features(): array
    {
        return $this->configuration['features'] ?? [];
    }

    /**
     * Retrieve the application specific configuration.
     *
     * @return array<string, mixed>
     */
    public function configuration(): array
    {
        return $this->configuration;
    }

    /**
     * Update runtime configuration for downstream consumers.
     *
     * @param  array<string, mixed>  $configuration
     */
    public function update(array $configuration): void
    {
        $this->configuration = array_merge($this->configuration, $configuration);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->configuration();
    }
}
