<?php
/**
 * Plugin meta for Inserto
 */

global $meta;
?>
 <div class="inserto_meta_control">

	<p>
		<textarea name="_inpost_head_script[synth_header_script]" rows="5" style="width:98%;" placeholder="<script></script>"><?php if(!empty($meta['synth_header_script'])) echo $meta['synth_header_script']; ?></textarea>
	</p>

	<p>Insert code before &lt;/body></p>
</div>
