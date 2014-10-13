<?php include(NX_PATH.'media/templates/head.html.php'); ?>

<div class="header">
	<span><?php p($project->getName());?></span>
		
</div>

<div id="editmenu"></div>
<div class="navigation">
	<a class="index" href="?"><img src="media/img/back.svg" /></a>
	<span class="share_project">	
		<?php if($project->isShared()) {?>
			
			<a class="publicUrl" href="?<?php p($project->getName().'&'.$project->getSharekey());?>">
				share this url
			</a>
			<a href="#" id="unshareProject">unshare</a>
		<?php } else { ?>
			<a href="#" id="shareProject">Generate shareable link</a>
		<?php } ?>
	</span>

	
</div>
		
<progress id="uploadprogress" min="0" max="100" value="0" >0</progress>
<div id="project" class="pcontent">
	<article>
		<div id="holder">
			<input id="pup" class="knob" data-width="100" data-angleOffset="0" data-fgColor="#81DCDD" data-bgColor="#FFFFFF" data-thickness=".05" value="0">
		</div>
		<p id="upload" class="hidden">
			<span class="cameraupload"></span>
			<input id="uup" class="upload" type="file"/>
		</p>
		<p id="filereader"></p>
		<p id="formdata"></p>
		<p id="progress"></p>
	</article>

	<div class="activeProject"><?php p($project->getName());?></div>
	
	<div id="newMarkdown" class="row">
		<div class="c3 edit-mode">
			<p class="date">preview</p>
			<div class="ncontent"><div class="content"><div class="markdown"></div></div></div>
		</div>
		<div class="c3edit" style="display:inline-block;">
			<textarea id="addfield" class="editareafield" placeholder="start writing markdown here" ></textarea>
			<div class="addPost"></div>
			<div class="discardAdd"></div>
			<div class="backup"></div>
		</div>
	</div>

	<?php foreach($nodes as $node) { ?>
		<div class="row node-<?php p($node->type);?>" data-name="<?php p($node->getName());?>">
			<div class="snap-drawers">
				<div class="snap-drawer snap-drawer-right">
					<div class="edit m-sub e"></div>
					<div class="delete m-sub d"></div>
				</div>
			</div>
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
					
					<div class="actions">
						<?php if( $node->editable ) { ?>
							<div class="edit-big" data-target="r"></div>
						<?php } ?>
						<div class="download-big"></div>
						<div class="delete-big"></div>
					</div>
		 		</div>
			</div>
			<?php if( $node->editable ) { ?>
				<div class="c3edit">
					<textarea class="editareafield"></textarea>
					<div class="save"></div>
					<div class="discardUpdate"></div>
					<div class="backup"></div>
				</div>
			<?php } ?>
		</div>

	<?php } ?>
</div>

<?php include(NX_PATH.'media/templates/foot.html.php'); ?>

