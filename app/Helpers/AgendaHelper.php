<?php

namespace App\Helpers;

use Statamic\Facades\Entry;
use Illuminate\Support\Carbon;
use Statamic\Facades\Asset;

class AgendaHelper
{
    public static function getSpeakers($speakers)
    {
        if (empty($speakers)) {
            return [];
        }

        $speakerData = [];
        
        foreach ($speakers as $speakerId) {
            $speaker = \Statamic\Facades\Entry::find($speakerId);
            if ($speaker) {
                $speakerData[] = [
                    'name' => $speaker->title ?? '',
                    'content' => $speaker->content ?? '',
                    'photo' => isset($speaker->image) ? self::getSpeakerPhoto($speaker->image) : null,
                    'position' => $speaker->position ?? '',
                    'company' => $speaker->company ?? '',
                ];
            }
        }
        
        return $speakerData;
    }

    public static function getHall($halls)
    {
        // halls are taxonomies terms, not entries
        if (empty($halls)) {
            return 'TBA';
        }
        if(is_string($halls)) {
            // If halls is a string, it might be a single hall ID or slug
            $hall = \Statamic\Facades\Term::find('halls::'.$halls);
            
            return $hall ? $hall->title : 'TBA';
        }

        if (is_array($halls) ) {
            foreach ($halls as $key => $value) {
                $hall = \Statamic\Facades\Taxonomy::find($value);
                if ($hall) {
                    return $hall->title;
                }
            }
        }
    }

    public static function getSpeakerPhoto($image)
    {
        try {
            // Handle both asset ID and asset object
            if (is_object($image) && method_exists($image, 'url')) {
                return $image->url;
            }
            
            if (is_string($image)) {
                $asset = Asset::find($image);
                return $asset ? $asset->url : null;
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getAgendas($agendas)
    {
        if (empty($agendas)) {
            return [];
        }

        $agendaData = [];
        
        foreach ($agendas as $agendaId) {
            $agenda = \Statamic\Facades\Entry::find($agendaId);
            if ($agenda) {
                $agendaData[] = [
                    'id' => $agendaId,
                    'title' => $agenda->title ?? '',
                    'slug' => $agenda->slug ?? '',
                ];
            }
        }
        
        return $agendaData;
    }

    public static function shouldShowTimeDuration($startTime, $endTime)
    {
        return Carbon::createFromFormat('H:i', $startTime)
                    ->diffInHours(Carbon::createFromFormat('H:i', $endTime)) > 1;
    }
}