<?php include(NX_PATH.'media/templates/head.html.php'); ?>

<div class="header">
	<span><?php p($project->getName());?></span>
</div>
		
<progress id="uploadprogress" min="0" max="100" value="0" >0</progress>
<div id="project" class="pcontent">

	<div class="activeProject"><?php p($project->getName());?></div>

	<?php foreach($nodes as $node) { ?>
		<div class="row node-<?php p($node->type);?>" data-name="<?php p($node->getName());?>">
			<div class="snap-content c3">
				<p class="date">
					<?php p(date(CONFIG::DATE_FORMAT, $node->getTimestamp()));?>
				</p>
				<div class="ncontent">
					<?php if( $node instanceof NodeText ) {?>
						<div class="content">
							<div class="markdown"><?php p($node->getContent());?></div>
						</div>
					<?php } else if ( $node instanceof NodeImage ) {?>
						<a href="<?php p($node->getOriginalPath());?>">
							<img src="<?php p($node->getPath());?>"/>
						</a>
					<?php } ?>
		 		</div>
			</div>
		</div>

	<?php } ?>
</div>

<?php include(NX_PATH.'media/templates/foot.html.php'); ?>

