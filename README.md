# kiwi/contao-designer

Central design settings for Contao 5: a color palette, color schemes, backgrounds, CTA styles
and headline options, assigned to content in the backend and rendered as classes, attributes
and CSS variables in the frontend.

Integrates with implementations of `kiwi/contao-responsive-base`, like `kiwi/contao-bootstrap`
to set backgrounds and schemes per breakpoint; without it they are single values.

## Features

- **Colors** (Design → Colors): the palette, written to `files/themes/_colorvars.scss`.
- **Color schemes** (Design → Color schemes): written to `files/themes/_schemes.scss`, assigned
  to layouts, articles and elements as `data-scheme="…"`.
- **Backgrounds** for articles and element groups: color, picture or video (see below).
- **CTA styles** for links and form submit buttons (button, outline button, text link).
- **Headlines**: topline, subline and a visual headline class independent of the tag.

With `kiwi/contao-bootstrap`, the generated `_colorvars.scss` and `_schemes.scss` are added to
the Bootstrap SCSS build automatically.

## Backgrounds

Out of the box, articles and element groups get a background field. Further content element types can be added
in your config:

```php
$GLOBALS['design']['tl_content']['background'][] = 'my_element';
```

### Styling: enable the bundle CSS or write your own

The bundle stylesheet is **not** loaded automatically: you need to enable
**Backgrounds** (`background_styles`) in the layout under *CSS framework*.

This makes it easy to opt out and provide your own CSS, should the provided default styles clash with your existing styles.

The markup to cover when writing your own styles:

```html
<!-- article: .mod_article__background, content element: .content__background -->
<div class="mod_article__background background background--article">
  <div class="background__element background__element--color" style="--background-color:…"></div>
  <!-- or -->
  <div class="background__element background__element--picture"><figure>…</figure></div>
  <!-- or, with "limit width" (and optionally "blur") -->
  <div class="background__element background__element--picture">
    <div class="background__element__layer background__element__layer--blurred" aria-hidden="true" style="--background-blur-brightness:…"><figure>…</figure></div>
    <div class="background__element__layer background__element__layer--limited" style="--background-image-ratio:1920 / 850"><figure>…</figure></div>
  </div>
  <!-- or -->
  <div class="background__element background__element--video"><video class="background__element__video">…</video></div>
</div>
```

### CSS variables

Some aspects of the bundle-provided styles can be adjusted via CSS variables. All are optional; set them in your theme (e.g. on
`:root`) to adjust the defaults.

| Variable | Default | Controls |
|---|---|---|
| `--background-element-width` | `100%` | width of content element backgrounds (their own box) |
| `--background-article-width` | `--page-max`, else `100vw` | width of article backgrounds (centered on the article) |
| `--background-limit-min-width` | none | *limit image width*: minimum width of the sharp picture |
| `--background-limit-max-width` | none | *limit image width*: maximum width of the sharp picture |
| `--background-limit-width` | none | *limit image width*: shorthand for both; the specific variables take precedence |
| `--background-blur-radius` | `24px` | blur strength of the blurred version |

Set by the bundle (inline) and not meant to be set by themes: `--background-color`,
`--background-blur-brightness`, `--background-image-ratio`.

Note: `--page-max` is only read for backwards compatibility. New themes should set
`--background-article-width` directly instead.

### Limit image width

For pictures that should not stretch too wide (e.g. to avoid unfortunate cropping of people's heads), there is an option to **limit image width**.
By default, this preserves its own proportions at the full height of the background, so it is never cropped at the top or bottom. On narrower backgrounds it fills the full width and crops at the sides as usual. This requires the image dimensions to be known (e.g. not for SVGs without width and height); otherwise the image fills the width.
This behaviour can be modified via CSS variables:
- `--background-limit-max-width` limits the image's width and crops left and right, if it grows too large.
- `--background-limit-min-width` enforces a minimum width, even if that means cropping the image's top and bottom.
- `--background-limit-width` sets both at once; the two specific variables take precedence on their side.
Use the shorthand to define a uniform background width across the site, independent of image sizes and proportions.

The resulting empty space to the left and right can be filled with a blurred version of the original image, optionally darkened or lightened.

> *Yo dawg, I heard you like backgrounds, so now you can have a background for your background.* — Xzibit, allegedly

### Horizontal overflow fix

Full-width backgrounds use `100vw`, which includes the width of non-overlay vertical scrollbars. The bundle CSS therefore sets
`overflow-x: clip` on `body`, so the overhang does not cause horizontal scrolling. As a
consequence, anything wider than the page is cut off rather than scrollable: give wide content
(tables, embeds) its own `overflow-x: auto` wrapper. The rule has zero specificity; opt out with:

```css
body { overflow-x: visible; }
```

## Known limitations

### Backgrounds are only rendered by Twig templates

Backgrounds are output by the bundle's `mod_article.html.twig` and by the content element base
template (`content_element/_base.html.twig`). Contao only uses a bundle's Twig template if the
project does not provide a legacy `.html5` template of the same name, so:

- `templates/mod_article.html5` in a project disables article backgrounds (and the Twig
  additions of other bundles, e.g. the container classes of `kiwi/contao-responsive-base`).
- Custom article templates such as `mod_article_custom.html5` render no background either, also
  when they `$this->extend('mod_article')`.

Write overrides as Twig and extend the bundle template, changing only the blocks you need:

```twig
{# templates/mod_article.html.twig #}
{% extends "@Contao/mod_article.html.twig" %}
```

If a legacy template cannot be avoided, output what the bundle template adds yourself: the
background with its wrapper, the `mod_article--background` class (the bundle CSS relies on it)
and the color scheme attribute:

```php
<?php
$designer = \Contao\System::getContainer()->get('kiwi.contao.designer.frontend');
$hasBackground = $this->background && $designer->hasBackground($this->background);
?>
<div class="<?= $this->class ?><?= $hasBackground ? ' mod_article--background' : '' ?> block"<?= $this->cssID ?> <?= $designer->getGlobalStrings($this->getData(), 'scheme') ?>>
  <?php if ($hasBackground): ?>
    <div class="mod_article__background background background--article">
      <?= $designer->getGlobalStrings($this->getData(), 'backgroundElement', 'background') ?>
    </div>
  <?php endif; ?>
  <!-- … article content … -->
</div>
```

The bundle template also repeats the scheme attribute on the inner `mod_article__main` wrapper
and handles the dynamic image override (`backgroundOverwrite`); both are left out here.

### Legacy content elements

Content element types added to `$GLOBALS['design']['tl_content']['background']` must be
Twig-based elements. Legacy elements get the background field in the backend, but nothing renders
it.

### Backgrounds require `kiwi/contao-responsive-base`

Without a responsive-base implementation (e.g. `kiwi/contao-bootstrap`), the background field is
a plain select with only a file or color picker, and the frontend cannot render it, for any
background type:

- `hasBackground()`, which the templates call first, expects per-breakpoint data and throws a
  `TypeError` for the plain value. Articles and elements with any background option selected
  (including *none*) fail to render.
- Beyond that, the markup expects the field names of the responsive version (`image`, `video`,
  `poster`, `size`), and the `{{modifiers}}` placeholder stays in the output.

The *limit image width* options are only available in the responsive version as well.
