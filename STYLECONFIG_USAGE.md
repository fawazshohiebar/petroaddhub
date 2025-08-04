# StyleConfig Fieldtype Example

## Usage in Blueprint

Here's how to use the new StyleConfig fieldtype in your blueprints:

```yaml
title: Example Blueprint with StyleConfig
sections:
  main:
    display: Main Content
    fields:
      -
        handle: section_styling
        field:
          type: style_config
          display: 'Section Styling'
          instructions: 'Configure the visual styling for this section'
          available_padding_sizes:
            - none
            - small
            - large
            - xlarge
          default_padding: small
          available_colors:
            - transparent
            - primary
            - secondary
            - black
            - white
            - gray
          default_color: transparent
          variant_labels: |
            style1|Modern Card
            style2|Classic Box
            style3|Minimal Frame
          default_variant: style1
```

## Frontend Usage

In your Antlers templates, you can access the styling configuration:

```antlers
{{ section_styling }}
  <div class="{{ css_classes }}">
    <p>Padding: {{ padding }}</p>
    <p>Color: {{ color }}</p>
    <p>Variant: {{ variant }}</p>
  </div>
{{ /section_styling }}
```

The fieldtype automatically generates CSS classes based on the configuration:
- Padding: `padding-{size}` (e.g., `padding-large`)
- Color: `color-{color}` (e.g., `color-primary`)
- Variant: `variant-{variant}` (e.g., `variant-style1`)

## Custom CSS Classes

You can define CSS rules that match the generated classes:

```css
/* Padding classes */
.padding-small { padding: 1rem; }
.padding-large { padding: 2rem; }
.padding-xlarge { padding: 3rem; }

/* Color classes */
.color-primary { background-color: var(--color-primary); }
.color-secondary { background-color: var(--color-secondary); }
.color-black { background-color: #000; color: #fff; }
.color-white { background-color: #fff; color: #000; }
.color-gray { background-color: #6b7280; }

/* Variant classes */
.variant-style1 { border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
.variant-style2 { border: 2px solid #e5e7eb; }
.variant-style3 { border-bottom: 2px solid #3b82f6; }
```
