<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers to ensure JSON response
header('Content-Type: application/json');

// Check if vendor/autoload.php exists
if (!file_exists('vendor/autoload.php')) {
    echo json_encode(['error' => 'Composer dependencies not installed. Please run: composer require phpoffice/phppresentation']);
    exit;
}

require 'vendor/autoload.php';

use PhpOffice\PhpPresentation\IOFactory;

try {
    // Check if file was uploaded
    if (!isset($_FILES['pptx']) || $_FILES['pptx']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $file = $_FILES['pptx']['tmp_name'];
    
    // Verify file exists and is readable
    if (!file_exists($file) || !is_readable($file)) {
        throw new Exception('Uploaded file is not accessible');
    }

    $textContent = '';
    
    // Load the presentation
    $presentation = IOFactory::load($file);
    
    // Process each slide
    foreach ($presentation->getAllSlides() as $slide) {
        foreach ($slide->getShapeCollection() as $shape) {
            if ($shape instanceof \PhpOffice\PhpPresentation\Shape\RichText) {
                foreach ($shape->getParagraphs() as $paragraph) {
                    foreach ($paragraph->getRichTextElements() as $textElement) {
                        $textContent .= $textElement->getText() . ' ';
                    }
                }
            }
        }
    }
    
    // Return success with extracted text
    echo json_encode([
        'success' => true,
        'text' => $textContent
    ]);

} catch (Exception $e) {
    // Return error in JSON format
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 