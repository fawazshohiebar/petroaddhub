<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Form;

class TransportController extends Controller
{
    //

    public function index(): JsonResponse
    {
        $entry = Entry::query()
            ->where('slug', 'transport')
            ->first();

        if (! $entry) {
            return response()->json([
                'success' => false,
                'message' => 'Transport page not found',
            ], 404);
        }

        $pageBuilder = $entry->get('page_builder', []);
        $ctav1Data = null;

        foreach ($pageBuilder as $block) {
            if (
                isset($block['type']) &&
                $block['type'] === 'set' &&
                isset($block['attrs']['values']['type']) &&
                $block['attrs']['values']['type'] === 'ctav1'
            ) {
                $ctav1Data = $block['attrs']['values'];
                break;
            }
        }

        if (! $ctav1Data) {
            return response()->json([
                'success' => false,
                'message' => 'CTAv1 block not found',
            ], 404);
        }

        // Extract Transport Options
        $transportOptions = [];
        if (isset($ctav1Data['description'][0]['content'])) {
            foreach ($ctav1Data['description'][0]['content'] as $item) {
                // Each item is a listItem > paragraph > text
                $text = $item['content'][0]['content'][0]['text'] ?? null;
                if ($text) {
                    $transportOptions[] = $text;
                }
            }
        }

        // Convert heading_text to simple HTML
        $contentHtml = '';
        if (isset($ctav1Data['heading_text'])) {
            foreach ($ctav1Data['heading_text'] as $block) {
                if (isset($block['content'][0]['text'])) {
                    $contentHtml .= '<p>' . $block['content'][0]['text'] . '</p>';
                }
            }
        }

        // Prepare images array
        $images = [];
        if (!empty($ctav1Data['section_image'])) {
            $images[] = $ctav1Data['section_image'];
        }

        return response()->json([
            'success' => true,
            'Response' => [
                'Title' => $entry->get('title') ?? 'Transport',
                'Images' => $images,
                'Content' => $contentHtml,
                'TransportOptions' => $transportOptions,
            ],
        ]);
    }


    #[HeaderParameter(name: 'x-api-key', description: 'API key for authentication', required: true, example: '1234')]
    public function formSubmission(Request $request): JsonResponse
    {
        // Validate incoming request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:50',
            'transport_options' => 'required|array',
            'transport_options.*' => 'string',
            'comments' => 'nullable|string',
            'consent' => 'required|array',
        ]);

        try {
            // Get the transport form
            $form = Form::find('transport');

            if (!$form) {
                return response()->json([
                    'success' => false,
                    'message' => 'Form not found',
                ], 404);
            }

            // Create a new submission
            $submission = $form->makeSubmission();
            $submission->data([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'transport_options' => $validated['transport_options'],
                'comments' => $validated['comments'] ?? '',
                'consent' => $validated['consent'],
            ]);
            $submission->save();

            return response()->json([
                'success' => true,
                'message' => 'Form submitted successfully',
                'data' => [
                    'submission_id' => $submission->id(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit form',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
