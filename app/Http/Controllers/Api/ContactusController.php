<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Statamic\Facades\Entry;
use Statamic\Facades\Form;
class ContactusController extends Controller
{

    

    public function formSubmission(Request $request): JsonResponse
    {
        // Validate incoming request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'mobile' => 'required|string|max:50',
            'message' => 'nullable|string',
            'turnstile_token' => 'nullable|string',
        ]);

        try {
            // Get the transport form
            $form = Form::find('contact_us');

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
                'email_address' => $validated['email_address'],
                'mobile' => $validated['mobile'],
                'message' => $validated['message'] ?? '',
                'turnstile_token' => $validated['turnstile_token'] ?? '',
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
