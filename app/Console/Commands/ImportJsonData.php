<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportJsonData extends Command
{
    protected $signature = 'import:json {--manual}';
    protected $description = 'Import JSON data from public/source-data-json/in-progress directory';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $directory = public_path('source-data-json/in-progress');
        $completedDirectory = public_path('source-data-json/completed');
        $files = File::files($directory);

        foreach ($files as $file) {
            try {
                DB::beginTransaction();

                $filePath = $file->getPathname();
                $fileName = $file->getFilename();
                $existingFile = DB::table('file_metadata')->where('file_name', $fileName)->first();

                if ($existingFile) {
                    // If the file has been processed before, check for updates
                    $jsonData = json_decode(File::get($filePath), true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON format in file: ' . $fileName);
                    }

                    // Process and update the changes
                    $this->updateJsonData($jsonData, $existingFile->id);

                    // Update file metadata
                    DB::table('file_metadata')->where('file_name', $fileName)->update([
                        'status' => 'updated',
                        'updated_at' => Carbon::now(),
                    ]);

                    DB::commit();
                    $this->info("Successfully updated: $fileName");
                } else {
                    // If the file is new, insert the data
                    $jsonData = json_decode(File::get($filePath), true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON format in file: ' . $fileName);
                    }

                    // Process and insert the data
                    $this->insertJsonData($jsonData);

                    // Log file metadata
                    DB::table('file_metadata')->insert([
                        'file_name' => $fileName,
                        'status' => 'completed',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    DB::commit();
                    $this->info("Successfully processed: $fileName");
                }

                // Move file to completed directory with versioning
                $versionedFileName = $this->getVersionedFileName($fileName, $completedDirectory);
                File::move($filePath, $completedDirectory . '/' . $versionedFileName);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing file: ' . $file->getFilename() . '. Error: ' . $e->getMessage());

                DB::table('file_metadata')->insert([
                    'file_name' => $file->getFilename(),
                    'status' => 'error',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $this->error("Failed to process: $fileName. Error: " . $e->getMessage());
            }
        }
    }

    private function insertJsonData($jsonData)
    {
        // Insert data into respective tables
        $partId = DB::table('parts')->insertGetId([
            'iePublicationDate' => $jsonData['iePublicationDate'] ?? null,
            'ieUpdateDate' => $jsonData['ieUpdateDate'] ?? null,
            'ieControlNumber' => $jsonData['ieControlNumber'] ?? null,
            'mediaNumber' => $jsonData['mediaNumber'] ?? null,
            'isExpandedMiningProduct' => $jsonData['isExpandedMiningProduct'] ?? false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('captions')->insert([
            'part_id' => $partId,
            'name' => $jsonData['caption']['name'],
            'language' => $jsonData['caption']['language'],
            'orgCode' => $jsonData['caption']['orgCode'],
            'kitsLinkedMediaNumber' => $jsonData['caption']['kitsLinkedMediaNumber'] ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Handle groupPart which is an object
        $groupPart = $jsonData['groupPart'];
        DB::table('group_parts')->insert([
            'part_id' => $partId,
            'partNumber' => $groupPart['partNumber'],
            'orgCode' => $groupPart['orgCode'],
            'partName' => $groupPart['partName']['name'],
            'partLanguage' => $groupPart['partName']['language'],
            'modifier' => $groupPart['modifier']['name'] ?? null,
            'modifierLanguage' => $groupPart['modifier']['language'] ?? null,
            'serviceabilityIndicator' => $groupPart['serviceabilityIndicator'],
            'alternatePartType' => $groupPart['alternatePartType'] ?? null,
            'hasAlternate' => $groupPart['hasAlternate'],
            'isCCRPart' => $groupPart['isCCRPart'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        foreach ($jsonData['smcsCodes'] as $smcsCode) {
            DB::table('smcs_codes')->insert([
                'part_id' => $partId,
                'code' => $smcsCode['code'],
                'description' => $smcsCode['description']['name'],
                'language' => $smcsCode['description']['language'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        foreach ($jsonData['lineItems'] as $lineItem) {
            $lineItemId = DB::table('line_items')->insertGetId([
                'part_id' => $partId,
                'noteCodes' => $lineItem['noteCodes'] ?? null,
                'partNumber' => $lineItem['partNumber'],
                'orgCode' => $lineItem['orgCode'],
                'partName' => $lineItem['partName']['name'] ?? null,
                'partNameLanguage' => $lineItem['partName']['language'] ?? null,
                'serviceabilityIndicator' => $lineItem['serviceabilityIndicator'],
                'partSequenceNumber' => $lineItem['partSequenceNumber'],
                'parentage' => $lineItem['parentage'],
                'quantity' => $lineItem['quantity'] ?? null,
                'ieSystemControlNumber' => $lineItem['ieSystemControlNumber'] ?? null,
                'mediaNumber' => $lineItem['mediaNumber'] ?? null,
                'componentId' => $lineItem['componentId'] ?? null,
                'comments' => $lineItem['comments'] ?? null,
                'referenceNumber' => $lineItem['referenceNumber'] ?? null,
                'modifier' => $lineItem['modifier']['name'] ?? null,
                'modifierLanguage' => $lineItem['modifier']['language'] ?? null,
                'isCCRPart' => $lineItem['isCCRPart'],
                'hasAlternate' => $lineItem['hasAlternate'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (isset($lineItem['imageIdentifiers']) && is_array($lineItem['imageIdentifiers'])) {
                foreach ($lineItem['imageIdentifiers'] as $imageId) {
                    DB::table('image_identifiers')->insert([
                        'part_id' => $partId,
                        'line_item_id' => $lineItemId,
                        'imageId' => $imageId,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }

            if (isset($lineItem['graphicNumbers']) && is_array($lineItem['graphicNumbers'])) {
                foreach ($lineItem['graphicNumbers'] as $graphicNumber) {
                    DB::table('line_item_graphic_numbers')->insert([
                        'line_item_id' => $lineItemId,
                        'graphicNumber' => $graphicNumber,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }

        foreach ($jsonData['notes'] as $note) {
            DB::table('notes')->insert([
                'part_id' => $partId,
                'noteCode' => $note['noteCode'],
                'noteName' => $note['note']['name'],
                'language' => $note['note']['language'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        if (isset($groupPart['imageIdentifiers']) && is_array($groupPart['imageIdentifiers'])) {
            foreach ($groupPart['imageIdentifiers'] as $imageId) {
                DB::table('image_identifiers')->insert([
                    'part_id' => $partId,
                    'imageId' => $imageId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        if (isset($jsonData['caption']['referencedCaptionParts'])) {
            foreach ($jsonData['caption']['referencedCaptionParts'] as $refPart) {
                DB::table('referenced_caption_parts')->insert([
                    'caption_id' => DB::table('captions')->where('part_id', $partId)->first()->id,
                    'partNumber' => $refPart['partNumber'],
                    'orgCode' => $refPart['orgCode'],
                    'ieSystemControlNumber' => $refPart['ieSystemControlNumber'] ?? null,
                    'disambiguation' => $refPart['disambiguation'] ?? false,
                    'componentId' => $refPart['componentId'] ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

    private function updateJsonData($jsonData, $fileId)
    {
        // Update parts table
        $part = DB::table('parts')->where('id', $fileId)->first();
        DB::table('parts')->where('id', $fileId)->update([
            'iePublicationDate' => $jsonData['iePublicationDate'] ?? $part->iePublicationDate,
            'ieUpdateDate' => $jsonData['ieUpdateDate'] ?? $part->ieUpdateDate,
            'ieControlNumber' => $jsonData['ieControlNumber'] ?? $part->ieControlNumber,
            'mediaNumber' => $jsonData['mediaNumber'] ?? $part->mediaNumber,
            'isExpandedMiningProduct' => $jsonData['isExpandedMiningProduct'] ?? $part->isExpandedMiningProduct,
            'updated_at' => Carbon::now(),
        ]);

        // Similar updates for other tables (captions, group_parts, smcs_codes, line_items, notes, etc.)

        // Update existing lineItems and add new lineItems if any
        foreach ($jsonData['lineItems'] as $lineItem) {
            $existingLineItem = DB::table('line_items')->where([
                ['part_id', '=', $fileId],
                ['partNumber', '=', $lineItem['partNumber']],
                ['orgCode', '=', $lineItem['orgCode']],
            ])->first();

            if ($existingLineItem) {
                DB::table('line_items')->where('id', $existingLineItem->id)->update([
                    'noteCodes' => $lineItem['noteCodes'] ?? $existingLineItem->noteCodes,
                    'partName' => $lineItem['partName']['name'] ?? $existingLineItem->partName,
                    'partNameLanguage' => $lineItem['partName']['language'] ?? $existingLineItem->partNameLanguage,
                    'serviceabilityIndicator' => $lineItem['serviceabilityIndicator'],
                    'partSequenceNumber' => $lineItem['partSequenceNumber'],
                    'parentage' => $lineItem['parentage'],
                    'quantity' => $lineItem['quantity'] ?? $existingLineItem->quantity,
                    'ieSystemControlNumber' => $lineItem['ieSystemControlNumber'] ?? $existingLineItem->ieSystemControlNumber,
                    'mediaNumber' => $lineItem['mediaNumber'] ?? $existingLineItem->mediaNumber,
                    'componentId' => $lineItem['componentId'] ?? $existingLineItem->componentId,
                    'comments' => $lineItem['comments'] ?? $existingLineItem->comments,
                    'referenceNumber' => $lineItem['referenceNumber'] ?? $existingLineItem->referenceNumber,
                    'modifier' => $lineItem['modifier']['name'] ?? $existingLineItem->modifier,
                    'modifierLanguage' => $lineItem['modifier']['language'] ?? $existingLineItem->modifierLanguage,
                    'isCCRPart' => $lineItem['isCCRPart'],
                    'hasAlternate' => $lineItem['hasAlternate'],
                    'updated_at' => Carbon::now(),
                ]);

                // Update or add imageIdentifiers
                if (isset($lineItem['imageIdentifiers']) && is_array($lineItem['imageIdentifiers'])) {
                    foreach ($lineItem['imageIdentifiers'] as $imageId) {
                        $existingImage = DB::table('image_identifiers')->where([
                            ['part_id', '=', $fileId],
                            ['line_item_id', '=', $existingLineItem->id],
                            ['imageId', '=', $imageId],
                        ])->first();

                        if (!$existingImage) {
                            DB::table('image_identifiers')->insert([
                                'part_id' => $fileId,
                                'line_item_id' => $existingLineItem->id,
                                'imageId' => $imageId,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }
                    }
                }

                // Update or add graphicNumbers
                if (isset($lineItem['graphicNumbers']) && is_array($lineItem['graphicNumbers'])) {
                    foreach ($lineItem['graphicNumbers'] as $graphicNumber) {
                        $existingGraphicNumber = DB::table('line_item_graphic_numbers')->where([
                            ['line_item_id', '=', $existingLineItem->id],
                            ['graphicNumber', '=', $graphicNumber],
                        ])->first();

                        if (!$existingGraphicNumber) {
                            DB::table('line_item_graphic_numbers')->insert([
                                'line_item_id' => $existingLineItem->id,
                                'graphicNumber' => $graphicNumber,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }
                    }
                }
            } else {
                // Add new lineItem if not exists
                $this->insertLineItem($lineItem, $fileId);
            }
        }

        // Similar updates for other tables
    }

    private function insertLineItem($lineItem, $partId)
    {
        $lineItemId = DB::table('line_items')->insertGetId([
            'part_id' => $partId,
            'noteCodes' => $lineItem['noteCodes'] ?? null,
            'partNumber' => $lineItem['partNumber'],
            'orgCode' => $lineItem['orgCode'],
            'partName' => $lineItem['partName']['name'] ?? null,
            'partNameLanguage' => $lineItem['partName']['language'] ?? null,
            'serviceabilityIndicator' => $lineItem['serviceabilityIndicator'],
            'partSequenceNumber' => $lineItem['partSequenceNumber'],
            'parentage' => $lineItem['parentage'],
            'quantity' => $lineItem['quantity'] ?? null,
            'ieSystemControlNumber' => $lineItem['ieSystemControlNumber'] ?? null,
            'mediaNumber' => $lineItem['mediaNumber'] ?? null,
            'componentId' => $lineItem['componentId'] ?? null,
            'comments' => $lineItem['comments'] ?? null,
            'referenceNumber' => $lineItem['referenceNumber'] ?? null,
            'modifier' => $lineItem['modifier']['name'] ?? null,
            'modifierLanguage' => $lineItem['modifier']['language'] ?? null,
            'isCCRPart' => $lineItem['isCCRPart'],
            'hasAlternate' => $lineItem['hasAlternate'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (isset($lineItem['imageIdentifiers']) && is_array($lineItem['imageIdentifiers'])) {
            foreach ($lineItem['imageIdentifiers'] as $imageId) {
                DB::table('image_identifiers')->insert([
                    'part_id' => $partId,
                    'line_item_id' => $lineItemId,
                    'imageId' => $imageId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        if (isset($lineItem['graphicNumbers']) && is_array($lineItem['graphicNumbers'])) {
            foreach ($lineItem['graphicNumbers'] as $graphicNumber) {
                DB::table('line_item_graphic_numbers')->insert([
                    'line_item_id' => $lineItemId,
                    'graphicNumber' => $graphicNumber,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

    private function getVersionedFileName($fileName, $completedDirectory)
    {
        $fileInfo = pathinfo($fileName);
        $baseName = $fileInfo['filename'];
        $extension = $fileInfo['extension'];
        $dateTime = Carbon::now()->format('Ymd_His');

        $versionedFileName = "{$baseName}_{$dateTime}.{$extension}";
        while (File::exists($completedDirectory . '/' . $versionedFileName)) {
            $versionedFileName = "{$baseName}_{$dateTime}.{$extension}";
        }

        return $versionedFileName;
    }
}
