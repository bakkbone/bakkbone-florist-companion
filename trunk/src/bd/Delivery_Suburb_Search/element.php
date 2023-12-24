<?php

namespace bkfCustomElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


/**\Breakdance\ElementStudio\registerElementForEditing(
    "bkfCustomElements\\DeliverySuburbSearch",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);**/

class DeliverySuburbSearch extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'SearchIcon';
    }

    static function tag()
    {
        return 'div';
    }

    static function tagOptions()
    {
        return [];
    }

    static function tagControlPath()
    {
        return false;
    }

    static function name()
    {
        return esc_html__('Delivery Suburb Search', 'bakkbone-florist-companion');
    }

    static function className()
    {
        return 'bkf-bd-deliverysuburbsearch';
    }

    static function category()
    {
        return 'bkf';
    }

    static function badge()
    {
        return ['label' => esc_html__('FloristPress', 'bakkbone-florist-companion'), 'backgroundColor' => 'var(--red900)', 'textColor' => 'var(--white)'];
    }

    static function slug()
    {
        return get_class();
    }

    static function template()
    {
        return file_get_contents(__DIR__ . '/html.twig');
    }

    static function defaultCss()
    {
        return file_get_contents(__DIR__ . '/default.css');
    }

    static function defaultProperties()
    {
        return ['design' => ['element' => ['size' => ['width' => ['breakpoint_base' => ['number' => 100, 'unit' => '%', 'style' => '100%']]]], 'input_styles' => ['spacing' => ['padding' => ['breakpoint_base' => ['top' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'left' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'right' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'bottom' => ['number' => 10, 'unit' => 'px', 'style' => '10px']]]]], 'input_typography' => ['typography' => ['typography' => ['custom' => ['customTypography' => ['fontFamily' => ['breakpoint_base' => 'gfont-oxygen'], 'fontSize' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']], 'fontWeight' => ['breakpoint_base' => '400']]]]]], 'result_box_styles' => ['size' => ['width' => ['breakpoint_base' => ['number' => 100, 'unit' => '%', 'style' => '100%']]], 'background' => '#FFFFFFFF', 'spacing' => ['padding' => ['breakpoint_base' => ['top' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'left' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'right' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'bottom' => ['number' => 10, 'unit' => 'px', 'style' => '10px']]]], 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'topLeft' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'topRight' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'bottomLeft' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'bottomRight' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'editMode' => 'all']], 'border' => ['breakpoint_base' => ['top' => ['width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'color' => '#A5ACB2', 'style' => 'solid'], 'bottom' => ['width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'color' => '#A5ACB2', 'style' => 'solid'], 'left' => ['width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'color' => '#A5ACB2', 'style' => 'solid'], 'right' => ['width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'color' => '#A5ACB2', 'style' => 'solid']]], 'shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#222222FF', 'x' => '5', 'y' => '5', 'blur' => '10', 'spread' => '0', 'position' => 'outset']], 'style' => '5px 5px 10px 0px #222222FF']]]], 'result_item_styles' => ['background_hover' => '#EEEEEE', 'borders' => ['radius' => ['breakpoint_base' => null], 'border' => ['breakpoint_base' => ['top' => ['width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'color' => '#A5ACB2', 'style' => 'solid'], 'bottom' => ['width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'color' => '#A5ACB2', 'style' => 'solid'], 'left' => ['width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'color' => '#A5ACB2', 'style' => 'solid'], 'right' => ['width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'color' => '#A5ACB2', 'style' => 'solid']]]], 'spacing' => ['margin' => ['breakpoint_base' => ['top' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'left' => ['number' => 0, 'unit' => 'px', 'style' => '0px'], 'right' => ['number' => 0, 'unit' => 'px', 'style' => '0px'], 'bottom' => ['number' => 5, 'unit' => 'px', 'style' => '5px']]], 'padding' => ['breakpoint_base' => ['top' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'left' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'right' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'bottom' => ['number' => 5, 'unit' => 'px', 'style' => '5px']]]]], 'result_typography' => ['heading' => ['typography' => ['custom' => ['customTypography' => ['fontFamily' => ['breakpoint_base' => 'gfont-oxygen'], 'fontSize' => ['breakpoint_base' => ['number' => 24, 'unit' => 'px', 'style' => '24px']]]]]], 'result_title' => ['typography' => ['custom' => ['customTypography' => ['fontFamily' => ['breakpoint_base' => 'gfont-oxygen'], 'fontSize' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]]]], 'result_details' => ['typography' => ['custom' => ['customTypography' => ['fontFamily' => ['breakpoint_base' => 'gfont-oxygen'], 'fontSize' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']]]]]]], 'no_results_message' => ['typography' => ['typography' => ['custom' => ['customTypography' => ['fontFamily' => ['breakpoint_base' => 'gfont-oxygen'], 'fontSize' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']], 'fontWeight' => ['breakpoint_base' => '600']]]]]]], 'content' => ['text' => ['placeholder' => esc_html__('Start typing a suburb to check the delivery cost...', 'bakkbone-florist-companion'), 'no_results' => esc_html__('No suburbs matched your search.', 'bakkbone-florist-companion'), 'results_header' => esc_html__('We deliver to these suburbs matching your search:', 'bakkbone-florist-companion')]]];
    }

    static function defaultChildren()
    {
        return false;
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [c(
        "element",
        esc_html__("Element", 'bakkbone-florist-companion'),
        [getPresetSection(
      "EssentialElements\\size",
      esc_html__("Size", 'bakkbone-florist-companion'),
      "size",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_all",
      esc_html__("Spacing", 'bakkbone-florist-companion'),
      "spacing",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "input_styles",
        esc_html__("Input Styles", 'bakkbone-florist-companion'),
        [c(
        "background",
        esc_html__("Background", 'bakkbone-florist-companion'),
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        true,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      esc_html__("Borders", 'bakkbone-florist-companion'),
      "borders",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_all",
      esc_html__("Spacing", 'bakkbone-florist-companion'),
      "spacing",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "input_typography",
        esc_html__("Input Typography", 'bakkbone-florist-companion'),
        [getPresetSection(
      "EssentialElements\\typography",
      esc_html__("Typography", 'bakkbone-florist-companion'),
      "typography",
       ['type' => 'popout']
     ), c(
        "placeholder",
        esc_html__("Placeholder", 'bakkbone-florist-companion'),
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'accordion']],
        false,
        false,
        [],
      ), c(
        "result_box_styles",
        esc_html__("Result Box Styles", 'bakkbone-florist-companion'),
        [getPresetSection(
      "EssentialElements\\size",
      esc_html__("Size", 'bakkbone-florist-companion'),
      "size",
       ['type' => 'popout']
     ), c(
        "background",
        esc_html__("Background", 'bakkbone-florist-companion'),
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        true,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      esc_html__("Borders", 'bakkbone-florist-companion'),
      "borders",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_all",
      esc_html__("Spacing", 'bakkbone-florist-companion'),
      "spacing",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "result_item_styles",
        esc_html__("Result Item Styles", 'bakkbone-florist-companion'),
        [c(
        "background",
        esc_html__("Background", 'bakkbone-florist-companion'),
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_all",
      esc_html__("Spacing", 'bakkbone-florist-companion'),
      "spacing",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\borders",
      esc_html__("Borders", 'bakkbone-florist-companion'),
      "borders",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "result_typography",
        esc_html__("Result Typography", 'bakkbone-florist-companion'),
        [getPresetSection(
      "EssentialElements\\typography",
      esc_html__("Heading", 'bakkbone-florist-companion'),
      "heading",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      esc_html__("Result Title", 'bakkbone-florist-companion'),
      "result_title",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      esc_html__("Result Details", 'bakkbone-florist-companion'),
      "result_details",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "no_results_message",
        esc_html__("No Results Message", 'bakkbone-florist-companion'),
        [getPresetSection(
      "EssentialElements\\typography",
      esc_html__("Typography", 'bakkbone-florist-companion'),
      "typography",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_all",
      esc_html__("Spacing", 'bakkbone-florist-companion'),
      "spacing",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      )];
    }

    static function contentControls()
    {
        return [c(
        "text",
        esc_html__("Text", 'bakkbone-florist-companion'),
        [c(
        "placeholder",
        esc_html__("Placeholder", 'bakkbone-florist-companion'),
        [],
        ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => false], 'variableItems' => []],
        false,
        false,
        [],
      ), c(
        "no_results",
        esc_html__("No Results", 'bakkbone-florist-companion'),
        [],
        ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => false], 'variableItems' => []],
        false,
        false,
        [],
      ), c(
        "results_header",
        esc_html__("Results Header", 'bakkbone-florist-companion'),
        [],
        ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => false], 'variableItems' => []],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
      )];
    }

    static function settingsControls()
    {
        return [];
    }

    static function dependencies()
    {
        return false;
    }

    static function settings()
    {
        return ['proOnly' => false, 'dependsOnGlobalScripts' => true, 'requiredPlugins' => ['0' => 'WooCommerce'], 'bypassPointerEvents' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return false;
    }

    static function nestingRule()
    {
        return ["type" => "final",   "notAllowedWhenNodeTypeIsPresentInTree" => ['bkfCustomElements\DeliverySuburbSearch'],];
    }

    static function spacingBars()
    {
        return [];
    }

    static function attributes()
    {
        return false;
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 0;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '1' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '2' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '3' => ['accepts' => 'string', 'path' => 'content.text.results_header', 'proOnly' => true], '4' => ['accepts' => 'string', 'path' => 'content.text.placeholder', 'proOnly' => true], '5' => ['accepts' => 'string', 'path' => 'content.text.no_results', 'proOnly' => true], '6' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '7' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '8' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '9' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '10' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '11' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '12' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '13' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string'], '14' => ['accepts' => 'image_url', 'path' => 'design.result_item_styles.background.layers[].image'], '15' => ['path' => 'settings.advanced.attributes[].value', 'accepts' => 'string']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return false;
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return ['design.element.layout.horizontal.vertical_at', 'design.element.background.image', 'design.element.background.overlay.image', 'design.element.background.image_settings.unset_image_at', 'design.element.background.image_settings.size', 'design.element.background.image_settings.height', 'design.element.background.image_settings.repeat', 'design.element.background.image_settings.position', 'design.element.background.image_settings.left', 'design.element.background.image_settings.top', 'design.element.background.image_settings.attachment', 'design.element.background.image_settings.custom_position', 'design.element.background.image_settings.width', 'design.element.background.overlay.image_settings.custom_position', 'design.element.background.image_size', 'design.element.background.overlay.image_size', 'design.element.background.overlay.type', 'design.element.background.image_settings'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
