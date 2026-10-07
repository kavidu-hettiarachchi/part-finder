<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PartController extends Controller
{
    private const MIN_QUERY_LENGTH = 4;
    private const MAX_QUERY_LENGTH = 100;
    private const MAX_RESULTS = 50;

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:'.self::MAX_QUERY_LENGTH],
        ]);

        $query = trim($validated['query'] ?? '');

        if (mb_strlen($query) < self::MIN_QUERY_LENGTH) {
            return response()->json([]);
        }

        // Escape LIKE wildcards so user input is matched literally.
        $term = '%'.addcslashes($query, '\\%_').'%';

        $parts = Part::with(['captions.referencedCaptionParts', 'groupParts', 'smcsCodes', 'lineItems.imageIdentifiers', 'notes'])
            ->where(function ($q) use ($term) {
                $q->where('ieControlNumber', 'LIKE', $term)
                    ->orWhere('mediaNumber', 'LIKE', $term)
                    ->orWhereHas('lineItems', function ($q) use ($term) {
                        $q->where('partNumber', 'LIKE', $term)
                            ->orWhere('partName', 'LIKE', $term);
                    });
            })
            ->limit(self::MAX_RESULTS)
            ->get();

        return response()->json($parts);
    }
}
