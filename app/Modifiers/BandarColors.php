<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class BandarColors extends Modifier
{
    /**
     * Transform inline styles with color: redgrad to customcolors class
     *
     * @param mixed $value
     * @param array $params
     * @param array $context
     * @return string
     */
    public function index($value, $params, $context)
    {
        if (!is_string($value)) {
            return $value;
        }

        // Pattern to match elements with style="color: redgrad"
        $pattern = '/(<[^>]+)style\s*=\s*["\']([^"\']*?)color:\s*redgrad;?([^"\']*?)["\']([^>]*>)/i';
        
        return preg_replace_callback($pattern, function($matches) {
            $beforeStyle = $matches[1];
            $styleBeforeColor = $matches[2];
            $styleAfterColor = $matches[3];
            $afterStyle = $matches[4];
            
            // Check if the element already has a class attribute
            if (preg_match('/class\s*=\s*["\']([^"\']*)["\']/', $beforeStyle, $classMatches)) {
                // Add customcolors to existing class
                $existingClasses = $classMatches[1];
                if (strpos($existingClasses, 'customcolors') === false) {
                    $newClasses = trim($existingClasses . ' customcolors');
                    $beforeStyle = str_replace($classMatches[0], 'class="' . $newClasses . '"', $beforeStyle);
                }
            } else {
                // Add class attribute with customcolors
                $beforeStyle .= ' class="customcolors"';
            }
            
            // Remove the color: redgrad style, but keep other styles if they exist
            $remainingStyles = trim($styleBeforeColor . $styleAfterColor);
            if (!empty($remainingStyles)) {
                $styleAttr = ' style="' . $remainingStyles . '"';
            } else {
                $styleAttr = '';
            }
            
            return $beforeStyle . $styleAttr . $afterStyle;
        }, $value);
    }
}
