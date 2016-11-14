<?php

namespace Miaoxing\Plugin;

trait Service
{
    /**
     * The service provider map
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Invoke a service by the given name
     *
     * @param string $name The name of service
     * @param array $args The arguments for the service's __invoke method
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array($this->$name, $args);
    }

    /**
     * Get a service object by the given name
     *
     * @param  string $name The name of service
     * @return $this
     */
    public function __get($name)
    {
        return $this->$name = $this->wei->get($name, [], $this->providers);
    }
}
