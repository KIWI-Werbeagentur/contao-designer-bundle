# kiwi/contao-designer

Contao 5.3 Bundle für zentrale Design-Verwaltung: Farbpalette, Farbschemata, Hintergründe und CTA-Styling.

**Anforderungen:** PHP ^8.3, Contao ^5.3, `kiwi/contao-cmx`

---

## Funktionen

- Verwaltet eine globale **Farbpalette** (`tl_color`) und schreibt daraus `_colorvars.scss`
- Definiert **Farbschemata** (`tl_color_scheme`) und schreibt daraus `_schemes.scss`
- Erweitert Content-Elemente, Artikel und Form-Submit-Buttons um **Hintergrund- und CTA-Felder**
- Erweitert alle Paletten mit `headline`-Feld um **Topline / Subline / Headline-Class**
- Stellt **Twig-Funktionen** zur Design-Auflösung in Templates bereit

---

## Installation

```bash
composer require kiwi/contao-designer
php bin/console contao:migrate
```

---

## Farbpalette

### Backend

`Design → Farbpalette` — Farben anlegen mit:
- `variable` (nur `[a-z-]`) → wird zum SCSS-Variablennamen
- `value` — Hex, RGB, RGBA, HSL, HSLA oder HTML-Farbnamen
- `category` — Verwendungszweck: `cta` / `background`
- `isApplicable` — Checkbox, ob die Farbe in Auswahlfeldern erscheint

Jede Änderung im Backend schreibt `files/themes/_colorvars.scss` neu:

```scss
$primary: #05975f;
$secondary: #72777a;

$colors: (
  "primary": $primary,
  "secondary": $secondary,
);

:root {
  --color-primary: #05975f;
  --color-secondary: #72777a;
}
```

### SCSS → DB Sync (Migration)

Die Migration `SyncColorVars` liest `:root { --color-*: … }` aus `_colorvars.scss` und synchronisiert `tl_color`:
- Neue Variablen → INSERT
- Geänderte Werte → UPDATE
- Entfernte Variablen → DELETE

Deaktivierbar per Umgebungsvariable:

```dotenv
DESIGNER_BUNDLE_COLOR_SYNC=false
```

---

## Farbschemata

### Konfiguration

Vor Nutzung müssen die Schema-Felder global definiert werden (z. B. im eigenen Bundle):

```php
// contao/config/config.php
$GLOBALS['scheme']['fields'] = ['text', 'background', 'accent'];
```

Jeder Eintrag erzeugt in `tl_color_scheme` zwei Felder:
- `text` → Farbauswahl (Color-ID)
- `textBrightness` → Helligkeitsanpassung 0–100 % (optional, leer = keine Anpassung)

### Generiertes SCSS

`files/themes/_schemes.scss` enthält einen `@mixin scheme($scheme)` sowie Selektoren für Layout- und Element-Ebene:

```scss
// Für body (basierend auf Layout-Zuweisung)
.body--theme-alias--layout-alias {
  @include scheme(dark);
}

// Für einzelne Elemente
[data-scheme="dark"] {
  @include scheme(dark);
}
```

### Hook zum Modifizieren des generierten SCSS

```php
$GLOBALS['TL_HOOKS']['alterSchemesScss'][] = [MyClass::class, 'myMethod'];
// Signatur: function(string $scssContent): string
```

---

## DCA-Erweiterungen

### Welche Tabellen werden erweitert

| Tabelle | Felder |
|---|---|
| `tl_content` | `background`, `scheme`, `backgroundOverwrite`, `topline`, `subline`, `headlineClass`, `isCta`, `ctaColor`, `ctaDesign` |
| `tl_article` | `background`, `scheme`, `backgroundOverwrite` |
| `tl_form_field` | `isCta`, `ctaColor`, `ctaDesign` (nur Palette `submit`) |
| `tl_module` | `customTpl` Callback |
| `tl_layout` | `scheme`, Framework-Optionen `color_styles` / `background_styles` |
| `tl_theme` | Init-Callback erstellt `_colorvars.scss` beim ersten Speichern |

### Hintergrund-Felder (`background`, `scheme`)

Standardmäßig werden die Felder in die Paletten aus `$GLOBALS['design']['tl_content']['background']` eingefügt:

```php
$GLOBALS['design']['tl_content']['background'] = ['element_group']; // Standard
```

Weitere Paletten-Gruppen einfach ergänzen.

### Hintergrund-Typen

| Typ | Subpaletten-Felder |
|---|---|
| `none` | — |
| `color` | `color` |
| `picture` | `image`, `size` |
| `video` | `video`, `poster` |

Zusätzlich: `backgroundOverwrite` (Checkbox) → dynamischer Bild-Override aus einer anderen DB-Tabelle via URL-Parameter.

---

## Design-Globals

Das Bundle nutzt `$GLOBALS['design']` als Template-String-System mit `{{variable}}`-Platzhaltern. Die Defaults sind in `contao/config/config.php` definiert und können im eigenen Bundle überschrieben werden.

### Wichtige Einträge

```php
// CTA-Button-Klassen
$GLOBALS['design']['ctaDesign'] = [
    'btn'         => 'btn btn-{{ctaColor}}',
    'btn-outline' => 'btn btn-outline-{{ctaColor}}',
    'link'        => 'textlink textlink-{{ctaColor}}',
];

// CSS Custom Properties für Hintergründe
$GLOBALS['design']['background'] = [
    'none'    => '--background{{modifier}}:none;',
    'color'   => '--background{{modifier}}:var(--color-{{color}});...',
    'picture' => "--background{{modifier}}:url('{{image}}');",
    'video'   => "--background{{modifier}}:url('{{video}}');",
];

// HTML für Hintergrund-Elemente
$GLOBALS['design']['backgroundElement'] = [
    'none'    => '',
    'color'   => "<div class='background__element background__element--color' ...>",
    'picture' => "<div class='background__element background__element--picture' ...>",
    'video'   => "<div class='background__element background__element--video' ...>",
];

// Schema-Attribut
$GLOBALS['design']['scheme'] = 'data-scheme{{modifier}}={{scheme}}';

// Headline-Klassen-Optionen
$GLOBALS['design']['headlineClass'] = ['h1','h2','h3','h4','h5','h6','display-1',...,'display-6'];

// Farbkategorien für die Synchronisation
$GLOBALS['design']['color']['categories'] = ['cta', 'background'];
```

---

## Twig-Funktionen

Alle Methoden des `DesignerFrontendService` sind in Twig verfügbar:

```twig
{# Gibt aufgelösten String aus $GLOBALS['design'][mapping] zurück #}
{{ getClasses(data, 'scheme') }}
{# → z. B. 'data-scheme=dark' #}

{{ getClasses(data, 'background', 'backgroundElement') }}
{# → z. B. '<div class="background__element ..." style="...">' #}

{# CTA-Klassen für Widget-Buttons #}
{{ getCtaClasses(data) }}
{# → z. B. 'btn btn-primary' #}

{# Prüft ob background !== 'none' gesetzt ist #}
{% if hasBackground(data.background) %}

{# CSS-Variablenname für eine Color-ID #}
{{ getColorVar(data.ctaColor) }}
{# → z. B. 'primary' #}

{# Body-Klasse aus Theme + Layout #}
{{ getThemeAndLayout() }}
{# → z. B. 'main--default' #}
```

### Hook: Design-Werte anpassen

```php
$GLOBALS['TL_HOOKS']['resolveDesignValues'][] = [MyClass::class, 'myMethod'];
// Signatur: function(string $name, string &$value, array $data): void
```

---

## SCSS-Dateien einbinden

Im Layout unter **Framework** die Optionen aktivieren:

- `color_styles` → lädt `/bundles/kiwidesigner/colors.scss` (Bootstrap-Farbvariablen)
- `background_styles` → lädt `/bundles/kiwidesigner/background.scss`

Bei Nutzung von `kiwi/contao-bootstrap` werden `_colorvars.scss` und `_schemes.scss` automatisch über `AlterBootstrapImports` in den Bootstrap-Build eingespielt.

---

## CSS-Klassen & Attribute (Übersicht)

| Element | Klasse / Attribut |
|---|---|
| `<body>` | `.body--{theme}--{layout}` |
| Artikel mit Hintergrund | `.mod_article--background` |
| Hintergrund-Element | `.background__element--color / --picture / --video` |
| Schema-Attribut | `data-scheme="{alias}"` |
| Topline | `.topline` |
| Subline | `.subline` |

---

## Models

```php
use Kiwi\Contao\DesignerBundle\Models\ColorModel;

// Alle verwendbaren Farben einer Kategorie
$colors = ColorModel::findApplicable('cta');
// gibt ColorModel-Collection zurück, gefiltert nach isApplicable=1 + category enthält 'cta'
```