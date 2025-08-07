<?php

namespace App\Http\Controllers;

use App\Helpers\AgendaHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GetAgendaByDateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $agenda, $date)
    {
        /** @var \Statamic\Contracts\Entries\EntryQueryBuilder $query */
        $query = \Statamic\Facades\Entry::query();

         $sessions = $query
        ->orderBy('start_time', 'asc')
        ->orderBy('order', 'asc')
        ->where('collection', 'sessions')
        ->whereTaxonomy('agendas::' . $agenda)
        ->whereDate('event_date', Carbon::parse($date)->format('Y-m-d'))
        ->select([
            'title',
            'slug',
            'event_date',
            'start_time',
            'end_time',
            'halls',
            'content',
            'speakers',
            'agendas',
        ])
        ->get()
        ->map(function ($item) {
            return [
                'title' => $item->get('title'),
                'slug' => $item->get('slug'),
                'event_date' => $item->get('start_date'),
                'start_time' => $item->get('start_time'),
                'end_time' => $item->get('end_time'),
                'show_time_duration' => AgendaHelper::shouldShowTimeDuration($item->get('start_time'), $item->get('end_time')),
                'hall' => AgendaHelper::getHall($item->get('halls', [])),
                'description' => $item->get('content'),
                'speakers' => AgendaHelper::getSpeakers($item->get('speakers', [])),
            ];
        });

    return response()->json($sessions);
    }
}
