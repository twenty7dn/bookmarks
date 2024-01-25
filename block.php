<?php
$lazyload = is_admin() ? false : false;
$substitute = array(
	'source' => get_field('source'),
	'title' => get_field('title'),
	'subtitle' => get_field('subtitle'),
	'excerpt' => get_field('excerpt'),
	'author' => get_field('author'),
	'site' => get_field('site'),
	'thumbnail' => get_field('thumbnail'),
	'icon' => get_field('icon')
);
$bookmark = new Bookmark();
$imgix = new Imgix();
$data = $bookmark->get_data( get_field('source'), $substitute ); ?>
<figure class="bookmark-card">
	<div class="bookmark-container<?php echo $data->thumbnail ? ' has-thumbnail' : ''; ?>">
		<div class="bookmark-content">
			<div class="bookmark-title"><a href="<?php echo $data->source; ?>" data-rel="nofollow noindex ignore" target="_blank"><?php echo $data->title; ?></a><?php echo $data->subtitle ? '&nbsp;&nbsp;<span class="bookmark-subtitle">' . $data->subtitle . '</span>' : ''; ?></div>
			<div class="bookmark-description"><?php echo $data->excerpt; ?></div>
			<div class="bookmark-metadata">
				<?php echo $imgix->get_remote_image( $data->icon, array(
					array(
						'w' => 20,
						'h' => 20
					)
				), array( 'bookmark-icon' ), $data->site, 3, false ); ?>
				<?php echo isset( $data->author ) && $data->author != ' ' ? '<span class="bookmark-author">' . $data->author . '</span>' : ''; ?>
				<span class="bookmark-publisher"><?php echo $data->site; ?></span>
			</div>
		</div>
		<?php if ( isset( $data->thumbnail ) ) { ?>
			<div class="bookmark-thumbnail">
			<?php echo $imgix->get_remote_image( $data->thumbnail, array(
					'(max-width: 1023px) and (orientation: portrait)' => array(
						'w' => 512,
						'h' => 128
					),
					'(max-width: 1023px) and (orientation: landscape)' => array(
						'w' => 128,
						'h' => 128
					),
					'(min-width: 1024px)' => array(
						'w' => 128,
						'h' => 128
					)
				), array(), $data->title, 3, false ); ?>
			</div>
		<?php } ?>
	</div>
</figure>