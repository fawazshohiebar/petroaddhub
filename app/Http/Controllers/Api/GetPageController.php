<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;

class GetPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    #[HeaderParameter(name: 'x-api-key', description: 'API key for authentication', required: true, example: '1234')]
    public function __invoke(Request $request, $slug)
    {
        $entry = Entry::query()
            ->where('slug', $slug)
            ->select(['title', 'mobile_builder'])
            ->first();
        if (!$entry) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        // Get mobile_builder as augmented array
        $mobileBuilder = collect($entry->toAugmentedArray()['mobile_builder'] ?? [])
            ->map(function ($block) {
                // Convert block to array recursively
                $blockArray = json_decode(json_encode($block->toArray()), true);
                
                // Replace card_image with just the permalink
                if (isset($blockArray['card_image']['permalink'])) {
                    $blockArray['card_image'] = $blockArray['card_image']['permalink'];
                }
                
                return $blockArray;
            })
            ->values(); // reindex array

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $entry->value('title'),
                'mobile_builder' => $mobileBuilder,
            ],
        ]);
    }
}
