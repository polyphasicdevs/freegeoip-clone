<?php
/**
 * 
 */
require_once WP_CIP_FUNC . 'functions.php';

/**
 * 
 */
function wp_cip_admin_noredirect_view() {
    $message = NULL;
    if (isset($_POST['addnodir'])) {
        $message = wp_cip_update_no_rd($_POST);
    }
    $enabled = get_option(WP_CIP_PREFIX . 'no_redirect', 0);
    ?>
    <div class="container">
        <h2>No Redirect</h2>
        <hr>
        <!-- [Add rule area] -->
        <div class="row">
            <?php if ($message) { ?> <div class="col-md-12"> <?php echo $message; ?> </div> <?php } ?>
            <div class="col-md-12"> 
                <div id="callout-glyphicons-empty-only" class="bs-callout bs-callout-danger"> 
                    Append <strong>?noredirect=true</strong> to any URL to avoid being redirected.<br />
                    <em>Example: <?php bloginfo('url') ?>/page/?noredirect=true</em>
                </div>
            </div>
            <div class="col-md-8">
                <form class="form-horizontal" role="form" method="post">
                    <fieldset>                       
                        <div class="form-group">
                            <label>Enable <strong>?noredirect=true</strong> GET parameter?</label>
                            <div class="radio">
                                <label>                                     
                                    <input type="radio" name="no_redirect" value="0" <?php if ($enabled == "0") print 'checked'; ?>/>
                                    No 
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="no_redirect" value="1" <?php if ($enabled == "1") print 'checked'; ?>/>
                                    yes
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" name="addnodir" class="btn btn-success">Update Settings</button>
                            </div>
                        </div>

                    </fieldset>
                </form>
            </div>
        </div>
        <!-- [Add rule area] -->
    </div>












    <?php
}
