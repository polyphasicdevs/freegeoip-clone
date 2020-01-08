<?php
/**
 * 
 */
require_once WP_CIP_FUNC . 'functions.php';

/**
 * 
 */
function wp_cip_admin_redirect_view() {
    $message = NULL;
    if (isset($_POST['addmass'])) {
        $message = wp_cip_update_mass($_POST);
    }
    $enabled = get_option(WP_CIP_PREFIX . 'mass_redirect');
    $targetURL = get_option(WP_CIP_PREFIX . 'mass_url');
    ?>
    <div class="container">
        <h2>Mass Redirect For Countries Without Rules</h2>
        <hr>
        <!-- [Add rule area] -->
        <div class="row">
            <?php if ($message) { ?> <div class="col-md-12"> <?php echo $message; ?> </div> <?php } ?>
            <div class="col-md-6">
                <form class="form-horizontal" role="form" method="post">
                    <fieldset>                       
                        <div class="form-group">
                            <label>Enable This Feature ?</label>
                            <div class="radio">
                                <label>                                     
                                    <input type="radio" name="mass_redirect" value="0" <?php if ($enabled == "0") print 'checked'; ?>/> 
                                    No 
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="mass_redirect" value="1" <?php if ($enabled == "1") print 'checked'; ?>/>
                                    yes
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mass_url" class="col-sm-3 control-label">Target URL:</label>
                            <div class="col-sm-9">
                                <input type="text" name="mass_url" value="<?php print $targetURL; ?>" class="form-control" placeholder="http://www.example.com/url" >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" name="addmass" class="btn btn-success">Update Settings</button>
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
