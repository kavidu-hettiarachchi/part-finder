<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PartController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        if (strlen($query) > 3) {
            $parts = Part::with(['captions.referencedCaptionParts', 'groupParts', 'smcsCodes', 'lineItems', 'notes', 'lineItems.imageIdentifiers'])
                ->where('ieControlNumber', 'LIKE', "%{$query}%")
                ->orWhere('mediaNumber', 'LIKE', "%{$query}%")
                ->orWhereHas('lineItems', function ($q) use ($query) {
                    $q->where('partNumber', 'LIKE', "%{$query}%")
                        ->orWhere('partName', 'LIKE', "%{$query}%");
                })
                ->get();
            return response()->json($parts);
        }
        return response()->json([]);
    }
}
