<?php include(NX_PATH.'media/templates/head.html.php'); ?>

<div class="header">NEMEX</div>

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
			<h1>Welcome to your nemex!</h1>
			<p>Get started and create a new project with the + Button above. You can upload images, create texts, edit and delete them and download the whole project as a single zip-file.
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
