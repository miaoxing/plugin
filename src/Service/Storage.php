<?php

namespace Miaoxing\Plugin\Service;

use Wei\Ret;

class Storage extends BaseStorage
{
    /**
     * The file service object
     *
     * @var BaseStorage|null
     */
    protected $object;

    /**
     * The storage service driver
     *
     * @var string
     */
    protected $driver = 'localStorage';

    /**
     * Constructor
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (!$this->object) {
            $this->setDriver($this->driver);
        }
    }

    /**
     * {@inheritdoc}
     * @svc
     */
    protected function write(string $path, string $content, array $options = []): Ret
    {
        return $this->object->write($path, $content, $options);
    }

    /**
     * {@inheritdoc}
     * @svc
     */
    protected function moveLocal(string $path, array $options = []): Ret
    {
        return $this->object->moveLocal($path, $options);
    }

    /**
     * {@inheritdoc}
     * @svc
     */
    protected function getUrl(string $path): string
    {
        return $this->object->getUrl($path);
    }

    /**
     * Get the file driver
     *
     * @svc
     */
    protected function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Set file driver
     *
     * @svc
     */
    protected function setDriver(string $driver): self
    {
        $class = $this->wei->getClass($driver);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Storage driver class "%s" not found', $class));
        }

        $object = $this->wei->get($driver);
        if (!$object instanceof BaseStorage) {
            throw new \InvalidArgumentException(sprintf(
                'Storage driver object "%s" must extend "BaseStorage"',
                $class
            ));
        }

        $this->driver = $driver;
        $this->object = $object;
        return $this;
    }
}
