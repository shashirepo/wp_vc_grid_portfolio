<div class="item block masonry-brick" data-bgimage="<?php echo $vc_fportfolio_bgimage; ?>" style="position: absolute; top: 0px; left: 305px;">
				<div class="thumbs-wrapper" style="width: 260px;">
					<div class="thumbs" style="width: 520px; margin-left: 0px;">
					<?php foreach ($vc_fportfolio_thumbnails as $key => $thumbs): ?>
						<img width="260" height="173" src="<?php echo $thumbs ?>">
					<?php endforeach; ?>
					</div><div class="thumbs-nav"><span class="thumbs-nav-prev">Previous</span><span class="thumbs-nav-next" style="display: inline;">Next</span></div>
				</div>
				<h2 class="title"><?php echo $vc_fportfolio_type; ?></h2>
				<p class="subline"><?php echo $vc_fportfolio_title; ?></p>
				<div class="intro">
					<p><?php if(strlen($content) > 120): echo strip_tags(substr($content, 0, 120)).'...'; else: echo strip_tags($content); endif; ?><a href="#" class="more_link">View project</a></p>
				</div>
				<div class="project-descr">
					<?php echo $content; ?>
				</div>
			</div>