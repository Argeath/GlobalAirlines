<div class="well">
	<div class="page-header">
		<h1>Konto <small>Zmiana avatara</small></h1>
	</div>
	<table class="table table-striped">
		<tr>
			<td class="col-md-3 col-sm-6 col-xs-12">
				<img src="<?= $avatar; ?>" alt="" width="200" height="200" class="img-thumbnail"/>
			</td>
			<td>
				<form id="upload-form" action="<?php echo URL::site('user/avatar') ?>" method="post" enctype="multipart/form-data" onsubmit="return checkSize(2097152)">
					Wybierz nowy awatar. Dopuszczalne rozrzeszenia: jpg, png, gif. Maksymalna rozdzielczość: 120x120px. Maksymalny rozmiar: 2MB.
					<input type="hidden" name="MAX_FILE_SIZE" value="2097152" /> 
					<input type="file" name="avatar" id="avatar" class="form-control" />
					<input type="submit" name="submit" id="submit" value="Wgraj nowy" class="btn btn-primary btn-block"/>
				</form>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
function checkSize(max_img_size)
{
    var input = document.getElementById("upload");
    // check for browser support (may need to be modified)
    if(input.files && input.files.length == 1)
    {           
        if (input.files[0].size > max_img_size) 
        {
            alert("The file must be less than " + (max_img_size/1024/1024) + "MB");
            return false;
        }
    }

    return true;
}
</script>