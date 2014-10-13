
jQuery.fn.visible = function() {
	return this.css('visibility', 'visible');
};

jQuery.fn.invisible = function() {
	return this.css('visibility', 'hidden');
};

jQuery.fn.visibilityToggle = function() {
	return this.css('visibility', function(i, visibility) {
		return (visibility == 'visible') ? 'hidden' : 'visible';
	});
};

$.fn.serializeObject = function() {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name] !== undefined) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};


var nemexApi = function(action, data, callback, errback ) {
	// Check if data is a (jq wrapped?) form element
	if( 
		data instanceof HTMLFormElement ||
		((data instanceof $) && data.is('form'))
	) { 
		data = $(data).serializeObject(); 
	}
	data.action = action;
	$.ajax({type: 'POST', url: 'api.php', success: callback, error: (errback||null), dataType: 'json', data: data});
};


var mobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

var resizeSnapDrawers = function() {
	$('.node-text').each(function(){
		$(this).find('.snap-drawer .m-sub').height( $(this).find('.ncontent').height()/2 );
	});

	$('.node-image .snap-drawer .e').hide();
	$('.node-image').each(function(){
		$(this).find('.snap-drawer .m-sub').height( $(this).find('.ncontent img').height() );
	});
};

$(document).ready(resizeSnapDrawers);
$(window).resize(resizeSnapDrawers);


$(document).ready(function(){
	var markdownConverter = new Showdown.converter();

	$('textarea').autosize();

	// Convert all markdown texts to html, but save the markdown source 
	// in a data attribute
	
	$('.markdown').each(function(){
		var el = $(this);
		var source = el.text();
		el.data('source', source);
		el.html( markdownConverter.makeHtml(source) );
	});

	if( mobile ) {
		var snappers = [];
		$('.row .snap-content').each(function(){
			snappers.push( new Snap({element: this, disable:'left', minPosition: -55}) );
		});
	}
	else {
		$(".row").hover(
			function(){
				// not editable, or not in edit mode?
				if( !$(this).find('.c3edit').length || $(this).find('.c3edit').is(':hidden') ) {
					$(this).find('.c3 .actions').css('visibility', 'visible'); 
				}
			},
			function(){ 
				$(this).find('.c3 .actions').css('visibility', 'hidden'); 
			}
		);
	}
	
	$(".project-list-item").hover(function(){
		if(!mobile) { 
			$(this).children('.p_actions').visibilityToggle();
		}
	});

	$(".projectList a").last().addClass('last');

	$("#addProject").click(function(){
		$("#addProject").toggleClass('rotate');
		$('.addProjectForm').slideToggle("fast", "linear");
		$('#newProject').focus();
	});

	$("#holder").click(function(){
		$("#holder").toggleClass('rotate');
		$('#upload').addClass('mobile');
		$('#newMarkdown').slideToggle();
		$('#newMarkdown').children(".c3edit").children(".editareafield").val('');
		$('#newMarkdown').children(".c3").children(".content").html('');
		$('#newMarkdown').children(".c3edit").children(".editareafield").trigger('autosize.resize');	
	});

	$(".discardAdd").click(function() {
		$('#newMarkdown').slideToggle("normal", function() {
			$('#upload').removeClass('mobile');
			$("#holder").toggleClass('rotate');
		});
	});

	$('#logoutButton').click(function(){
		nemexApi('logout', {}, function(response){
			location.reload();
		});
		return false;
	});




	// add new project
	$('.addProjectForm').submit(function(){
		nemexApi('addProject', this, function(response){
			location.reload();
		});
		return false;
	});

	// delete project
	$('.p_delete').click(function() {
		var name = $(this).parents('.project-list-item').data('name');
		if( confirm('Do you really want to delete the project '+name+'?') ) {
			nemexApi('deleteProject', {name:name}, function(response){
				location.reload();
			});
		}
		return false;
	});

	// download project
	$('.p_download').click(function(e){
		var name = $(this).parents('.project-list-item').data('name');
		window.location.href = 'api.php?downloadProject='+encodeURIComponent(name);
		return false;	
	});

	$('#markdownhelp').click(function(e) {
		$('#mdhelp').slideToggle();
	});

	// share
	$('#shareProject').click(function(){
		var project = $('.activeProject').text();
		nemexApi('shareProject', {project:project}, function(response){
			location.reload();
		});
		return false;
	});

	// unshare
	$('#unshareProject').click(function(){
		var project = $('.activeProject').text();
		nemexApi('unshareProject', {project:project}, function(response){
			location.reload();
		});
		return false;
	});

	// download node
	$('.download-big').click(function(){
		var nodeName = $(this).parents('.row').data('name');
		var projectName = $('.activeProject').text();
		window.location.href = 
			'api.php?downloadNode='+encodeURIComponent(nodeName)+
			'&project='+encodeURIComponent(projectName);
		return false;	
	});

	// add node
	$('.addPost').click(function(){
		var project = $('.activeProject').text();
		var content = $('#addfield').val();
		nemexApi('addNode', {project:project, content:content}, function(response){
			location.reload();
		});
	});

	// discard edit
	$('.discardUpdate').click(function(){
		var $node = $(this).parents('.row');
		var source = $node.find('.markdown').data('source');

		$node.find('.markdown').html( markdownConverter.makeHtml(source) );
		$node.find('.c3').toggleClass('edit-mode');
		$node.find('.c3edit').toggle();
		
		if( mobile ) {
			$node.find('.snap-drawers').visible();	
			$node.find('.actions').invisible();
		}
	});

	// save edit
	$('.save').click(function(){
		var $node = $(this).parents('.row');
		var data = {
			content: $node.find('.editareafield').val(),
			project: $('.activeProject').text(),
			node: $node.data('name')
		};

		nemexApi('updateNode', data, function(response){
			location.reload();
		});
	});

	// delete 
	$('.delete, .delete-big').click(function(){
		var nodeName = $(this).parents('.row').data('name');
		var projectName = $('.activeProject').text();
		if( confirm('Do you really want to delete the node '+nodeName+'?') ) {
			nemexApi('deleteNode', {project:projectName, node:nodeName}, function(response){
				location.reload();
			});
		}
	});




	var state = 1;

	$('.edit, .edit-big').click(function(){
		var $node = $(this).parents('.row');
		$node.find('.snap-drawers').visibilityToggle();
		$node.find('.actions').visibilityToggle();

		var source = $node.find('.markdown').data('source');


		$node.find('.c3').toggleClass('edit-mode');
		$node.find('.c3edit').toggle();
		$node.find('.editareafield').val(source).trigger('autosize.resize');
		$node.find('.editarea').focus();
	});

	$('.editareafield').keyup(function(){
		var $node = $(this).parents('.row');
		var md = markdownConverter.makeHtml($node.find('.editareafield').val());

		$node.find('.markdown').html(md);
		$node.find('.editareafield').trigger('autosize.resize');
	});



	// file upload/preview
	var holder = document,
		tests = {
				filereader: typeof FileReader != 'undefined',
				dnd: 'draggable' in document.createElement('span'),
				formdata: !!window.FormData,
				progress: "upload" in new XMLHttpRequest
			}, 
		support = {
			filereader: document.getElementById('filereader'),
			formdata: document.getElementById('formdata'),
			progress: document.getElementById('progress'),
		},
		acceptedTypes = {
			'image/png': true,
			'image/jpeg': true,
			'image/gif': true
		},
		progress = document.getElementById('uploadprogress'),
		fileupload = document.getElementById('upload');

		"filereader formdata progress".split(' ').forEach(function (api) {
			if (tests[api] === false) {
				support[api].className = 'fail';
			} 
			else {
				// FFS. I could have done el.hidden = true, but IE doesn't support
				// hidden, so I tried to create a polyfill that would extend the
				// Element.prototype, but then IE10 doesn't even give me access
				// to the Element object. Brilliant.
				// support[api].className = 'hidden';
			}
	});

	function previewfile(file) {
		if( tests.filereader === true && acceptedTypes[file.type] === true ) {
			var reader = new FileReader();
			reader.onload = function (event) {
				var image = new Image();
				image.src = event.target.result;
				holder.getElementById('holder').appendChild(image);

			};
			reader.readAsDataURL(file);
		} 
		else {
			holder.innerHTML += '<p>Uploaded ' + file.name + ' ' + (file.size ? (file.size/1024|0) + 'K' : '') + '</p>';
		}
	}

	function readfiles(files) {
		var formData = new FormData();
		for( var i = 0; i < files.length; i++ ) {
			formData.append('file_'+i, files[i]);
			previewfile(files[i]);
		}

		formData.append('action', 'upload');
		formData.append('project', $('.activeProject').text());

		// now post a new XHR request
		var xhr = new XMLHttpRequest();
		xhr.open('POST', 'api.php');
		xhr.onload = function(data) {
			progress.value = progress.innerHTML = 100;
			location.reload();
		};

		xhr.upload.onprogress = function (event) {
			if (event.lengthComputable) {
				var complete = (event.loaded / event.total * 100 | 0);
				progress.value = progress.innerHTML = complete;
			}
		}	
		xhr.send(formData);
	}

	if( tests.dnd ) { 
		holder.ondragover = function () { this.className = 'hover'; return false; };
		holder.ondragend = function () { this.className = ''; return false; };
		holder.ondrop = function (e) {
			this.className = '';
			e.preventDefault();
			readfiles(e.dataTransfer.files);
		}
		
		$("#uup").change(function(e){
			e.preventDefault();
			readfiles(e.target.files);
			$("#holder").toggleClass('rotate');
			$('#upload').removeClass('mobile');
			$('#newMarkdown').fadeToggle("fast", "linear");
		});

	} 
	else {
		fileupload.className = 'hidden';
		fileupload.querySelector('input').onchange = function () {
			readfiles(this.files);
		};
	}

});


// Prevent links in standalone web apps opening Mobile Safari
if( ("standalone" in window.navigator) && window.navigator.standalone ){
	var noddy, remotes = false;
	document.addEventListener('click', function(event) {
		noddy = event.target;
		while(noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
			noddy = noddy.parentNode;
		}

		if('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location.host) !== -1 || remotes)) {
			event.preventDefault();
			document.location.href = noddy.href;
		}

	},false);
}
