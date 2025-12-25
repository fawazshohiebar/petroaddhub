<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Statamic\Facades\Entry;

/**
 * @tags FAQ
 */
class FaqController extends Controller
{
    /**
     * Get all FAQ sections and questions
     * 
     * Returns all frequently asked questions organized by sections.
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "title": "Faq",
     *     "sections": [
     *       {
     *         "title": "GENERAL",
     *         "questions": [
     *           {
     *             "id": "miztd69y",
     *             "question": "What is Challenge Sir Bani Yas?",
     *             "answer": "Challenge Sir Bani Yas is the latest edition..."
     *           }
     *         ]
     *       }
     *     ]
     *   }
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "FAQ page not found"
     * }
     */
    public function index(): JsonResponse
    {
        // Get the FAQ page entry
        $faqEntry = Entry::whereCollection('pages')
            ->where('slug', 'faq')
            ->first();

        if (!$faqEntry) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ page not found'
            ], 404);
        }

        $faqSections = [];
        
        // Extract FAQ sections from page_builder
        $pageBuilder = $faqEntry->get('page_builder', []);
        
        foreach ($pageBuilder as $block) {
            // Check if this is a FAQ section block
            if ($block['type'] === 'set' && 
                isset($block['attrs']['values']['type']) && 
                $block['attrs']['values']['type'] === 'faq_section') {
                
                $values = $block['attrs']['values'];
                
                // Extract section title
                $sectionTitle = 'Untitled Section';
                if (isset($values['faq_title'])) {
                    $titleContent = $values['faq_title'];
                    $titleText = '';
                    
                    foreach ($titleContent as $node) {
                        if (isset($node['content'])) {
                            foreach ($node['content'] as $textNode) {
                                if (isset($textNode['text'])) {
                                    $titleText .= $textNode['text'];
                                }
                            }
                        } elseif (isset($node['text'])) {
                            $titleText .= $node['text'];
                        }
                    }
                    $sectionTitle = trim($titleText);
                }
                
                // Extract questions
                $questions = [];
                foreach ($values['faq_rep'] ?? [] as $faqItem) {
                    if ($faqItem['enabled'] ?? true) {
                        // Extract answer text from ProseMirror format
                        $answerText = '';
                        $answerContent = $faqItem['answer'] ?? [];
                        
                        foreach ($answerContent as $node) {
                            if (isset($node['content'])) {
                                foreach ($node['content'] as $textNode) {
                                    if (isset($textNode['text'])) {
                                        $answerText .= $textNode['text'];
                                    }
                                }
                                // Add paragraph spacing
                                if (isset($node['type']) && $node['type'] === 'paragraph') {
                                    $answerText .= "\n\n";
                                }
                            } elseif (isset($node['text'])) {
                                $answerText .= $node['text'];
                            }
                        }
                        
                        $questions[] = [
                            'id' => $faqItem['id'],
                            'question' => $faqItem['question'],
                            'answer' => trim($answerText)
                        ];
                    }
                }
                
                // Add section if it has questions
                if (!empty($questions)) {
                    $faqSections[] = [
                        'title' => $sectionTitle,
                        'questions' => $questions
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $faqEntry->get('title', 'FAQ'),
                'sections' => $faqSections
            ]
        ]);
    }

    /**
     * Get FAQ questions from a specific section
     * 
     * Returns FAQ questions filtered by section title.
     * 
     * @param string $section The section name (e.g., "general", "registration", "the-island")
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "section": "GENERAL",
     *     "questions": [
     *       {
     *         "id": "miztd69y",
     *         "question": "What is Challenge Sir Bani Yas?",
     *         "answer": "Challenge Sir Bani Yas is the latest edition..."
     *       }
     *     ]
     *   }
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "Section not found"
     * }
     */


    

    /**
     * Search FAQ questions by keyword
     * 
     * Returns FAQ questions that match the search query in question or answer.
     * 
     * @param string $query The search term
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "query": "triathlon",
     *     "results": [
     *       {
     *         "id": "miztd69y",
     *         "question": "What is Challenge Sir Bani Yas?",
     *         "answer": "Challenge Sir Bani Yas is the latest edition...",
     *         "section": "GENERAL"
     *       }
     *     ]
     *   }
     * }
     */


    public function testing(string $slug): JsonResponse
    {
        $entry = Entry::query()
            ->where('slug', $slug)
            ->first();

        if (! $entry) {
            return response()->json([
                'success' => false,
                'message' => 'Entry not found',
            ], 404);
        }

        $pageBuilder = $entry->get('page_builder') ?? [];

        // Process sections and expand reusable blocks
        $processedSections = collect($pageBuilder)->map(function ($section) {
            // Check if this is a reusable block INSIDE a 'set' block
            if (
                isset($section['type']) && $section['type'] === 'set' &&
                isset($section['attrs']['values']['type']) && $section['attrs']['values']['type'] === 'reusable_block' &&
                isset($section['attrs']['values']['entry'])
            ) {
                $entryIds = $section['attrs']['values']['entry'];
                Log::info('Reusable block entry IDs:', ['ids' => $entryIds]);
                if (!is_array($entryIds)) {
                    $entryIds = [$entryIds];
                }
                $reusableEntries = [];
                foreach ($entryIds as $entryId) {
                    try {
                        $reusableEntry = Entry::find($entryId);
                        if ($reusableEntry) {
                            $reusableEntries[] = [
                                'id' => $reusableEntry->id(),
                                'slug' => $reusableEntry->slug(),
                                'title' => $reusableEntry->get('title'),
                                'data' => $reusableEntry->data()->toArray(),
                            ];
                            Log::info('Found reusable entry:', ['id' => $entryId, 'title' => $reusableEntry->get('title')]);
                        } else {
                            Log::warning('Reusable entry not found:', ['id' => $entryId]);
                            $reusableEntries[] = [
                                'id' => $entryId,
                                'error' => 'Entry not found'
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::error('Error fetching reusable entry:', ['id' => $entryId, 'error' => $e->getMessage()]);
                        $reusableEntries[] = [
                            'id' => $entryId,
                            'error' => $e->getMessage()
                        ];
                    }
                }
                // Place expanded_entries inside the entry array
                $section['attrs']['values']['expanded_entries'] = $reusableEntries;
                $section['debug'] = [
                    'original_entry_ids' => $section['attrs']['values']['entry'],
                    'processed_count' => count($reusableEntries)
                ];
            }
            return $section;
        });

        return response()->json([
            'success' => true,
            'slug' => $entry->slug(),
            'title' => $entry->get('title'),
            'sections' => $processedSections,
            'debug' => [
                'total_sections' => count($pageBuilder),
                'reusable_blocks' => $processedSections->filter(function($section) {
                    return isset($section['type']) && isset($section['attrs']['values']['type']) && $section['attrs']['values']['type'] === 'reusable_block';
                })->count()
            ]
        ]);
    }
}
