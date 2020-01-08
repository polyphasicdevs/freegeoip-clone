<?php
/**
 * 
 */
require_once WP_CIP_FUNC . 'functions.php';

/**
 * 
 */
function wp_cip_admin_log_view() {
    $logs = wp_cip_get_logs(100);
    ?>
    <div class="container">
        <h2>Country IP Specific Redirections Log </h2>
        <hr>
        <!-- [Listing area] -->    
        <div class="row">
            <div class="col-md-12">                
                <div class="panel panel-default panel-table">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col col-xs-6">
                                <h3 class="panel-title">Latest 100 entries:</h3>
                            </div>
                            <!--                            <div class="col col-xs-6 text-right">
                                                            <button type="button" class="btn btn-sm btn-primary btn-create">Create New</button>
                                                        </div>-->
                        </div>
                    </div>
                    <?php if (count($logs)) { ?>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-list">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Result</th>
                                    </tr> 
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($logs as $log) {
                                        print '<tr>
                                            <td>' . $log->post . '</td>
                                            <td>' . $log->message . '</td>
                                            </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    <?php } else { ?>
                        <div class="panel-body">
                            <p>Nothing logged yet</p>
                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
        <!-- [Listing area] -->
    </div>












    <?php
}
