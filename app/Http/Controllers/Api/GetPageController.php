<?php

namespace App\Http\Controllers\API;

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

        return response()->json([
            'success' => true,
            'data' => $entry,
        ]);
    }
}
