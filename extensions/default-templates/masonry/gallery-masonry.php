<?php
/**
 * FooGallery masonry gallery template
 */
global $current_foogallery;
global $current_foogallery_arguments;
$width = foogallery_gallery_template_setting( 'thumbnail_width', '150' );
$gutter_width = foogallery_gallery_template_setting( 'gutter_width', '10' );
$date_sep = foogallery_gallery_template_setting('date_separators', false );
if ($date_sep === 'none') {
  $date_sep = false;
}
$date_caption = foogallery_gallery_template_setting('date_caption', false);
if ($date_caption === 'no') {
  $date_caption = false;
}
$center_align = 'center' === foogallery_gallery_template_setting( 'center_align', false );
$hover_zoom_class = 'default' === foogallery_gallery_template_setting( 'hover_zoom', 'default' ) ? 'foogallery-masonry-hover-zoom-default' : '';
$layout = foogallery_gallery_template_setting( 'layout', 'fixed' );
$gutter_percent = foogallery_gallery_template_setting( 'gutter_percent', '' );
$args = array(
	'width' => $width,
	'link' => foogallery_gallery_template_setting( 'thumbnail_link', 'image' ),
	'crop' => false,
);
$lightbox = foogallery_gallery_template_setting( 'lightbox', 'unknown' );
$small_screen = $width + $gutter_width + $gutter_width;

?>
<style>
	#foogallery-gallery-<?php echo $current_foogallery->ID; ?>.masonry-layout-fixed .item {
		margin-bottom: <?php echo $gutter_width; ?>px;
		width: <?php echo $width; ?>px;
	}
	#foogallery-gallery-<?php echo $current_foogallery->ID; ?>.masonry-layout-fixed .masonry-item-width {
		width: <?php echo $width; ?>px;
	}

	#foogallery-gallery-<?php echo $current_foogallery->ID; ?>.masonry-layout-fixed .masonry-gutter-width {
		width: <?php echo $gutter_width; ?>px;
	}

	<?php if ( $center_align && 'fixed' === $layout ) { ?>
	#foogallery-gallery-<?php echo $current_foogallery->ID; ?> {
		margin: 0 auto;
	}
	<?php } ?>
</style>
<?php
$per_pos = 'fixed' === $layout ? '' : '"percentPosition": "true", ';
$foo_gal_id = $current_foogallery->ID;
$prefix = "<div data-masonry-options='{ ";
$prefix .= ' "itemSelector" : ".item", ';
if ('fixed' === $layout) {
  $prefix .= ' "percentPosition": "true", ';
}
$prefix .= ' "columnWidth" : "#foogallery-gallery-' . $foo_gal_id . ' .masonry-item-width", ';
$prefix .= ' "gutter" : "#foogallery-gallery-' . $foo_gal_id . ' .masonry-gutter-width", ';
$prefix .= ' "isFitWidth" : ' . (( $center_align && 'fixed' === $layout ) ? 'true' : 'false');
$prefix .= '}\'';
$prefix .= ' id="foogallery-gallery-' . $foo_gal_id . '"';
$prefix .= ' class="' . esc_attr(foogallery_build_class_attribute( $current_foogallery, 'foogallery-lightbox-' . $lightbox, $hover_zoom_class, 'masonry-layout-' . $layout, $gutter_percent, 'foogallery-masonry-loading' )) . '"';
$prefix .= '>';
$prefix .= '  <div class="masonry-item-width"></div>';
$prefix .= '  <div class="masonry-gutter-width"></div>';

$labels = array();
$atts = array();
$times = array();
$stuff = array();
$extra = array();

$ixmap = array();
$attachments = $current_foogallery->attachments();
if ($date_sep) {

  $ref_date = (new DateTime('@0'))->setTime(0, 0, 0);

  foreach ( $attachments as $attachment ) {
    $meta = wp_get_attachment_metadata($attachment->ID);
    $timestamp = $meta['image_meta']['created_timestamp'];
    $img_date = new DateTime("@$timestamp");
    $img_date_fmt = $img_date->format('Y-m-d H:i');
    $diff = (int)$ref_date->diff($img_date)->format("%r%a");
    $label = $img_date->format('F j, Y');
    if (array_key_exists($diff, $ixmap)) {
      $ix = $ixmap[$diff];
      $atts[$ix][] = count($stuff);
      $stuff[] = $attachment;
      $extra[$attachment->ID] = [$img_date_fmt, $img_date->getTimestamp()];
    } else {
      $ix = count($labels);
      $ixmap[$diff] = $ix;
      $times[$ix] = $img_date->getTimestamp();
      $labels[$ix] = $label;
      $atts[$ix] = [count($stuff)];
      $stuff[] = $attachment;
      $extra[$attachment->ID] = [$img_date_fmt, $img_date->getTimestamp()];
    }
  }
  $atts[] = [];
  $day_sep_cmp = function ($ga, $gb) {
    $a = $extra[$ga->ID][1];
    $b = $extra[$gb->ID][1];
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
  };
  foreach ($atts as $ix => &$as) {
    usort($as, $day_sep_cmp);
  }
  
  $grp_cmp = function ($ix1, $ix2) use ($times) {
    $v1 = $times[$ix1];
    $v2 = $times[$ix2];
    if ($v1 == $v2) {
      return 0;
    }
    return ($v1 > $v2) ? -1 : 1;
  };
  uasort($ixmap, $grp_cmp);
} else {
  foreach ( $attachments as $attachment ) {
    $meta = wp_get_attachment_metadata($attachment->ID);
    $timestamp = $meta['image_meta']['created_timestamp'];
    $img_date = new DateTime("@$timestamp");
    $img_date_fmt = $img_date->format('Y-m-d H:i');
    $atts[0][] = count(stuff);
    $stuff[] = $attachment;
    $extra[$attachment->ID] = [$img_date_fmt, $img_date->getTimestamp()];
  }
  $ixmap[0] = 0;
}
?>
<?php

  foreach ($ixmap as $day => $i) {
    $as = $atts[$i];
    $label = $labels[$i];
    if ($date_sep) {
      echo '<div class="foogallery-datesep">' . $label . '</div>';
    }
    echo $prefix;
    for ($j = 0; $j < count($as); $j++) {
                $attachment = $stuff[$as[$j]];
                $datetime = $extra[$attachment->ID];
		echo '	<div class="item">';
		echo $attachment->html( $args, true, false );
                if ($date_caption) {
                  echo '<div class="foogallery-datetime">';
                  echo '<div class="foogallery-datetime-inner">' . $datetime . '</div>';
                  echo '</div>';
                }
		echo $attachment->html_caption( 'title' );
		echo '</a>';
		echo '</div>';
    }
    echo '</div>';
  }
?>
