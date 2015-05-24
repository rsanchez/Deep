<?php

use rsanchez\Deep\Model\Asset;
use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Repository\UploadPrefRepository;

class AssetModelSaveTest extends AbstractModelSaveTest
{
    /**
     * {@inheritdoc}
     */
    protected function getModelAttributes()
    {
        return [
            'folder_id' => 1,
            'source_type' => 'ee',
            'filedir_id' => 1,
            'file_name' => 'foo.jpg',
            'kind' => 'image',
            'width' => 300,
            'height' => 300,
            'size' => 51200,
            'search_keywords' => 'foo.jpg',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function createModel()
    {
        $model = parent::createModel();

        $uploadPrefRepository = new UploadPrefRepository(new UploadPref());

        $model->setUploadPref($uploadPrefRepository->find($model->filedir_id));

        return $model;
    }

    public function testFolderIdRequiredValidation()
    {
        $this->validateExceptionTest(['folder_id' => ''], 'The folder id field is required.');
    }

    public function testFolderIdExistsValidation()
    {
        $this->validateExceptionTest(['folder_id' => '10'], 'The selected folder id is invalid.');
    }

    public function testSourceTypeRequiredValidation()
    {
        $this->validateExceptionTest(['source_type' => ''], 'The source type field is required.');
    }

    public function testSourceIdRequiredValidation()
    {
        $this->validateExceptionTest(['source_type' => 's3', 'source_id' => ''], 'The source id field is required when source type is s3.');
    }

    public function testSourceIdExistsValidation()
    {
        $this->validateExceptionTest(['source_id' => '10'], 'The selected source id is invalid.');
    }

    public function testFiledirIdRequiredValidation()
    {
        $this->validateExceptionTest(['filedir_id' => '', 'source_type' => 'ee'], 'The filedir id field is required when source type is ee.');
    }

    public function testFiledirIdExistsValidation()
    {
        $this->validateExceptionTest(['filedir_id' => '10'], 'The selected filedir id is invalid.');
    }

    public function testFileNameRequiredValidation()
    {
        $this->validateExceptionTest(['file_name' => ''], 'The file name field is required.');
    }

    public function testKindRequiredValidation()
    {
        $this->validateExceptionTest(['kind' => ''], 'The kind field is required.');
    }

    public function testKindInValidation()
    {
        $this->validateExceptionTest(['kind' => 'foo'], 'The selected kind is invalid.');
    }

    public function testWidthIntegerValidation()
    {
        $this->validateExceptionTest(['width' => 'foo'], 'The width must be an integer.');
    }

    public function testHeightIntegerValidation()
    {
        $this->validateExceptionTest(['height' => 'foo'], 'The height must be an integer.');
    }

    public function testSizeRequiredValidation()
    {
        $this->validateExceptionTest(['size' => ''], 'The size field is required.');
    }

    public function testSizeIntegerValidation()
    {
        $this->validateExceptionTest(['size' => 'foo'], 'The size must be an integer.');
    }
}
