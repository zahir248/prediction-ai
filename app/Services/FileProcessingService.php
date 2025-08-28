<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Str;

class FileProcessingService
{
    protected $pdfParser;

    public function __construct()
    {
        $this->pdfParser = new Parser();
    }

    /**
     * Process uploaded files and extract text content
     */
    public function processFiles(array $files): array
    {
        $processedFiles = [];
        $extractedTexts = [];

        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $result = $this->processFile($file);
                if ($result) {
                    $processedFiles[] = $result['file_info'];
                    $extractedTexts[] = $result['extracted_text'];
                }
            }
        }

        return [
            'files' => $processedFiles,
            'extracted_text' => implode("\n\n--- File Separator ---\n\n", $extractedTexts)
        ];
    }

    /**
     * Process a single file
     */
    public function processFile(UploadedFile $file): ?array
    {
        $fileName = $file->getClientOriginalName();
        $fileExtension = strtolower($file->getClientOriginalExtension());
        
        // Store the file
        $storedPath = $file->store('predictions', 'public');
        
        $fileInfo = [
            'original_name' => $fileName,
            'stored_path' => $storedPath,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $fileExtension
        ];

        // Extract text based on file type
        $extractedText = '';
        
        try {
            switch ($fileExtension) {
                case 'pdf':
                    $extractedText = $this->extractTextFromPdf($file);
                    break;
                    
                case 'xlsx':
                case 'xls':
                    $extractedText = $this->extractTextFromExcel($file);
                    break;
                    
                case 'txt':
                    $extractedText = $this->extractTextFromTxt($file);
                    break;
                    
                case 'csv':
                    $extractedText = $this->extractTextFromCsv($file);
                    break;
                    
                default:
                    $extractedText = "File type not supported for text extraction: {$fileExtension}";
                    break;
            }
        } catch (\Exception $e) {
            $extractedText = "Error processing file: " . $e->getMessage();
        }

        return [
            'file_info' => $fileInfo,
            'extracted_text' => $extractedText
        ];
    }

    /**
     * Extract text from PDF file
     */
    protected function extractTextFromPdf(UploadedFile $file): string
    {
        try {
            $pdf = $this->pdfParser->parseFile($file->path());
            return $pdf->getText();
        } catch (\Exception $e) {
            return "Error extracting text from PDF file: " . $e->getMessage();
        }
    }

    /**
     * Extract text from Excel file
     */
    protected function extractTextFromExcel(UploadedFile $file): string
    {
        try {
            $data = Excel::toArray([], $file);
            $extractedText = [];
            
            foreach ($data as $sheetIndex => $sheet) {
                $extractedText[] = "=== Sheet " . ($sheetIndex + 1) . " ===";
                
                foreach ($sheet as $rowIndex => $row) {
                    if (is_array($row)) {
                        $rowText = array_filter($row, function($cell) {
                            return !empty($cell) && is_string($cell);
                        });
                        
                        if (!empty($rowText)) {
                            $extractedText[] = "Row " . ($rowIndex + 1) . ": " . implode(" | ", $rowText);
                        }
                    }
                }
            }
            
            return implode("\n", $extractedText);
        } catch (\Exception $e) {
            return "Error extracting text from Excel file: " . $e->getMessage();
        }
    }

    /**
     * Extract text from TXT file
     */
    protected function extractTextFromTxt(UploadedFile $file): string
    {
        return $file->get();
    }

    /**
     * Extract text from CSV file
     */
    protected function extractTextFromCsv(UploadedFile $file): string
    {
        try {
            $data = Excel::toArray([], $file);
            $extractedText = [];
            
            foreach ($data as $sheet) {
                foreach ($sheet as $rowIndex => $row) {
                    if (is_array($row)) {
                        $rowText = array_filter($row, function($cell) {
                            return !empty($cell) && is_string($cell);
                        });
                        
                        if (!empty($rowText)) {
                            $extractedText[] = "Row " . ($rowIndex + 1) . ": " . implode(" | ", $rowText);
                        }
                    }
                }
            }
            
            return implode("\n", $extractedText);
        } catch (\Exception $e) {
            return "Error extracting text from CSV file: " . $e->getMessage();
        }
    }

    /**
     * Get supported file types
     */
    public function getSupportedFileTypes(): array
    {
        return [
            'pdf' => 'PDF Documents',
            'xlsx' => 'Excel Spreadsheets',
            'xls' => 'Excel Spreadsheets (Legacy)',
            'csv' => 'CSV Files',
            'txt' => 'Text Files'
        ];
    }

    /**
     * Get maximum file size in MB
     */
    public function getMaxFileSize(): int
    {
        return 10; // 10MB
    }
}
