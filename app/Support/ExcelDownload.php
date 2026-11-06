<?php

namespace App\Support;

use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use XMLWriter;
use ZipArchive;

class ExcelDownload
{
    /** Write text explicitly so identifiers retain leading zeros and values cannot become formulas. */
    public function download(string $filename, array $headings, iterable $rows): BinaryFileResponse
    {
        $sheet = tempnam(sys_get_temp_dir(), 'excel-sheet-');
        $file = tempnam(sys_get_temp_dir(), 'excel-book-');

        try {
            $xml = new XMLWriter();
            $xml->openUri($sheet);
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('worksheet');
            $xml->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $xml->startElement('sheetData');
            $writeRow = function (array $values) use ($xml): void {
                $xml->startElement('row');
                foreach ($values as $value) {
                    $xml->startElement('c');
                    if (is_int($value) || is_float($value)) {
                        $xml->writeElement('v', (string) $value);
                    } else {
                        $xml->writeAttribute('t', 'inlineStr');
                        $xml->startElement('is');
                        $xml->startElement('t');
                        $xml->writeAttribute('xml:space', 'preserve');
                        $text = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', (string) $value);
                        $xml->text(mb_substr($text ?? '', 0, 32767));
                        $xml->endElement();
                        $xml->endElement();
                    }
                    $xml->endElement();
                }
                $xml->endElement();
            };
            $writeRow($headings);
            $count = 1;
            foreach ($rows as $row) {
                if (++$count > 1048576) {
                    throw new RuntimeException('Data melebihi batas baris Excel.');
                }
                $writeRow($row);
            }
            $xml->endElement();
            $xml->endElement();
            $xml->endDocument();
            $xml->flush();
            unset($xml);

            $zip = new ZipArchive();
            if ($zip->open($file, ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('File Excel tidak dapat dibuat.');
            }
            $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
            $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
            $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Data" sheetId="1" r:id="rId1"/></sheets></workbook>');
            $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
            $zip->addFile($sheet, 'xl/worksheets/sheet1.xml');
            if (!$zip->close()) {
                throw new RuntimeException('File Excel tidak dapat disimpan.');
            }

            return response()->download($file, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'private, no-store',
            ])->deleteFileAfterSend(true);
        } catch (\Throwable $exception) {
            @unlink($file);
            throw $exception;
        } finally {
            @unlink($sheet);
        }
    }
}
