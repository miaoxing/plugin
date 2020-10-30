<?php

namespace Miaoxing\Plugin\Service;

use Wei\Validator\BaseValidator;

class IsModelExists extends BaseValidator
{
    /**
     * The message added when query return empty result
     *
     * @var string
     */
    protected $notFoundMessage = '%name% not exists';

    /**
     * The message for negative mode
     *
     * @var string
     */
    protected $negativeMessage = '%name% already exists';

    /**
     * The class name of model or model instance
     *
     * @var string|Model
     */
    protected $model;

    /**
     * The column to search
     *
     * @var string
     */
    protected $column = 'id';

    /**
     * Check if the input is existing model
     *
     * @param string $input
     * @param string $model
     * @param string $column
     * @return bool
     */
    public function __invoke($input = null, $model = null, $column = 'id')
    {
        $model && $this->storeOption('model', $model);
        $column && $this->storeOption('column', $column);

        return $this->isValid($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function doValidate($input)
    {
        if (is_string($this->model) && is_subclass_of($this->model, Model::class)) {
            /** @var Model $model */
            $this->model = $this->model::where($this->column, $input);
        } elseif ($this->model instanceof Model) {
            $this->model->where($this->column, $input);
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Expected "model" option to be an existing model class or instance of Model, "%s" given',
                $this->getType($this->model)
            ));
        }

        if (!$this->model->first()) {
            $this->addError('notFound');
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns the type of model
     *
     * @param mixed $model
     * @return false|string
     */
    private function getType($model)
    {
        if (is_object($model)) {
            return get_class($model);
        }
        if (is_string($model)) {
            return $model;
        }
        return gettype($model);
    }
}
