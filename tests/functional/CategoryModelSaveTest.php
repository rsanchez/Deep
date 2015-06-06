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
        $this->validateExceptionTest(['site_id' => ''], 'The Site ID field is required.');
    }

    public function testSiteIdExistsValidation()
    {
        $this->validateExceptionTest(['site_id' => '10'], 'The selected Site ID is invalid.');
    }

    public function testGroupIdRequiredValidation()
    {
        $this->validateExceptionTest(['group_id' => ''], 'The Group ID field is required.');
    }

    public function testGroupIdExistsValidation()
    {
        $this->validateExceptionTest(['group_id' => '10'], 'The selected Group ID is invalid.');
    }

    public function testParentIdExistsValidation()
    {
        $this->validateExceptionTest(['parent_id' => '10'], 'The selected Parent ID is invalid.');
    }

    public function testCatNameRequiredValidation()
    {
        $this->validateExceptionTest(['cat_name' => ''], 'The Category Name field is required.');
    }

    public function testCatUrlTitleRequiredValidation()
    {
        $this->validateExceptionTest(['cat_url_title' => ''], 'The Category URL Title field is required.');
    }

    public function testCatUrlTitleAlphaDashValidation()
    {
        $this->validateExceptionTest(['cat_url_title' => 'Foo bar'], 'The Category URL Title may only contain letters, numbers, and dashes.');
    }

    public function testCatUrlTitleUniqueValidation()
    {
        $this->validateExceptionTest(['cat_url_title' => 'category-a'], 'The Category URL Title has already been taken.');
    }

    public function testCatOrderRequiredValidation()
    {
        $this->validateExceptionTest(['cat_order' => ''], 'The Category Order field is required.');
    }

    public function testCatOrderIntegerValidation()
    {
        $this->validateExceptionTest(['cat_order' => 'integer'], 'The Category Order must be an integer.');
    }

    public function testCatColorRequiredValidation()
    {
        $this->validateExceptionTest(['cat_color' => ''], 'The Color field is required.');
    }

    public function testCatColorInValidation()
    {
        $this->validateExceptionTest(['cat_color' => 'Fuchsia'], 'The selected Color is invalid.');
    }
}
