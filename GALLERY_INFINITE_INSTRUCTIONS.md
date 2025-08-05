# Gallery Infinite - Dual Row Sliding Implementation

## What Has Been Changed

The `gallery_infinite.antlers.html` file has been transformed from a static grid gallery into a modern dual-row sliding gallery with the following features:

### Key Features

1. **Two Horizontal Rows**: 
   - Top row slides from right to left
   - Bottom row slides from left to right (opposite direction)

2. **Fade Effect at Edges**: 
   - Gradient overlays on both left and right sides
   - Creates a seamless fade effect that makes images appear/disappear gradually

3. **Continuous Auto-Sliding**:
   - Images continuously scroll in opposite directions
   - Smooth linear animation using SwiperJS

4. **Interactive Features**:
   - Hover to pause animation
   - Click to view in lightbox (preserved from original)
   - Responsive design for all screen sizes

### Technical Implementation

**Dependencies Used:**
- SwiperJS (already installed in package.json)
- TailwindCSS (for styling)
- AlpineJS (for component state)

**CSS Classes Added:**
- Gradient fade effects: `bg-gradient-to-r` and `bg-gradient-to-l`
- Responsive slide widths
- Smooth transitions and animations

**JavaScript Functions:**
- `slidingGallery()`: Main Alpine.js component
- Two Swiper instances with opposite directions
- Hover pause/resume functionality
- Responsive breakpoints for different screen sizes

### How It Works

1. Each gallery row uses SwiperJS with infinite loop
2. First swiper moves normally (right to left)
3. Second swiper has `reverseDirection: true` (left to right)
4. Gradient overlays create fade effect at edges
5. Images duplicate automatically for seamless looping

### Customization Options

You can adjust these settings in the script section:

- **Speed**: Change `speed: 8000` (milliseconds for one complete slide)
- **Slide widths**: Modify breakpoints (320px, 768px, 1024px, 1280px)
- **Fade width**: Adjust `w-20` class on gradient overlays
- **Image heights**: Change `h-40 lg:h-60` classes

### Browser Compatibility

- Works on all modern browsers
- Mobile responsive
- Touch-friendly (though auto-sliding continues)

## Next Steps

1. Test the gallery on your website
2. Adjust speeds/sizes if needed
3. Consider adding pause/play controls if desired
4. Verify image loading performance

The gallery will automatically work with your existing `gallery_items` data structure from Statamic.
