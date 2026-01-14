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
                
                // Replace all image fields with just the permalink
                $blockArray = $this->processImageFields($blockArray);
                
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

    /**
     * Recursively process array to replace image objects with their permalinks
     */
    private function processImageFields($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            // Check if this is an image array (has permalink and is_image)
            if (is_array($value)) {
                // If it's an array with a single element that has permalink and is_image
                if (count($value) === 1 && isset($value[0]['permalink']) && isset($value[0]['is_image']) && $value[0]['is_image'] === true) {
                    $data[$key] = $value[0]['permalink'];
                }
                // If it's an object with permalink and is_image (not in array)
                elseif (isset($value['permalink']) && isset($value['is_image']) && $value['is_image'] === true) {
                    $data[$key] = $value['permalink'];
                }
                // Otherwise recursively process nested arrays
                else {
                    $data[$key] = $this->processImageFields($value);
                }
            }
        }

        return $data;
    }
}
