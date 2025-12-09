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

// Also run when new content is dynamically added (useful for AJAX content)
const observer = new MutationObserver(() => {
    convertredgradColors();
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});

Alpine.start();