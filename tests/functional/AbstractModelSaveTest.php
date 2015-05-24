<?php

use rsanchez\Deep\Model\Model;
use Symfony\Component\Translation\Translator;
use rsanchez\Deep\Validation\Factory as ValidatorFactory;
use rsanchez\Deep\Validation\DatabasePresenceVerifier;

abstract class AbstractModelSaveTest extends PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $validationTranslator = new Translator('en');

        $validatorFactory = new ValidatorFactory($validationTranslator);

        $validationPresenceVerifier =  new DatabasePresenceVerifier(Model::getConnectionResolver());

        $validatorFactory->setPresenceVerifier($validationPresenceVerifier);

        Model::setValidatorFactory($validatorFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        Model::unsetValidatorFactory();
    }

    /**
     * Get the class name of the model being tested
     * @return string
     */
    protected function getModelClass()
    {
        // derive the class name from the test class name
        $reflectionClass = new \ReflectionClass($this);

        $shortName = $reflectionClass->getShortName();

        $modelClass = '\\rsanchez\\Deep\\Model\\' . preg_replace('/ModelSaveTest$/', '', $shortName);

        return $modelClass;
    }

    /**
     * Get the default attributes when creating a new model instance
     * @return array  attr => value
     */
    abstract protected function getModelAttributes();

    /**
     * Create a new model instance
     * @return \rsanchez\Deep\Model\Model
     */
    protected function createModel()
    {
        $class = $this->getModelClass();

        $attributes = $this->getModelAttributes();

        return new $class($attributes);
    }

    /**
     * Find a instance of the model by id
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getModelInstance($id)
    {
        $class = $this->getModelClass();

        return call_user_func([$class, 'find'], $id);
    }

    /**
     * Test the model save method
     * @return void
     */
    public function testSave()
    {
        $class = $this->getModelClass();

        $model = $this->createModel();

        $model->save();

        $id = $model->getKey();

        // if the save was successful, you should be able to find new record in DB
        $model = $this->getModelInstance($id);

        $this->assertInstanceOf($class, $model);

        $this->modelAttributesTest($model);

        unset($model);
    }

    /**
     * Assert that the saved model contains all the attributes assigned to it
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected function modelAttributesTest($model)
    {
        foreach ($this->getModelAttributes() as $key => $value) {
            $this->assertEquals($model->$key, $value);
        }
    }

    /**
     * Test model validation by supplying model attributes that will trigger validation
     * errors with the specified messages.
     *
     * @param  array        $attributes key/value pairs of attributes to set on the model
     * @param  string|array $messages one or more validation messages expected
     * @return void
     */
    protected function validateExceptionTest($attributes, $messages)
    {
        $message = implode(PHP_EOL, (array) $messages);

        $this->setExpectedException('rsanchez\Deep\Exception\ValidationException', $message);

        $model = $this->createModel();

        foreach ($attributes as $attribute => $value) {
            $model->$attribute = $value;
        }

        $model->validateOrFail();

        unset($model);
    }
}
