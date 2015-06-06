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
        $this->validateExceptionTest(['folder_id' => ''], 'The Folder ID field is required.');
    }

    public function testFolderIdExistsValidation()
    {
        $this->validateExceptionTest(['folder_id' => '10'], 'The selected Folder ID is invalid.');
    }

    public function testSourceTypeRequiredValidation()
    {
        $this->validateExceptionTest(['source_type' => ''], 'The Source Type field is required.');
    }

    public function testSourceIdRequiredValidation()
    {
        $this->validateExceptionTest(['source_type' => 's3', 'source_id' => ''], 'The Source ID field is required when Source Type is s3.');
    }

    public function testSourceIdExistsValidation()
    {
        $this->validateExceptionTest(['source_id' => '10'], 'The selected Source ID is invalid.');
    }

    public function testFiledirIdRequiredValidation()
    {
        $this->validateExceptionTest(['filedir_id' => '', 'source_type' => 'ee'], 'The Filedir ID field is required when Source Type is ee.');
    }

    public function testFiledirIdExistsValidation()
    {
        $this->validateExceptionTest(['filedir_id' => '10'], 'The selected Filedir ID is invalid.');
    }

    public function testFileNameRequiredValidation()
    {
        $this->validateExceptionTest(['file_name' => ''], 'The File Name field is required.');
    }

    public function testKindRequiredValidation()
    {
        $this->validateExceptionTest(['kind' => ''], 'The Kind field is required.');
    }

    public function testKindInValidation()
    {
        $this->validateExceptionTest(['kind' => 'foo'], 'The selected Kind is invalid.');
    }

    public function testWidthIntegerValidation()
    {
        $this->validateExceptionTest(['width' => 'foo'], 'The Width must be an integer.');
    }

    public function testHeightIntegerValidation()
    {
        $this->validateExceptionTest(['height' => 'foo'], 'The Height must be an integer.');
    }

    public function testSizeRequiredValidation()
    {
        $this->validateExceptionTest(['size' => ''], 'The Size field is required.');
    }

    public function testSizeIntegerValidation()
    {
        $this->validateExceptionTest(['size' => 'foo'], 'The Size must be an integer.');
    }
}
