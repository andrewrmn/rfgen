<?php
/*
 * Content Manager Module
 * Version: 1.1.34
 * SVN: 34
 * @version $Rev: 30 $
 * $LastChangedBy: feljun $
 * $LastChangedDate: 2016-01-21 05:41:36 -0500 (Thu, 21 Jan 2016) $
 *
 */
if(!class_exists('WLM3_ContentManager')){
    /**
     * Content Archiver Core Class
     */
    class WLM3_ContentManager{
        //activate module
        function load_hooks(){
            add_action('init',array(&$this,'ApplyDueDate'));
            add_action('wishlistmember3_post_page_options_menu',array(&$this,'wlm3_post_options_menu'));
            add_action('wishlistmember3_post_page_options_content',array(&$this,'ContentManagerOptions'));
        }
        //deactivate module
        function remove_hooks(){ //remove filters and actions
            remove_action('init',array(&$this,'ApplyDueDate'));
            remove_action('wishlistmember3_post_page_options_menu',array(&$this,'wlm3_post_options_menu'));
            remove_action('wishlistmember3_post_page_options_content',array(&$this,'ContentManagerOptions'));
        }

        function wlm3_post_options_menu() {
            echo '<li><a href="#" data-target=".wlm-inside-manager" class="wlm-inside-toggle">Manager</a></li>';
        }
        //page options
        function ContentManagerOptions(){
            $post_id = $_GET['post'];
            $custom_types = get_post_types(array('public'=> true,"_builtin"=>false));
            $ptypes = array_merge(array("post","page"),$custom_types);
            $post_type = $post_id ? get_post_type($post_id):$_GET['post_type'];
            $post_type = $post_type ? $post_type : 'post';

            $support_categories = $post_type == 'post' ? true : false;
            if ( $post_type !== 'post' AND $post_type !== 'page' ) {
                $p = get_post_type_object($post_type);
                if ( in_array("category", $p->taxonomies) ) $support_categories = true;
            }

            global $WishListMemberInstance,$WishListContentControl;
            //default date
            $wlccduedate = date_parse(date('Y-m-d H:i:s'));
            $wlccduedate = date('Y-m-d H:i:s',mktime(0,0,0,(int)$wlccduedate["month"],(int)$wlccduedate["day"],(int)$wlccduedate["year"]));
            $wlcc_duedate = $this->format_date($wlccduedate,'m/d/Y h:i A');

            $sched_type = ["move","repost","set"];
            $content_schedules = [];
            if ( $post_id ) {
                foreach ( $sched_type as $key => $t ) {
                    $content_sched = $this->GetPostManagerDate( $t ,$post_id );
                    foreach ( (array) $content_sched as $key => $value ) {
                        $content_schedules[] = ["type"=>$t,"value"=>$value];
                    }
                }
            }
            ?>
                <script type='text/javascript' src='<?php echo $WishListMemberInstance->legacy_wlm_url ?>/admin/post_page_options/content-control/js/manager.js'></script>
                <div class="wlm-inside wlm-inside-manager" style="display: none;">
                    <div class="manager-form-holder">
                        <table class="widefat" id='wlcc_set' style="width:100%;text-align: left;" cellspacing="0">
                            <thead>
                                <tr style="width:100%;">
                                    <th colspan="3"><?php _e('Add Schedule','wl-contentcontrol'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="width:100%;">
                                     <td style="width: 20%;border-bottom: 1px solid #eeeeee;">
                                         <label for="">Action</label>
                                        <select class="form-control wlm-select wlm-select-action" name="content_action" placeholder="Select Action" style="width: 100%">
                                            <option value="set">Set Content Status</option>
                                            <?php if ( $support_categories ) : ?>
                                                <option value="add">Add Content to a Category</option>
                                                <option value="move">Move Content to a Category</option>
                                            <?php endif; ?>
                                            <option value="repost">Repost Content</option>
                                        </select>
                                     </td>
                                     <td style="width: 20%; border-bottom: 1px solid #eeeeee;">
                                         <label for="">Schedule</label>
                                        <input id="DateRangePicker" type="text" class="form-control wlm-datetimepicker" value="" name="schedule_date" placeholder="Schedule Date">
                                     </td>
                                     <td style="width: 60%; border-bottom: 1px solid #eeeeee;">
                                            <div class="form-group membership-level-select action-moveadd-holder d-none">
                                                 <?php $cats = get_categories('hide_empty=0'); ?>
                                                 <label for="">Category</label>
                                                <select class="form-control wlm-select-cat" name="content_cat[]" multiple="multiple" placeholder="Select Categories" style="width: 100%">
                                                    <?php foreach((array)$cats AS $cats): ?>
                                                        <option value="<?php echo $cats->cat_ID; ?>"><?php echo $cats->name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group action-status-holder">
                                                 <label for="">Status</label>
                                                <select class="form-control wlm-select wlm-select-status" name="content_status" placeholder="Select Status" style="width: 100%">
                                                    <option value="publish">Published</option>
                                                    <option value="pending">Pending Review</option>
                                                    <option value="draft">Draft</option>
                                                    <option value="trash">Trash</option>
                                                </select>
                                            </div>
                                            <div class="form-group action-repost-holder d-none">
                                                <div class="row">
                                                    <div style="float: left; width: 20%;">
                                                        <label for="">Every</label>
                                                        <input type="number" min="1" max="999999" class="form-control" name="content_every">
                                                    </div>
                                                    <div style="float: left; width: 40%;">
                                                        <label for="">&nbsp;</label>
                                                        <select class="form-control wlm-select-by" name="content_by" placeholder="Select Frequency" style="width: 100%">
                                                            <option value="day">Day/s</option>
                                                            <option value="month">Month/s</option>
                                                            <option value="year">Year/s</option>
                                                        </select>
                                                    </div>
                                                    <div style="float: left; width: 40%; padding-left: 5%;">
                                                        <label for="">Repitition</label>
                                                        <input type="number" min="1" max="999999" class="form-control" name="content_repeat">
                                                    </div>
                                                </div>
                                            </div>
                                     </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="text-align: right; padding-top: 4px; padding-bottom: 8px;">
                            <div class="wlm-message" style="display: none"><?php _e('Saved', 'wishlist-member'); ?></div>
                            <a href="#" class="wlm-btn -with-icons -success -centered-span wlm-manager-save">
                                <i class="wlm-icons"><img src="<?php echo $WishListMemberInstance->pluginURL3; ?>/ui/images/baseline-save-24px.svg" alt=""></i>
                                <span><?php _e('Save Schedule', 'wishlist-member'); ?></span>
                            </a>
                        </div>
                    </div>
                    <table class="widefat" id='wlcc_manager_table' style="width:100%;text-align: left;" cellspacing="0">
                        <thead>
                            <tr style="width:100%;">
                                <th style="border-bottom: 1px solid #aaaaaa;"><?php _e('Schedules','wl-contentcontrol'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( count($content_schedules) > 0 ) : ?>
                                <?php foreach ( $content_schedules as $sched): ?>
                                    <tr>
                                        <td style="border-bottom: 1px solid #eeeeee;">
                                            <span class='wlm-manage-sched' style="vertical-align: middle;">
                                                <?php
                                                    $str = "";
                                                    $v = $sched['value'];
                                                    switch ($sched["type"]) {
                                                        case 'move':
                                                            if ( $v->action == "move" ) $str = "Move to ";
                                                            else $str = "Add to ";
                                                            $cat = explode('#',$v->categories);
                                                            $t = [];
                                                            foreach((array)$cat AS $cati=>$c) {
                                                                $category = get_term_by('id', $c, 'category');
                                                                $t[] = $category->name;
                                                            }
                                                            $str .= implode(",", $t);
                                                            $str .= " on <strong>" .$WishListMemberInstance->FormatDate( $v->due_date, 0 ) ."</strong>";
                                                            break;
                                                        case 'repost':
                                                            $str = "Repost";
                                                            $str .= " on <strong>" .$WishListMemberInstance->FormatDate( $v->due_date, 0 ) ."</strong>.";
                                                            if ( $v->rep_num > 0 ) {
                                                                $every = array('day'=>'Day/s','month'=>'Month/s','year'=>'Year/s');
                                                                $str .= ' Repeat every <strong>'.$v->rep_num .' ' .$every[$v->rep_by] ."</strong>.";
                                                                   $d1 = date_parse($v->due_date);
                                                                   if ( $v->rep_by == 'day' ) {
                                                                        $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$v->rep_num),$d1['year']);
                                                                   } else if ( $v->rep_by == 'month' ) {
                                                                        $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],($d1['month']+$v->rep_num),$d1['day'],$d1['year']);
                                                                   } else if ( $v->rep_by == 'year' ) {
                                                                       $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],($d1['year']+$v->rep_num));
                                                                   } else {
                                                                       $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$v->rep_num),$d1['year']);
                                                                   }

                                                                   if ( $v->rep_end > 0 ) {
                                                                        $str .= " Next due date is on <strong>" .$WishListMemberInstance->FormatDate( date('Y-m-d H:i:s',$new_bue_date), 0 ) ."</strong> (" .($v->rep_end -1) ." repetition/s left)";
                                                                   } else {
                                                                       $str.=' No repetition limit.';
                                                                   }
                                                            }
                                                            break;
                                                        case 'set':
                                                            $stats = array('publish'=>'Published','pending'=>'Pending Review','draft'=>'Draft','trash'=>'Trash');
                                                            $str = "Set content status to " .$stats[$v->status];
                                                            $str .= " on <strong>" .$WishListMemberInstance->FormatDate( $v->due_date, 0 ) ."</strong>.";
                                                            break;
                                                    }
                                                    echo $str;
                                                ?>
                                            </span>
                                            <span class="wlm-manage-actions" style="float: right; vertical-align: middle;">
                                                <a href="#" class="wlm-manager-remove" type="<?php echo $sched["type"] ?>" id="<?php echo $v->id ?>">remove</a>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                    <tr class="empty-tr">
                                        <td style="border-bottom: 1px solid #eeeeee;">
                                            <span class='wlm-manage-sched' style="vertical-align: middle;">
                                                - No schedule -
                                            </span>
                                            <span class="wlm-manage-actions" style="float: right; vertical-align: middle;">
                                                <a href="#" class="wlm-manager-remove" type="" id="">remove</a>
                                            </span>
                                        </td>
                                    </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        <?php
        }

        //save post expiry date
        function UpdatePostManagerDate($id,$data){
            global $wpdb;
            $table1 = $wpdb->prefix."wlcc_contentmanager_repost";
            $table2 = $wpdb->prefix."wlcc_contentmanager_move";
            $table3 = $wpdb->prefix."wlcc_contentmanager_set";

            if($data['action'] =='move'){
                    $q = "UPDATE $table2 SET due_date='" .$data['date'] ."',categories='" .$data['cats'] ."',action='" .$data['method'] ."' WHERE id=" .$id;
            }else if($data['action'] =='repost'){
                    $q = "UPDATE $table1 SET due_date='" .$data['date'] ."',rep_num=" .$data['rep_num'] .",rep_by='" .$data['rep_by'] ."',rep_end=" .$data['rep_end'] ." WHERE id=" .$id;
            }else if($data['action'] =='set'){
                   $q = "UPDATE $table3 SET due_date='" .$data['date'] ."',status='" .$data['status'] ."' WHERE id=" .$id;
            }
            $wpdb->query($q);
        }
        //save post expiry date
        function SavePostManagerDate($post_id,$data){
            global $wpdb;
            $table1 = $wpdb->prefix."wlcc_contentmanager_repost";
            $table2 = $wpdb->prefix."wlcc_contentmanager_move";
            $table3 = $wpdb->prefix."wlcc_contentmanager_set";
            $q = "";
            if($data['action'] =='move'){
                if(is_array($post_id)){
                    foreach($post_id as $key=>$value){
                        $q = "INSERT INTO $table2(post_id,due_date,categories,action) VALUES('" .$value ."','" .$data['date'] ."','" .$data['cats'] ."','" .$data['method']."')";
                        $wpdb->query($q);
                    }
                }else{
                    $q = "INSERT INTO $table2(post_id,due_date,categories,action) VALUES('" .$post_id ."','" .$data['date'] ."','" .$data['cats'] ."','" .$data['method']."')";
                    $wpdb->query($q);
                }
            }else if($data['action'] =='repost'){
                if(is_array($post_id)){
                    foreach($post_id as $key=>$value){
                        $q = "INSERT INTO $table1(post_id,due_date,rep_num,rep_by,rep_end) VALUES('" .$value ."','" .$data['date'] ."'," .$data['rep_num'] .",'" .$data['rep_by'] ."'," .$data['rep_end'].")";
                        $wpdb->query($q);
                    }
                }else{
                    $q = "INSERT INTO $table1(post_id,due_date,rep_num,rep_by,rep_end) VALUES('" .$post_id ."','" .$data['date'] ."'," .$data['rep_num'] .",'" .$data['rep_by']."'," .$data['rep_end'].")";
                    $wpdb->query($q);
                }
            }else if($data['action'] =='set'){
                if(is_array($post_id)){
                    foreach($post_id as $key=>$value){
                        $q = "INSERT INTO $table3(post_id,due_date,status) VALUES('" .$value ."','" .$data['date'] ."','" .$data['status']."')";
                        $wpdb->query($q);
                    }
                }else{
                    $q = "INSERT INTO $table3(post_id,due_date,status) VALUES('" .$post_id ."','" .$data['date'] ."','" .$data['status']."')";
                    $wpdb->query($q);
                }
            }
            return $wpdb->insert_id;
        }
        //get post expiry date of the post
        function GetPostManagerDate($action,$post_id='',$due_id='',$start=0,$limit=0){
            global $wpdb;
            $table = $wpdb->prefix."wlcc_contentmanager_" .$action;
            $limit = $limit > 0 ? " LIMIT " .$start ."," .$limit : "";
            $where = "";
            if($post_id != ''){
                $where = " WHERE post_id" .(is_array($post_id)? (" IN (" .implode(',',$post_id) .") "):("=" .$post_id ." "));
            }else if($due_id != ''){
                $where = " WHERE id" .(is_array($due_id)? (" IN (" .implode(',',$due_id) .") "):("=" .$due_id ." "));
            }else{
                return array();
            }
            $q = "SELECT * FROM $table $where ORDER BY due_date ASC". $limit;
            return $wpdb->get_results($q);
        }
        //get due date
        function GetDueDate($action,$dueid='',$start=0,$limit=0){
            global $wpdb;
            $table = $wpdb->prefix."wlcc_contentmanager_" .$action;
            $limit = $limit > 0 ? " LIMIT " .$start ."," .$limit : "";

            if(is_array($dueid)){
                $post_ids = implode(',',$dueid);
                $q = "SELECT * FROM $table WHERE id IN (" .$dueid .") ORDER BY due_date ASC". $limit;
            }else{
                if($dueid!=''){
                    $q = "SELECT * FROM $table WHERE id=" .$dueid ." ORDER BY due_date ASC". $limit;
                }else{
                    $q = "SELECT * FROM $table ORDER BY date_added DESC". $limit;
                }
            }
            return $wpdb->get_results($q);
        }
        //delete post expiry date by id
        function DeletePostManagerDate($id,$action){
            global $wpdb;
            $table = $wpdb->prefix."wlcc_contentmanager_" .$action;
                if(is_array($id)){
                    $ids = implode(',',$id);
                    $q = "DELETE FROM $table WHERE id IN (" .$ids .")";
                }else{
                    $q = "DELETE FROM $table WHERE id=" .$id;
                }
            $wpdb->query($q);
        }
        //delete post expiry date by id
        function DeletePostManagerDate_byPostId($id,$action){
            global $wpdb;
            $table = $wpdb->prefix."wlcc_contentmanager_" .$action;
                if(is_array($id)){
                    $ids = implode(',',$id);
                    $q = "DELETE FROM $table WHERE post_id IN (" .$ids .")";
                }else{
                    $q = "DELETE FROM $table WHERE post_id=" .$id;
                }
            $wpdb->query($q);
        }
        //retrieve all posts or with expiry only
            function GetPosts($action,$show_all=false,$show_poststat='all',$ptype='post',$start=0,$per_page=0,$sort="ID",$asc=1){
                global $wpdb,$WishListMemberInstance;
                $table1 = $wpdb->prefix."posts";

                $limit = "";
                if($per_page >0) $limit =  " LIMIT " .$start ."," .$per_page;
                $order = " ORDER BY " .$sort .($asc == 1 ? " ASC":" DESC");

                if($show_all){
                    if($show_poststat == "all"){
                        $post_status_filter = " AND post_status IN ('publish','draft','trash','pending')";
                        $q = "SELECT ID,post_author,post_date,post_status,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}'" .$post_status_filter  .$order .$limit;
                    }else{
                        $post_status_filter = ($show_poststat!="")?(" AND post_status='" .$show_poststat ."'"):(" AND post_status='publish'");
                        $q = "SELECT ID,post_author,post_date,post_status,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}'" .$post_status_filter .$order .$limit;
                    }
                }else{
                    $table2 = $wpdb->prefix."wlcc_contentmanager_" .$action;
                    $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_date,$table1.post_status,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 INNER JOIN $table2 ON  $table1.ID=$table2.post_id AND $table1.post_type='{$ptype}'" .$order .$limit;
                }

                return $wpdb->get_results($q);
            }

        //retrieve all posts or with expiry only
        function ApplyDueDate(){
            global $wpdb,$WishListMemberInstance;
            $table = $wpdb->prefix."posts";
            $table1 = $wpdb->prefix."wlcc_contentmanager_repost";
            $table2 = $wpdb->prefix."wlcc_contentmanager_move";
            $table3 = $wpdb->prefix."wlcc_contentmanager_set";
            $wlcc_status=$WishListMemberInstance->GetOption('wlcc_status');
            if(!$wlcc_status['ContentManager'])return false; // skip when disabled

           $q = "SELECT * FROM $table1 WHERE due_date <= '" .date('Y-m-d H:i:s') ."'";
           $res = $wpdb->get_results($q);
           foreach((array)$res as $result):
                   $wpdb->query("UPDATE $table SET post_date='" .$result->due_date ."', post_date_gmt='" .$result->due_date ."' WHERE ID=" .$result->post_id);
                   //check for repetition
                   $rep_num = $result->rep_num;
                   $rep_end = $result->rep_end;
                   if($rep_num > 0){
                       $d1 = date_parse($result->due_date);
                       if($result->rep_by == 'day'){
                            $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$rep_num),$d1['year']);
                       }else if($result->rep_by == 'month'){
                            $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],($d1['month']+$rep_num),$d1['day'],$d1['year']);
                       }else if($result->rep_by == 'year'){
                           $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],($d1['year']+$rep_num));
                       }else{
                           $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$rep_num),$d1['year']);
                       }
                       if($rep_end > 0){
                          if($rep_end == 1){
                              $this->DeletePostManagerDate($result->id,'repost');
                          }else{
                              $rep_end = $rep_end-1;
                          }
                       }
                       $datum = array('action'=>'repost','date'=>date('Y-m-d H:i:s',$new_bue_date),'rep_num'=>$rep_num,'rep_by'=>$result->rep_by,'rep_end'=>$rep_end);
                       $this->UpdatePostManagerDate($result->id,$datum);
                   }else{ //if not repeated then delete
                    $this->DeletePostManagerDate($result->id,'repost');
                   }
           endforeach;

           $q = "SELECT * FROM $table2 WHERE due_date <= '" .date('Y-m-d H:i:s') ."'";
           $res = $wpdb->get_results($q);
           foreach((array)$res as $result):
               $cat = explode('#',$result->categories);
               if($result->action == "add"){
                   $cur_cat = wp_get_post_categories($result->post_id);
                   $x = array_merge((array)$cat,(array)$cur_cat);
                   $cat = array_unique((array)$x);
               }
               $catpost = array();
               $catpost['ID'] = $result->post_id;
               $catpost['post_category'] = $cat;
               $ret = wp_update_post($catpost);
               $this->DeletePostManagerDate($result->id,'move');
           endforeach;

           $q = "SELECT * FROM $table3 WHERE due_date <= '" .date('Y-m-d H:i:s') ."'";
           $res = $wpdb->get_results($q);
           foreach((array)$res as $result):
                   $wpdb->query("UPDATE $table SET post_status='" .$result->status ."' WHERE ID=" .$result->post_id);
                   $this->DeletePostManagerDate($result->id,'set');
           endforeach;
        }
        /*
            OTHER FUNCTIONS NOT CORE OF CONTENT ARCHIVER GOES HERE
        */
        //Save current selection in the dropdown
        function SaveView(){
            $wpm_current_user=wp_get_current_user();
            if(!session_id()){
                session_start();
            }
            if($wpm_current_user->caps['administrator']){
                $mode = isset($_POST['mode']) ? $_POST['mode']:$_GET['mode'];
                if($mode != ""){
                    $_SESSION['wlcmmode'] = $mode;
                }
                $ptype = isset($_POST['ptype']) ? $_POST['ptype']:$_GET['ptype'];
                if($ptype != ""){
                    $_SESSION['wlcmptype'] = $ptype;
                }
                if(isset($_POST['frmsubmit'])){
                    $show_post = isset($_POST['show_post']) ? $_POST['show_post']:$_GET['show_post'];
                    $show_post_stat = isset($_POST['show_post_stat']) ? $_POST['show_post_stat']:$_GET['show_post_stat'];

                    $_SESSION['wlcmshowpost'] = $show_post;
                    if($show_post == 'all' && $show_post_stat !=""){
                        $_SESSION['wlcmshowpoststat'] = $show_post_stat;
                    }
                }
            }
        }
        //function for string
        function cut_string($str, $length, $minword){
            $sub = '';
            $len = 0;
            foreach (explode(' ', $str) as $word){
                $part = (($sub != '') ? ' ' : '') .$word;
                $sub .= $part;
                $len += strlen($part);
                if (strlen($word) > $minword && strlen($sub) >= $length)
                break;
            }
            return $sub . (($len < strlen($str)) ? '...' : '');
        }
        //function to get date difference needs php5.2
        function isvalid_date($date){
            $ret = false;
            if($date > date('Y-m-d H:i:s')){
                $ret = true;
            }
            return $ret;
        }
        //function to format the date
        function format_date($date,$format='M j, Y g:i a'){
            $d1 = date_parse($date);
            $pdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
            $date = date($format,$pdate);
            return $date;
        }
        //validate integer
        function validateint($inData) {
            $intRetVal = false;
          $IntValue = intval($inData);
          $StrValue = strval($IntValue);
          if($StrValue == $inData) {
            $intRetVal = true;
          }

          return $intRetVal;
        }
    }//End of ContentArchiver Class
}
?>