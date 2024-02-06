<?php
/**
 * Plugin meta for Inserto
 */

global $post;
$meta = get_post_meta($post->ID,'_inpost_head_script',1);
$hs = !empty($meta) ? base64_decode($meta) : '';
?>
 <div class="inserto_meta_control">

	<p>
		<textarea name="_inpost_head_script" rows="5" style="width:98%;" placeholder="<script></script>"><?php echo $hs; ?></textarea>
	</p>

	<p>Insert code before &lt;/body></p>
</div>
