<?php

$edit_mode = false;
$page_data = null;
if(isset($_GET['edit_id'])){
	$page_data = Page::getById($_GET['edit_id']);
	if($page_data){
		$edit_mode = true;
	} else {
		echo 'Page ID not exist or missing!';
		return;
	}
}

$active = 'active';
if($edit_mode){
	$active = '';
}

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.49.0/lib/codemirror.min.css">
<!-- KaTeX -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.css">
<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<!-- <link href="https://cdn.jsdelivr.net/npm/suneditor@latest/assets/css/suneditor.css" rel="stylesheet"> -->
<!-- <link href="https://cdn.jsdelivr.net/npm/suneditor@latest/assets/css/suneditor-contents.css" rel="stylesheet"> -->
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
<!-- languages (Basic Language: English/en) -->
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/src/lang/ko.js"></script>
<!-- codeMirror -->
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.49.0/lib/codemirror.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.49.0/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.49.0/mode/xml/xml.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.49.0/mode/css/css.js"></script>
<!-- KaTeX -->
<script src="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.js"></script>

<div class="section section-full">
	<ul class="nav nav-tabs custom-tab" role="tablist">
		<li class="nav-item" role="presentation">
			<a class="nav-link <?php echo $active ?>" data-bs-toggle="tab" href="#pagelist"><?php _e('Pages') ?></a>
		</li>
		<?php if($edit_mode){
			?>
		<li class="nav-item" role="presentation">
			<a class="nav-link active" data-bs-toggle="tab" href="#plugin-editpage" id="trigger-edit"><?php _e('Edit page') ?></a>
		</li>
		<?php } else { ?>
		<li class="nav-item" role="presentation">
			<a class="nav-link" data-bs-toggle="tab" href="#addpage"><?php _e('Add page') ?></a>
		</li>
		<?php } ?>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<div class="tab-pane tab-container <?php echo $active ?>" id="pagelist">
			<table class="table table-striped">
				<thead>
				<tr>
					<th>#</th>
					<th><?php _e('Title') ?></th>
					<th><?php _e('Created') ?></th>
					<th><?php _e('Slug') ?></th>
					<th><?php _e('URL') ?></th>
					<th><?php _e('Action') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$results = array();
				$data = Page::getList();
				$results['pages'] = $data['results'];
				$results['totalRows'] = $data['totalRows'];
				$index = 0;
				foreach ( $results['pages'] as $page ) {
					$index++;
					?>
				<tr>
					<th scope="row"><?php echo esc_int($index); ?></th>
					<td>
						<?php echo esc_string($page->title)?>
					</td>
					<td>
						<?php echo date('j M Y', $page->createdDate) ?>
					</td>
					<td>
						<?php echo esc_string($page->slug)?>
					</td>
					<td><a href="<?php echo get_permalink('page', $page->slug) ?>" target="_blank"><?php _e('Visit') ?></a></td>
					<td><span class="actions"><a class="editpage" href="<?php echo DOMAIN ?>admin/dashboard.php?viewpage=plugin&name=pages&edit_id=<?php echo esc_int($page->id)?>"><i class="fa fa-pencil-alt circle" aria-hidden="true"></i></a><a class="deletepage" href="#" id="<?php echo esc_int($page->id)?>"><i class="fa fa-trash circle" aria-hidden="true"></i></a></span></td>
				</tr>
				<?php } ?>
			</tbody>
			</table>
			<div class="general-wrapper">
				<p><?php _e('%a pages in total.', esc_int($results['totalRows'])) ?></p>
			</div>
		</div>
		<?php if($edit_mode) { ?>
			<div class="tab-pane tab-container active" id="plugin-editpage">
				<div class="general-wrapper">
					<form id="plugin-form-editpage" method="post">
						<input type="hidden" name="id" value="<?php echo esc_int($page_data->id) ?>"/>
						<div class="mb-3">
							<label class="form-label" for="title"><?php _e('Page Title') ?>:</label>
							<input type="text" class="form-control" id="newpagetitle" name="title" placeholder="Name of the page" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $page_data->title )?>"/>
						</div>
						<div class="mb-3">
							<label class="form-label" for="slug"><?php _e('Page Slug') ?>:</label>
							<input type="text" class="form-control" id="newpageslug" name="slug" placeholder="Page url ex: this-is-sample-page" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $page_data->slug )?>"/>
						</div>
						<div class="mb-3">
							<label class="form-label" for="content"><?php _e('Content') ?>:</label>
							<textarea class="form-control" name="content" id="p-content"><?php echo $page_data->content ?></textarea>
						</div>
						<div class="mb-3">
							<label class="form-label" for="title"><?php _e('Created Date') ?>:</label>
							<input type="date" class="form-control" name="createdDate" placeholder="YYYY-MM-DD" required maxlength="10" value="<?php echo date( "Y-m-d", $page_data->createdDate ); ?>" />
						</div>
						<input type="submit" class="btn btn-primary"  name="saveChanges" value="<?php _e('Save') ?>" />
					</form>
					<br>
					<p>
						Note: Image upload is not work, use image link instead.
					</p>
					<p>
						WYSIWYG editor by <a href="https://github.com/JiHong88/SunEditor" target="_blank">SunEditor</a>
					</p>
				</div>
				
			</div>
		<?php } else { ?>
			<div class="tab-pane tab-container fade" id="addpage">
				<div class="general-wrapper">
					<form id="plugin-form-newpage" method="post">
						<div class="mb-3">
							<label class="form-label" for="title"><?php _e('Page Title') ?>:</label>
							<input type="text" class="form-control" id="newpagetitle" name="title" placeholder="Name of the page" required autofocus maxlength="255" value=""/>
						</div>
						<div class="mb-3">
							<label class="form-label" for="slug"><?php _e('Page Slug') ?>:</label>
							<input type="text" class="form-control" id="newpageslug" name="slug" placeholder="Page url ex: this-is-sample-page" required autofocus maxlength="255" value=""/>
						</div>
						<div class="mb-3">
							<label class="form-label" for="content"><?php _e('Content') ?>:</label>
							<textarea class="form-control" name="content" id="p-content"></textarea>
						</div>
						<div class="mb-3">
							<label class="form-label" for="title"><?php _e('Created Date') ?>:</label>
							<input type="date" class="form-control" name="createdDate" placeholder="YYYY-MM-DD" required maxlength="10" value="<?php echo date( "Y-m-d" ); ?>" />
						</div>
						<input type="submit" class="btn btn-primary"  name="saveChanges" value="<?php _e('Publish') ?>" />
					</form>
					<br>
					<p>
						WYSIWYG editor by <a href="https://github.com/JiHong88/SunEditor" target="_blank">SunEditor</a>
					</p>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<script type="text/javascript">
	var sun_editor;
	$(document).ready(()=>{
		sun_editor = SUNEDITOR.create('p-content', {
			display: 'block',
			width: '100%',
			height: 'auto',
			popupDisplay: 'full',
			charCounter: true,
			charCounterLabel: 'Characters :',
			buttonList: [
		        ['undo', 'redo',
		        'font', 'fontSize', 'formatBlock',
		        'paragraphStyle', 'blockquote',
		        'bold', 'underline', 'italic', 'strike', 'subscript', 'superscript',
		        'fontColor', 'hiliteColor', 'textStyle',
		        'removeFormat',
		        'outdent', 'indent',
		        'align', 'horizontalRule', 'list', 'lineHeight',
		        'table', 'link', 'image', 'video', 'audio', /** 'math', */ // You must add the 'katex' library at options to use the 'math' plugin.
		        /** 'imageGallery', */ // You must add the "imageGalleryUrl".
		        'fullScreen', 'showBlocks', 'codeView',
		        'preview']
		    ],
			imageUploadUrl: 'includes/ajax-actions.php?action=upload_image',
			imageMultipleFile: false,
			imageAccept: '.jpg, .png, .jpeg, .gif',
			placeholder: 'Start typing something...',
			codeMirror: CodeMirror,
			katex: katex
		});
		//
		$( "form" ).submit(function( event ) {
			let arr = $( this ).serializeArray();
			if($(this).attr('id') === 'plugin-form-newpage'){
				event.preventDefault();
				let content = sun_editor.getContents(true);
				if(content){
					let data = {
						action: 'newPage',
						title: get_value(arr, 'title'),
						slug: (get_value(arr, 'slug').toLowerCase()).replace(/\s+/g, "-"),
						createdDate: get_value(arr, 'createdDate'),
						content: content,
					}
					sendRequest(data, true);
				} else {
					alert('Page content cannot be blank');
				}
			} else if($(this).attr('id') === 'plugin-form-editpage'){
				event.preventDefault();
				let content = sun_editor.getContents(true);
				let data = {
					action: 'editPage',
					title: get_value(arr, 'title'),
					slug: (get_value(arr, 'slug').toLowerCase()).replace(/\s+/g, "-"),
					id: get_value(arr, 'id'),
					createdDate: get_value(arr, 'createdDate'),
					content: content,
				}
				console.log(data)
				sendRequest(data, true);
			}
		});
		function get_value(arr, key){
			for(let i=0; i<arr.length; i++){
				if(arr[i].name === key){
					return arr[i].value;
				}
			}
		}
		<?php
			if($edit_mode){ ?>
				$('#trigger-edit').click();
			<?php }
		?>
	});
</script>