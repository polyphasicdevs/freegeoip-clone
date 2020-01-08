<?php
/**
 * 
 */
require_once WP_CIP_FUNC . 'functions.php';

/**
 * 
 */
function wp_cip_admin_rule_view() {
    $message = NULL;
    if (isset($_GET['delID'])) {
        wp_cip_delete_rule($_GET['delID']);
        print '<meta http-equiv="refresh" content="0;url=index.php?page=' . WP_CIP_MENU . '" />';
        exit;
    }
    if (isset($_POST['addrule'])) {
        $message = wp_cip_add_rule($_POST);
    }


    $countryList = wp_cip_get_contry_codes();
    $ctypes = wp_cip_get_all_content();
    $fl_array = wp_cip_check_cache();
    $rules = wp_cip_get_rules();
    ?>
    <div class="container">
        <h2>Country IP Specific Redirections</h2>
        <hr>
        <!-- [Add rule area] -->
        <div class="row">
            <!--    <div class="alert alert-danger">Achtung</div>
                <div class="alert alert-info">Hey, you know?</div>
                <div class="alert alert-success">Yep! Its all right</div>-->

            <?php if ($message) { ?> <div class="col-md-12"> <?php echo $message; ?> </div> <?php } ?>
            <div class="col-md-6">
                <form class="form-horizontal" role="form" method="post">
                    <fieldset>
                        <legend>Add New Redirect Rule</legend>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="country">Country:</label>
                            <div class="col-sm-7">          
                                <select class="form-control" name="country">
                                    <?php
                                    foreach ($countryList as $countryCode => $countryName) {
                                        printf('<option value="%s">%s</option>', $countryCode, $countryName);
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="catID">For This Category:</label>
                            <div class="col-sm-7">                                
                                <select class="form-control" name="catID">
                                    <option value="0">-None-</option>
                                    <?php
                                    $categories = get_categories();
                                    foreach ($categories as $cat) {
                                        print '<option value="' . $cat->cat_ID . '">' . $cat->name . '</option>';
                                        print "\n";
                                    }
                                    ?>
                                </select>                                
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="postID">OR For This POST/PAGE:</label>
                            <div class="col-sm-7">
                                <select class="form-control" name="postID">
                                    <option value="0">-None-</option>
                                    <option value="999999">SITEWIDE RULE - ALL PAGES</option>
                                    <option value="home">!HOMEPAGE!</option>
                                    <?php
                                    foreach ($ctypes as $type) {
                                        foreach ($type as $post) {
                                            print '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
                                            print "\n";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for=target">Target URL:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="target" id="target" placeholder="http://www.example.com/url">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" name="addrule" class="btn btn-success">Add New Rule</button>
                            </div>
                        </div>

                    </fieldset>
                </form>
            </div>
            <div class="col-md-6"> 
                <div id="callout-glyphicons-empty-only" class="bs-callout bs-callout-danger"> 
                    <h4>NOTES!</h4> 
                    <?php if (count($fl_array)) echo '<h3 style="color:#cc0000;">If you have any CACHING plugins ACTIVE this plugin will not work properly simply because it will also cache the 1st visitor location and assume everyone else is from the same country. Ignore this message if it\'s not the case.!</h3>'; ?>
                    <p><strong>NOTES!</strong><br/> - If you choose a category <strong>all traffic</strong> for that specific category will be redirected.<br/>
                        - If you want to redirect <strong>a single POST</strong> LEAVE category as -None-<br/>
                        - If you want to <strong>redirect</strong> no matter what category/page/post choose <strong>"SITEWIDE RULE"</strong>
                    </p>

                </div>
            </div>
        </div>
        <!-- [Add rule area] -->

        <!-- [Listing area] -->    
        <div class="row">
            <div class="col-md-12">                
                <div class="panel panel-default panel-table">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col col-xs-6">
                                <h3 class="panel-title">Current Rules:</h3>
                            </div>
                            <!--                            <div class="col col-xs-6 text-right">
                                                            <button type="button" class="btn btn-sm btn-primary btn-create">Create New</button>
                                                        </div>-->
                        </div>
                    </div>
                    <?php if (count($rules)) { ?>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-list">
                                <thead>
                                    <tr>
                                        <th class="hidden-xs">Country</th>
                                        <th>Target URL</th>
                                        <th>For Category/Post/Page</th>
                                        <th><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></th>
                                    </tr> 
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($rules as $row) {
                                        $postID = $row->post_id;
                                        $catID = $row->cat_id;

                                        if ($catID != 0) {
                                            $target = get_category($catID);
                                            $target = '<strong>Category</strong> : ' . $target->cat_name;
                                        } elseif ($postID != 0) {
                                            if ($postID != 999999) {
                                                $target = get_post($postID);
                                                $target = '<strong>' . ucfirst($target->post_type) . '</strong> : ' . $target->post_title;
                                            } else {
                                                $target = '<strong>SITEWIDE REDIRECT</strong>';
                                            }
                                        } else {
                                            $target = "<strong>!HOMEPAGE!</strong>";
                                        }

                                        print '<tr>
                                        <td>' . $countryList[$row->country_id] . '</td>
                                        <td>' . $row->target_url . '</td>
                                        <td>' . ($target) . '</td>
                                        <td><a class="btn btn-default" href="?page=' . WP_CIP_MENU . '&delID=' . $row->id . '" onclick="return confirm(\'Are you SURE you want to REMOVE this redirect rule?\');"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                        </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    <?php } else { ?>
                        <div class="panel-body">
                            <p>No rule found!</p>
                        </div>
                    <?php } ?>
                    <!--                    <div class="panel-footer">
                                            <div class="row">
                                                <div class="col col-xs-4">Page 1 of 5
                                                </div>
                                                <div class="col col-xs-8">
                                                    <ul class="pagination hidden-xs pull-right">
                                                        <li><a href="#">1</a></li>
                                                        <li><a href="#">2</a></li>
                                                        <li><a href="#">3</a></li>
                                                        <li><a href="#">4</a></li>
                                                        <li><a href="#">5</a></li>
                                                    </ul>
                                                    <ul class="pagination visible-xs pull-right">
                                                        <li><a href="#">«</a></li>
                                                        <li><a href="#">»</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>-->
                </div>

            </div>
        </div>
        <!-- [Listing area] -->
    </div>












    <?php
}
