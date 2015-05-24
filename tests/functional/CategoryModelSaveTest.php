<?php

use rsanchez\Deep\Model\Category;
use rsanchez\Deep\Model\CategoryField;
use rsanchez\Deep\Repository\CategoryFieldRepository;

class CategoryModelSaveTest extends AbstractModelSaveTest
{
    protected function getModelAttributes()
    {
        return [
            'site_id' => '1',
            'group_id' => '1',
            'parent_id' => '0',
            'cat_name' => 'Category D',
            'cat_url_title' => 'category-d',
            'cat_description' => 'My category description',
            'cat_image' => '{filedir_1}1eecbed0063a0253.jpg',
            'cat_order' => '4',
            'cat_color' => 'Red',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInstance($id)
    {
        $class = $this->getModelClass();

        return call_user_func([$class, 'withFields'])->find($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $categoryField = new CategoryField();

        $categoryFieldRepository = new CategoryFieldRepository($categoryField);

        Category::setCategoryFieldRepository($categoryFieldRepository);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        Category::unsetCategoryFieldRepository();
    }

    public function testSiteIdRequiredValidation()
    {
        $this->validateExceptionTest(['site_id' => ''], 'The site id field is required.');
    }

    public function testSiteIdExistsValidation()
    {
        $this->validateExceptionTest(['site_id' => '10'], 'The selected site id is invalid.');
    }

    public function testGroupIdRequiredValidation()
    {
        $this->validateExceptionTest(['group_id' => ''], 'The group id field is required.');
    }

    public function testGroupIdExistsValidation()
    {
        $this->validateExceptionTest(['group_id' => '10'], 'The selected group id is invalid.');
    }

    public function testParentIdExistsValidation()
    {
        $this->validateExceptionTest(['parent_id' => '10'], 'The selected parent id is invalid.');
    }

    public function testCatNameRequiredValidation()
    {
        $this->validateExceptionTest(['cat_name' => ''], 'The cat name field is required.');
    }

    public function testCatUrlTitleRequiredValidation()
    {
        $this->validateExceptionTest(['cat_url_title' => ''], 'The cat url title field is required.');
    }

    public function testCatUrlTitleAlphaDashValidation()
    {
        $this->validateExceptionTest(['cat_url_title' => 'Foo bar'], 'The cat url title may only contain letters, numbers, and dashes.');
    }

    public function testCatUrlTitleUniqueValidation()
    {
        $this->validateExceptionTest(['cat_url_title' => 'category-a'], 'The cat url title has already been taken.');
    }

    public function testCatOrderRequiredValidation()
    {
        $this->validateExceptionTest(['cat_order' => ''], 'The cat order field is required.');
    }

    public function testCatOrderIntegerValidation()
    {
        $this->validateExceptionTest(['cat_order' => 'integer'], 'The cat order must be an integer.');
    }

    public function testCatColorRequiredValidation()
    {
        $this->validateExceptionTest(['cat_color' => ''], 'The cat color field is required.');
    }

    public function testCatColorInValidation()
    {
        $this->validateExceptionTest(['cat_color' => 'Fuchsia'], 'The selected cat color is invalid.');
    }
}
