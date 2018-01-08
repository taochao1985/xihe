<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0,viewport-fit=cover">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<style type="text/css">
			body{
				width: 640px;
				margin: 0 auto;
			}
		</style>
    </head>
    <body>
    	<h2><?php echo $lession->title;?></h2>
		<?php if ( $lession->video_path != "" ) {?>
			<div>
				<video width="100%" controls="controls">
					<source src="<?php echo $lession->video_path;?>" type="video/ogg">
				    <source src="<?php echo $lession->video_path;?>" type="video/mp4">
				Your browser does not support the video tag.
				</video>
			</div>
		<?php }?>
		<?php if ( $lession->audio_path !="" ) {?>
			<div>
				<audio controls="controls">
				    <source src="<?php echo $lession->audio_path;?>" type="audio/ogg">
				  	<source src="<?php echo $lession->audio_path;?>" type="audio/mpeg">
				Your browser does not support the audio tag.
				</audio>
			</div>
		<?php }?>
		<div>
			<?php echo $lession->description;?>
		</div>
    </body>
</html> 
