<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FileProcessingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileProcessingServiceTest extends TestCase
{
    protected FileProcessingService $fileProcessingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileProcessingService = new FileProcessingService();
        Storage::fake('public');
    }

    public function test_get_supported_file_types()
    {
        $supportedTypes = $this->fileProcessingService->getSupportedFileTypes();
        
        $this->assertIsArray($supportedTypes);
        $this->assertArrayHasKey('pdf', $supportedTypes);
        $this->assertArrayHasKey('xlsx', $supportedTypes);
        $this->assertArrayHasKey('xls', $supportedTypes);
        $this->assertArrayHasKey('csv', $supportedTypes);
        $this->assertArrayHasKey('txt', $supportedTypes);
    }

    public function test_get_max_file_size()
    {
        $maxSize = $this->fileProcessingService->getMaxFileSize();
        
        $this->assertIsInt($maxSize);
        $this->assertEquals(10, $maxSize); // 10MB
    }

    public function test_process_txt_file()
    {
        $content = "This is a test text file content.\nIt has multiple lines.\nFor testing purposes.";
        
        $file = UploadedFile::fake()->createWithContent(
            'test.txt',
            $content
        );

        $result = $this->fileProcessingService->processFile($file);
        
        $this->assertNotNull($result);
        $this->assertArrayHasKey('file_info', $result);
        $this->assertArrayHasKey('extracted_text', $result);
        $this->assertEquals($content, $result['extracted_text']);
        $this->assertEquals('test.txt', $result['file_info']['original_name']);
        $this->assertEquals('txt', $result['file_info']['extension']);
    }

    public function test_process_multiple_files()
    {
        $files = [
            UploadedFile::fake()->createWithContent('test1.txt', 'Content 1'),
            UploadedFile::fake()->createWithContent('test2.txt', 'Content 2'),
        ];

        $result = $this->fileProcessingService->processFiles($files);
        
        $this->assertArrayHasKey('files', $result);
        $this->assertArrayHasKey('extracted_text', $result);
        $this->assertCount(2, $result['files']);
        $this->assertStringContainsString('Content 1', $result['extracted_text']);
        $this->assertStringContainsString('Content 2', $result['extracted_text']);
    }
}
