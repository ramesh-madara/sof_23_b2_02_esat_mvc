<?php

class FPDF
{
    private string $orientation;
    private string $unit;
    private string $pageSize;
    private float $pageWidth;
    private float $pageHeight;
    private float $currentX;
    private float $currentY;
    private float $leftMargin;
    private float $topMargin;
    private float $rightMargin;
    private string $currentFont;
    private float $currentFontSize;
    private array $pages;
    private int $pageNumber;

    public function __construct(string $orientation = 'P', string $unit = 'mm', string $pageSize = 'A4')
    {
        $this->orientation = $orientation;
        $this->unit = $unit;
        $this->pageSize = $pageSize;
        $this->pageWidth = 595.28;
        $this->pageHeight = 841.89;
        $this->leftMargin = 28.35;
        $this->topMargin = 28.35;
        $this->rightMargin = 28.35;
        $this->currentX = $this->leftMargin;
        $this->currentY = $this->topMargin;
        $this->currentFont = '/F1';
        $this->currentFontSize = 12.0;
        $this->pages = [];
        $this->pageNumber = 0;
    }

    public function AddPage(string $orientation = '', string $pageSize = '', int $rotation = 0): void
    {
        $this->pageNumber++;
        $this->pages[$this->pageNumber] = '';
        $this->currentX = $this->leftMargin;
        $this->currentY = $this->topMargin;
    }

    public function SetFont(string $fontFamily, string $fontStyle = '', float $fontSize = 0.0): void
    {
        $this->currentFont = (strpos(strtoupper($fontStyle), 'B') !== false) ? '/F2' : '/F1';
        if ($fontSize > 0) {
            $this->currentFontSize = $fontSize;
        }
    }

    public function Cell(float $cellWidth, float $cellHeight = 0.0, string $text = '', $cellBorder = 0, int $lineBreak = 0, string $alignment = '', bool $cellFill = false, string $link = ''): void
    {
        $actualWidth = ($cellWidth == 0) ? ($this->pageWidth - $this->leftMargin - $this->rightMargin) : ($cellWidth * 2.83465);
        $actualHeight = ($cellHeight == 0) ? ($this->currentFontSize * 1.25) : ($cellHeight * 2.83465);

        $pdfX = $this->currentX;
        $pdfY = $this->pageHeight - $this->currentY - $actualHeight;

        $pageContent = '';
        if ($cellBorder) {
            $pageContent .= sprintf("0 0 0 RG 0.5 w %.2F %.2F %.2F %.2F re S\n", $pdfX, $pdfY, $actualWidth, $actualHeight);
        }

        $escapedText = strtr($text, [')' => '\\)', '(' => '\\(', '\\' => '\\\\']);
        $textX = $pdfX + 3.0;
        $textY = $pdfY + ($actualHeight / 2) - ($this->currentFontSize * 0.35);

        if ($alignment === 'C') {
            $textX = $pdfX + ($actualWidth / 2) - (strlen($text) * $this->currentFontSize * 0.28);
        } elseif ($alignment === 'R') {
            $textX = $pdfX + $actualWidth - (strlen($text) * $this->currentFontSize * 0.55) - 3.0;
        }

        $pageContent .= "BT\n";
        $pageContent .= "0 g\n";
        $pageContent .= sprintf("%s %.2F Tf\n", $this->currentFont, $this->currentFontSize);
        $pageContent .= sprintf("%.2F %.2F Td\n", $textX, $textY);
        $pageContent .= sprintf("(%s) Tj\n", $escapedText);
        $pageContent .= "ET\n";

        $this->pages[$this->pageNumber] .= $pageContent;

        if ($lineBreak > 0) {
            $this->currentY += $actualHeight;
            $this->currentX = $this->leftMargin;
        } else {
            $this->currentX += $actualWidth;
        }
    }

    public function Ln(float $lineHeight = 0.0): void
    {
        $actualHeight = ($lineHeight > 0) ? ($lineHeight * 2.83465) : ($this->currentFontSize * 1.25);
        $this->currentX = $this->leftMargin;
        $this->currentY += $actualHeight;
    }

    public function Output(string $outputMode = 'I', string $fileName = 'document.pdf', bool $isUtf8 = false): string
    {
        $pdfContent = "%PDF-1.4\n";
        $objectOffsets = [];

        $objectOffsets[1] = strlen($pdfContent);
        $pdfContent .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

        $pageReferences = [];
        for ($pageIndex = 1; $pageIndex <= $this->pageNumber; $pageIndex++) {
            $pageObjectId = 5 + ($pageIndex - 1) * 2;
            $pageReferences[] = "{$pageObjectId} 0 R";
        }
        $pageList = implode(' ', $pageReferences);

        $objectOffsets[2] = strlen($pdfContent);
        $pdfContent .= "2 0 obj\n<< /Type /Pages /Kids [{$pageList}] /Count {$this->pageNumber} >>\nendobj\n";

        $objectOffsets[3] = strlen($pdfContent);
        $pdfContent .= "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\nendobj\n";

        $objectOffsets[4] = strlen($pdfContent);
        $pdfContent .= "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>\nendobj\n";

        $nextObjectId = 5;
        for ($pageIndex = 1; $pageIndex <= $this->pageNumber; $pageIndex++) {
            $pageObjectId = $nextObjectId++;
            $streamObjectId = $nextObjectId++;

            $streamContent = $this->pages[$pageIndex];
            $streamLength = strlen($streamContent);

            $objectOffsets[$pageObjectId] = strlen($pdfContent);
            $pdfContent .= "{$pageObjectId} 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Contents {$streamObjectId} 0 R /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> >>\nendobj\n";

            $objectOffsets[$streamObjectId] = strlen($pdfContent);
            $pdfContent .= "{$streamObjectId} 0 obj\n<< /Length {$streamLength} >>\nstream\n{$streamContent}endstream\nendobj\n";
        }

        $crossReferenceOffset = strlen($pdfContent);
        $totalObjects = $nextObjectId;
        $pdfContent .= "xref\n0 {$totalObjects}\n";
        $pdfContent .= "0000000000 65535 f \r\n";
        for ($objectIndex = 1; $objectIndex < $totalObjects; $objectIndex++) {
            $pdfContent .= sprintf("%010d 00000 n \r\n", $objectOffsets[$objectIndex]);
        }

        $pdfContent .= "trailer\n<< /Size {$totalObjects} /Root 1 0 R >>\n";
        $pdfContent .= "startxref\n{$crossReferenceOffset}\n%%EOF\n";

        if ($outputMode === 'I' || $outputMode === 'D') {
            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($pdfContent));
            $displayMode = ($outputMode === 'D') ? 'attachment' : 'inline';
            header("Content-Disposition: {$displayMode}; filename=\"{$fileName}\"");
            echo $pdfContent;
            exit;
        }

        return $pdfContent;
    }
}
