<?php

namespace App\Services;

/**
 * Plugin Hook System
 * Provides a way for plugins to hook into various application events
 */
class PluginHookManager
{
    protected array $hooks = [];
    protected array $filters = [];

    /**
     * Register a hook
     */
    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        if (!isset($this->hooks[$hook])) {
            $this->hooks[$hook] = [];
        }

        $this->hooks[$hook][] = [
            'callback' => $callback,
            'priority' => $priority
        ];

        // Sort by priority
        usort($this->hooks[$hook], function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }

    /**
     * Execute hooks
     */
    public function doAction(string $hook, ...$args): void
    {
        if (!isset($this->hooks[$hook])) {
            return;
        }

        foreach ($this->hooks[$hook] as $hookData) {
            call_user_func($hookData['callback'], ...$args);
        }
    }

    /**
     * Register a filter
     */
    public function addFilter(string $filter, callable $callback, int $priority = 10): void
    {
        if (!isset($this->filters[$filter])) {
            $this->filters[$filter] = [];
        }

        $this->filters[$filter][] = [
            'callback' => $callback,
            'priority' => $priority
        ];

        // Sort by priority
        usort($this->filters[$filter], function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }

    /**
     * Apply filters
     */
    public function applyFilters(string $filter, $value, ...$args)
    {
        if (!isset($this->filters[$filter])) {
            return $value;
        }

        foreach ($this->filters[$filter] as $filterData) {
            $value = call_user_func($filterData['callback'], $value, ...$args);
        }

        return $value;
    }

    /**
     * Check if hook exists
     */
    public function hasHook(string $hook): bool
    {
        return isset($this->hooks[$hook]) && !empty($this->hooks[$hook]);
    }

    /**
     * Check if filter exists
     */
    public function hasFilter(string $filter): bool
    {
        return isset($this->filters[$filter]) && !empty($this->filters[$filter]);
    }

    /**
     * Remove a hook
     */
    public function removeAction(string $hook, callable $callback): bool
    {
        if (!isset($this->hooks[$hook])) {
            return false;
        }

        foreach ($this->hooks[$hook] as $key => $hookData) {
            if ($hookData['callback'] === $callback) {
                unset($this->hooks[$hook][$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * Remove a filter
     */
    public function removeFilter(string $filter, callable $callback): bool
    {
        if (!isset($this->filters[$filter])) {
            return false;
        }

        foreach ($this->filters[$filter] as $key => $filterData) {
            if ($filterData['callback'] === $callback) {
                unset($this->filters[$filter][$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * Get all registered hooks
     */
    public function getHooks(): array
    {
        return array_keys($this->hooks);
    }

    /**
     * Get all registered filters
     */
    public function getFilters(): array
    {
        return array_keys($this->filters);
    }
}
