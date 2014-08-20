<?php include(NX_PATH.'media/templates/head.html.php'); ?>

<div class="header"><?= strtoupper(CONFIG::SITENAME) ?></div>

<div class="project-list">
	<div id="addProject"></div>
		<form class="addProjectForm">
			<div><input type="text" name="name" id="newProject" placeholder="Project name:"/></div>
			<div><button type="submit" id="np" ></button></div>
		</form>

		<?php foreach($projects as $project) { ?>
			<a href="?<?php p($project->getName());?>">
				<div 
					data-name="<?php p($project->getName());?>"
					class="project-list-item"
					style="background-image: <?php p($project->getTitleImage());?>"
				>
					<div class="item-info">
						<?php p($project->getName());?><br/>
						<span class='node-amount'>
							<?php p($project->getNodeCount());?>
							nodes
						</span>
					</div>
					
					<div class="p_actions">
						<div class="p_download"></div>
						<div class="p_delete"></div>
					</div>	
				</div>
			</a>
		<?php } ?>
	</div>

	<?php if( empty($projects) ) { ?> 
		<div class="content no-projects-notice">
			<h1>Hello World!</h1>
			<p>
				Here you can create new projects and collections.<br/>
				Inside of a project you can write, edit and delete texts<br/>
				or drag and drop some images to upload them.
			</p>
		</div>
	<?php } ?>

	<div class="navigation">
		<a id="logoutButton" class="index" href="#">
			<img src="media/img/logout.svg"/>
		</a>
	</div>
</div>

<?php include(NX_PATH.'media/templates/foot.html.php'); ?>
