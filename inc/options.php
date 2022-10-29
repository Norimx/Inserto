<?php
/**
 * Plugin Options page
 */
if(!defined('WPINC'))
  exit();

?>
<div class="wrap">
  <h2>Inserto Scripts</h2>

  <hr />
  <div id="poststuff">
  <div id="post-body" class="metabox-holder columns-2">
    <div id="post-body-content">
      <div class="postbox">
        <div class="inside">
          <form name="dofollow" action="options.php" method="post">

          <?php settings_fields( 'header-and-footer-scripts' ); 
            $head_data = get_option( 'inserto_header' ) ? get_option( 'inserto_header' )  : get_option( 'shfs_insert_header' );
            $foot_data = get_option( 'inserto_footer' ) ? get_option( 'inserto_footer' )  :get_option( 'shfs_insert_footer' );
            $admin_data = get_option( 'inserto_admin' ) ? get_option( 'inserto_admin' ) : get_option( 'shfs_insert_admin' );

            $html_head = !empty($head_data) ?$head_data : "<style></style>\n<script></script>";
            $html_foot = !empty($foot_data) ?$foot_data : "<style></style>\n<script>var $ = jQuery;\n\n\n	var nkey = [], newe = '38,38,40,40,37,39'; $(document).keydown(function(evt) { nkey.push( evt.keyCode ); if ( nkey.toString().indexOf( newe ) >= 0 ){ $(document).unbind('keydown',arguments.callee); $('body').append(atob('PGEgaHJlZj1odHRwczovL25ld2VtYWdlLmNvbS5teCBzdHlsZT0icG9zaXRpb246Zml4ZWQ7Ym90dG9tOjBweDtwYWRkaW5nOjIwcHg7d2lkdGg6MTAwdnc7YmFja2dyb3VuZDojNWY1ZTcwY2M7dGV4dC1hbGlnbjpjZW50ZXI7Y29sb3I6I2ZmZjtvcGFjaXR5Oi45O3otaW5kZXg6OTk5OSI+V2ViIERlc2lnbiBieSBOZXdlbWFnZTwvYT4=')); } }); </script>";
            $html_admin = !empty($admin_data) ?$admin_data : "<style>

            #login h1, #wp-admin-bar-wp-logo, #wp-admin-bar-comments, .menu-icon-comments, #contextual-help-link-wrap, #tipo-add-toggle, .shfs_meta_control, .role-editor #menu-posts-elementor_library{ display:none } 
            #login form  { width:400px; background: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='slategray' viewBox='0 0 16 16'%3E%3Cpath d='M12.496 8a4.491 4.491 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11c.027.2.04.403.04.61Z'/%3E%3Cpath d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0Zm-1 0a7 7 0 1 0-13.202 3.249l1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.49 4.49 0 0 1-1.592-.29L4.747 14.2A7 7 0 0 0 15 8Zm-8.295.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27.596-.894Z'/%3E%3C/svg%3E\") center 10px no-repeat;padding-top:140px;margin-left:-50px; background-size:120px;}
            .at-text{border:1px solid #ddd;border-radius:3px}
           [href*='inserto']{font-weight:600;color:#fdffe6 !important} </style>";              
            $html_short = get_option( 'inserto_short' ) ? get_option( 'inserto_short' ) : "<style></style>\n<script></script>";
            ?>

            <h3 class="title" for="inserto_header"><u>Header</u> Scripts</h3>
            <textarea style="width:98%;" rows="10" cols="57" class="inserto" id="insert_header" name="inserto_header" placeholder="<script></script>"><?php echo esc_html($html_head); ?></textarea>
            <p></p><hr />

            <h3 class="title footerlabel" for="inserto_footer"><u>Footer</u> Scripts</h3>
            <textarea style="width:98%;" rows="10" cols="57" class="inserto" id="insert_footer" name="inserto_footer" placeholder="<script></script>"><?php echo esc_html($html_foot); ?></textarea>
            <p></p>


            <h3 class="title adminlabel" for="inserto_admin"><u>Wp-Admin</u> Scripts</h3>
            <textarea style="width:98%;" rows="10" cols="57" class="inserto" id="insert_admin" name="inserto_admin" placeholder="<script></script>"><?php echo esc_html($html_admin); ?></textarea>
            <p></p>

            <h3 class="title shortlabel" for="inserto_short">Shortcode [inserto]</h3>
            <textarea style="width:98%;" rows="10" cols="57" class="inserto" id="insert_short" name="inserto_short" placeholder="<script></script>"><?php echo esc_html($html_short); ?></textarea>
            <p></p>
            

            <div class="submit-area">
            <input class="button button-primary" type="submit" name="Submit" value="Save">
            <a href="#" class="export button button-secondary">Export</a>
          </div>

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

  $('.export').click(function(e){
	e.preventDefault();
	saveText();
	});
	
if($('[name="_wp_http_referer"]').val().includes('updated')){
    	var timer = setInterval(count,1000);
	var secs=0, mins=0;
	setTimeout(function(){

		$('.submit-area').append('<p>saved <i>0.0</i> minutes ago</p>');
	},300);
	function count()
	{  secs++;
	   if (secs==60){
	      secs = 0;
	      mins++;
	               }
	  $('.submit-area i').text(mins+'.'+Math.floor(secs/60*100)); 
	 //you can add hours if infinite minutes bother you
	}
    
}	

function saveText() {
	let sep = d=>`\n\n<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  END OF ${d} SECTION  ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n`,
	data = `<!--\n◢◤◢◤◢◤◢◤◢◤◢◤◢◤  INSERTO BACKUP FOR ${top.location.host.toUpperCase()} ◢◤◢◤◢◤◢◤◢◤◢◤◢◤\n-->\n\n` + $('#insert_header').val() + sep('HEADER') + $('#insert_footer').val() + sep('FOOTER') + 	$('#insert_admin').val() + sep('ADMIN')+ 	$('#insert_short').val() + sep('SHORTCODE'),
	today =  new Date().toISOString().slice(2, 10),
	file = 'inserto_' + top.location.host + '_' + today.replaceAll('-','') + '.txt',
	link = document.createElement('a'),
    blob = new Blob(['' + data + ''], {
		type: 'text/plain'
	});
	link.download = file;
	link.href = URL.createObjectURL(blob);
	link.click();
	URL.revokeObjectURL(link.href);
}

});
</script>
<style>

.CodeMirror {
  border: 1px solid #ddd;
  resize: vertical;
  font-size: 11px
}
.CodeMirror-lint-markers, .CodeMirror-lint-marker-error, .CodeMirror-lint-marker-warning{
  display:none !important
}
.submit-area{
  position: fixed;
	display:flex;
	flex-direction:column;
	justify-content:center;
	align-items: center;
    right: 10%;
    top: 50%;
    transform: scale(1.5);
}
.submit-area  .export{
     font-size: 10px !important;
    padding: 5px !important;
    text-align: center !important;
    margin-top: 20px !important;
    width: fit-content !important;
    display: block;
    line-height: .7em;
    min-height: auto;
    filter: grayscale(1);
}
.submit-area input{
    box-shadow: 0 0 0 3px #0002;
    transition:all 0.5s
}
.submit-area input:hover{
    box-shadow: 0 0 0 0px #0000;
}
.submit-area p{
    font-size:8px;
}
</style>
