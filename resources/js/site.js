import Alpine from 'alpinejs'
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';
 
import Precognition from 'laravel-precognition-alpine';
import intersect from '@alpinejs/intersect'
import collapse from '@alpinejs/collapse'
import '@tailwindplus/elements';
 
window.Alpine = Alpine;
window.Swiper = Swiper;

Alpine.plugin(Precognition);
Alpine.plugin(intersect);
Alpine.plugin(collapse);

// Function to convert redgrad color styles to customcolors class
function convertredgradColors() {
    // Find all elements with style containing "color: redgrad"
    const elements = document.querySelectorAll('*[style*="color: redgrad"], *[style*="color:redgrad"]');
    
    elements.forEach(element => {
        const currentStyle = element.getAttribute('style');
        const currentClass = element.getAttribute('class') || '';
        
        // Remove color: redgrad from style
        const newStyle = currentStyle
            .replace(/color\s*:\s*redgrad\s*;?/gi, '')
            .replace(/;\s*;/g, ';') // Clean up double semicolons
            .replace(/^\s*;\s*/, '') // Remove leading semicolon
            .replace(/\s*;\s*$/, '') // Remove trailing semicolon
            .trim();
        
        // Add customcolors class if not already present
        if (!currentClass.includes('customcolors')) {
            element.setAttribute('class', currentClass ? `${currentClass} customcolors`.trim() : 'customcolors');
        }
        
        // Update or remove style attribute
        if (newStyle) {
            element.setAttribute('style', newStyle);
        } else {
            element.removeAttribute('style');
        }
    });
}

// Run on page load
document.addEventListener('DOMContentLoaded', convertredgradColors);

// Simple function to replace heroicon text with SVG icons
function replaceHeroicons() {
    // Find all paragraphs and divs that might contain heroicon text
    const elements = document.querySelectorAll('p, div.custom, li, td');
    let replacementCount = 0;
    
    elements.forEach(element => {
        const html = element.innerHTML;
        
        // Check if innerHTML contains the encoded heroicon pattern (even split across spans)
        if (html.includes('&lt;heroicon&gt;') && html.includes('&lt;/heroicon&gt;')) {
            // Use a more flexible regex that can handle tags and spans in between
            const newHTML = html.replace(
                /&lt;heroicon&gt;(?:<[^>]+>)*\[(?:<[^>]+>)*([^\]<>]+?)(?:<[^>]+>)*\](?:<[^>]+>)*&lt;\/heroicon&gt;/gi,
                (match, iconName) => {
                    // Clean up the icon name (remove any remaining HTML)
                    const cleanIcon = iconName.replace(/<[^>]+>/g, '').trim();
                    replacementCount++;
                    return getHeroiconSVG(cleanIcon);
                }
            );
            
            if (newHTML !== html) {
                element.innerHTML = newHTML;
            }
        }
    });
    
    if (replacementCount > 0) {
        console.log(`✅ Replaced ${replacementCount} heroicon(s)`);
    }
}

// Helper to generate SVG for heroicons
function getHeroiconSVG(iconName) {
    const icons = {
        'check-circle': 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'check': 'M4.5 12.75l6 6 9-13.5',
        'x-mark': 'M6 18L18 6M6 6l12 12',
        'x-circle': 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    };
    
    const path = icons[iconName.toLowerCase()];
    
    if (!path) {
        console.warn(`Icon "${iconName}" not found`);
        return `<span class="text-red-500">[${iconName}]</span>`;
    }
    
    return `<svg class="heroicon h-5 w-5 inline-block align-middle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="${path}"/></svg>`;
}

// Run after page loads
document.addEventListener('DOMContentLoaded', replaceHeroicons);

Alpine.start();