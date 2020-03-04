<?php
/**
 * Plugin Options page
 */
if(!defined('WPINC'))
  exit();

?>
<div class="wrap">
  <h2>Insert Scripts</h2>

  <hr />
  <div id="poststuff">
  <div id="post-body" class="metabox-holder columns-2">
    <div id="post-body-content">
      <div class="postbox">
        <div class="inside">
          <form name="dofollow" action="options.php" method="post">

            <?php settings_fields( 'header-and-footer-scripts' ); ?>

            <h3 class="shfs-labels" for="shfs_insert_header">Scripts in &lt;head></h3>
            <textarea style="width:98%;" rows="10" cols="57" class="inserto" id="insert_header" name="shfs_insert_header" placeholder="<script></script>"><?php echo esc_html( get_option( 'shfs_insert_header' ) ); ?></textarea>
            <p></p><hr />

            <h3 class="shfs-labels footerlabel" for="shfs_insert_footer">Scripts before &lt;/body></h3>
            <textarea style="width:98%;" rows="10" cols="57" class="inserto" id="shfs_insert_footer" name="shfs_insert_footer" placeholder="<script></script>"><?php echo esc_html( get_option( 'shfs_insert_footer' ) ); ?></textarea>
            <p></p>

          <p class="submit">
            <input class="button button-primary" type="submit" name="Submit" value="Save" />
          </p>

          </form>
        </div>
    </div>
    </div>

    </div>
  </div>
</div>
<script>

jQuery(document).ready(function($) {
  $('.inserto').each(function(){
  wp.codeEditor.initialize($(this), {
    lineNumbers: true,
    mode: "htmlmixed"
  });
  });
})
</script>
<style>
.CodeMirror {
  border: 1px solid #ddd;
}
.CodeMirror-lint-markers, .CodeMirror-lint-marker-error, .CodeMirror-lint-marker-warning{
  display:none !important
}
</style>