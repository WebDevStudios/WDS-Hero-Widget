
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

## Shortcode Example

```
[hero type="primary" id="my-id" class="my-class" video="http://mysite.com/my-video.mp4" heading="My Heading" sub_heading="My Sub Heading" button_text="My Button" button_link="http://webdevstudios.com" custom_content_action="my_content_action_filter" image="http://mysite.com/my-image.png" overlay="0.2" overlay_color="#fff" slider_id="2"]
    Additional Content
[/hero]
```

### Shortcode & Template Tag Arguments:

`type: primary|secondary|secondary-paralax`

The type of the hero. As of 1.0 we have 3: `primary`, `secondary`, and `secondary-paralax`.

`id: my-id`

Gives your heroes ID attributes:

```
<div id="my-id"...
```

`class: my-class`

Add classes to your heroes:

```
<div id="my-id" class="my-class"...
```

`video: http://mysite.com/my-video.mp4`

Place web-able videos in your heroes. Video must be a web-playable format.

`heading: My Heading`

Set the main heading.

`sub_heading: My Sub Heading`

Set the sub-heading.

`button_text: My Button`

Set the button text.

`button_link: http://webdevstudios.com`

Set the button link (opens in current window).

`custom_content_action: my_action`

Set the action that is performed within the hero. You can then add actions
to this and add additional content within your hero.

```
add_action( 'my_action', 'do_this_thing_func' );
```

`image: http://mysite.com/my-image.png`

Set the background image of your hero.

`overlay: 0.2`

Set the overlay transparency.

`overlay_color: #fff`

Set the overlay color.

`slider_id: 2`

Set the slider post ID to pull in slider images.

## Styling

This plugin is intended to place basic Hero's on your site intended for
additional styling by a developer.

Though this plugin provides some basic styling, additional CSS will be required
to customize Hero's for your site. Headings in heroes, for instance, may have margins
inherited from the theme that cause centering to be off.

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
define( 'DISABLE_WDS_HERO_WIDGET', true );
```

### Disable Sliders CPT

If you aren't planning on using sliders, you can disable them completely using
the following in your `wp-config.php`:

```
define( 'DISABLE_WDS_SLIDER_CPT', true );
```

# Changelog

## 1.0

- Sliders now use `background-size: cover` to fit images in the container
- Disable Slider CPT/Sliders and Widget UI using filters
- Place Heros on page with heading, sub-heading, and call to action button
- Use shortcodes, template tags, or Widget UI
- Configure sliding images to use as the background of the Hero
- Customize content through shortcodes and custom filters
