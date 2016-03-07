
This plugin is currently under development...but is usable. 1.0 is almost ready
and should be wrapped up soon!

# WDS Hero Widget

This allows you to add "Heros" to pages with heading, subheading, and buttons.
Complete with image backgrounds, video and sliders.

## What is a Hero?

A Hero is a large area on a page where, usually, an image is shown very large
and sometimes content is in them.

### Example

![](https://cldup.com/U9FjyLmeic-2000x2000.png)

*That is a hero!*

## Styling

This plugin is intended to place basic Hero's on your site intended for
additional styling by a developer.

*Though this plugin provides some basic styling, additional CSS will be required
to customize Hero's for your site.*

### The plugin should provide:

- A large Hero area with whatever background, video, or slider you setup as the background
- A heading, sub-heading, and a call to action button

## Disabling Things

This plugin is designed, mainly, for the developer as a toolkit for heroes. So,
you may want to disable things on a per-project basis. This is how you disable
things.

### Disable Widget UI

You may want to place heroes manually and not give the user any
UI to do so. To disable the Widget UI add this to `wp-config.php`:

```
add_filter( 'disable_wds_hero_widget', '__return_true' );
```

### Disable Sliders CPT

If you aren't planning on using sliders, you can disable them completely using
the following in your `wp-config.php`:

```
add_filter( 'disable_wds_slider_cpt', '__return_true' );
```

# Changelog

## 1.0-dev (Currently in Development)

- Can place Heros on page with heading, sub-heading, and call to action button
- Can use using [https://github.com/WebDevStudios/WDS-Hero-Widget/blob/master/wds-hero-widget.php#L146](shortcodes, template tags), or Widgets
- Can configure sliding images to use as the background of the Hero
- Can customize content through shortcode and [custom filters](https://github.com/WebDevStudios/WDS-Hero-Widget/blob/master/class-wds-hero-widget.php#L190)
