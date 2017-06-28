# wordpress-lightbox-gallery
This is a simple WordPress plugin that replaces the default behavior of WordPress' gallery shortcode to open photos in a lightbox. At this time the lightbox gallery only works for images.

It utilizes the [Featherlight](https://noelboss.github.io/featherlight/) jQuery plugin and [Gallery extension](https://noelboss.github.io/featherlight/gallery.html) created by Noël Bossart.


## Installation
To install, upload the entire `lightbox-photo-gallery` folder to the `/wp-content/plugins/` directory. You can then activate it in the Plugins section in WordPress.


## Basic Usage
Once the plugin is activated, any galleries you create will now open in a lightbox. This affects both new and previously created galleries.

To add a gallery, open the edit screen for the desired page or post. Click the "Add Media" button and select "Create Gallery" from the sidebar on the left. From here you can select images you've already uploaded to your site or upload new images. Select all of the images you want displayed in your gallery, then click the "Create a new gallery" button at the bottom.

On the "Edit Gallery" page you can drag and drop images to rearrange them, add captions and/or alt text to each image, or change any of the following settings:

| Setting Name  | Options                             | Notes                    |
| ------------- | ----------------------------------- | ------------------------ |
| Link To       | Attachment Page, Media File, None   | This field doesn't have any effect since we're opening the images in a lightbox. |
| Columns       | 1-9                                 | The number of columns to display the thumbnails in for desktop (>= 1024px). Tablet automatically gets two columns and mobile gets one column.    |
| Random Order  | Checkbox                            | If checked, the thumbnails will be displayed in a random order. |
| Size          | Thumbnail, Medium, Large, Full Size | The size to display for the thumbnail. The image displayed in the lightbox will be full size. |


## Theme Customization
At this time, the only customization options available are through CSS overrides. You can find the plugin's CSS at `/lightbox-photo-gallery/css/theme.css`, although you should only use this for reference. **Any changes you want to make should be made in your own theme's CSS file or you risk losing your customizations when the plugin is updated.**

You will need to either use a more specific selector or use the `!important` rule for any overrides:
```
/* Example of a more specific selector */
.blog .lbpg-gallery__caption {
    font-size: 1em;
}

/* Example of using the !important rule */
.lbpg-gallery__caption {
    font-style: normal !important;
}
```


## Uninstallation
If you choose you no longer wish to use the plugin, simply deactivate and then delete it in the Plugins section in WordPress. After uninstalling, the original WordPress gallery shortcode will be restored, leaving your content intact. Doing this reverts to how the original gallery shortcode operates, so your gallery images will no longer open in a lightbox and may look slightly different.

## Questions? Comments? Issues?
You can use the Issues tab at the top of the page to leave questions, bug lists, or feature requests. You can also tweet any comments at [@praliedutzel](http://twitter.com/praliedutzel). Thanks for checking this out!

> Current stable build: v1.0
