<?php
/*
 * Content Scheduler Module
 * Version: 1.1.34
 * SVN: 34
 * @version $Rev: 34 $
 * $LastChangedBy: ronaldo $
 * $LastChangedDate: 2017-08-02 13:47:45 -0400 (Wed, 02 Aug 2017) $
 *
 */
if(!class_exists('WLM3_ContentScheduler')){
	/**
	 * Content Scheduler Core Class
	 */
	class WLM3_ContentScheduler{
        private $debug = false;
        /**
         * Content Scheduler Constructor
         */
        function __construct(){
            global $WishListMemberInstance;
            //used to debug queries
            // domain.com?debug=<licensekey> will disyplay the debug post query
            $debug = isset($_GET['wlcc_debug']) && $_GET['wlcc_debug'] != "" ? $_GET['wlcc_debug'] : false;
            if ( $debug && $debug == $WishListMemberInstance->GetOption('LicenseKey') ) {
                $this->debug = true;
            }
        }

        //activate module
        function load_hooks(){
            // widget setup
            if(function_exists('register_sidebar_widget')){
                register_sidebar_widget('WishList Content Scheduler',array(&$this,'SchedWidget'),null);
                register_widget_control('WishList Content Scheduler',array(&$this,'SchedWidgetAdmin'));
            }

            //save Content Drip Options when savign the post
            add_action('wp_insert_post',array(&$this,'SaveContentSchedOptions'));

            add_action('wishlistmember3_post_page_options_menu',array(&$this,'wlm3_post_options_menu'));
            add_action('wishlistmember3_post_page_options_content',array(&$this,'ContentSchedPostsOptions'));

            add_filter( 'wishlistmember_shortcodes', array( $this, 'add_shortcode' ), 9999 );

            add_shortcode( 'scheduled-contents', array(&$this, 'process_shortcode') );

            if ( ! is_admin() ) { //do not run filters on admin area
                //hooks for Content Scheduler
                add_filter('posts_join',array(&$this,'WLMSchedContentJoin'),9999);
                add_filter('posts_where',array(&$this,'WLMSchedContentWhere'),9999);
                //add_filter('posts_orderby',array(&$this,'WLMSchedContentOrder'),9999); //remove we dont interfere with sorting anymore
                add_filter('posts_groupby',array(&$this,'WLMSchedContentGroup'),9999);

                //hooks for next and previous links for Content Scheduler
                add_filter('get_next_post_where',array(&$this,'WLMSchedAdjacentWhereNext'),9999);
                add_filter('get_previous_post_where',array(&$this,'WLMSchedAdjacentWherePrevious'),9999);

                add_filter('the_posts', array(&$this,'posts_pages_list'),9999); //use to filter the date
                add_filter('the_content', array(&$this, 'TheContent'),9999); //add private tag
                add_filter('posts_clauses', array(&$this,'my_posts_clause_filter'));

                add_filter( 'get_terms', array(&$this,'SchedTermFilter'),9999,3);

                add_filter('pre_get_posts', array(&$this, 'PreGetPost'));

                //filter for get_pages function because it does not use WP_Query
                add_filter('get_pages', array(&$this, 'GetPages'),9999,2);

                //filter  for menu items
                add_filter('wp_get_nav_menu_items', array(&$this, 'WpGetNavMenuItems'), 9999);

                if ( $this->debug ) {
                    add_filter( 'posts_request',array(&$this, 'debug_query'));
                }
            }

        }

        //deactivate module
        function remove_hooks(){
            //save Content Drip Options when savign the post
            remove_action('wp_insert_post',array(&$this,'SaveContentSchedOptions'));

            //hooks for Content Scheduler
            remove_filter('posts_join',array(&$this,'WLMSchedContentJoin'));
            remove_filter('posts_where',array(&$this,'WLMSchedContentWhere'));
           // remove_filter('posts_orderby',array(&$this,'WLMSchedContentOrder'));
            remove_filter('posts_groupby',array(&$this,'WLMSchedContentGroup'));

            //hooks for next and previous links for Content Scheduler
            remove_filter('get_next_post_where',array(&$this,'WLMSchedAdjacentWhereNext'));
            remove_filter('get_previous_post_where',array(&$this,'WLMSchedAdjacentWherePrevious'));

            remove_filter('the_posts', array(&$this,'posts_pages_list'));
            remove_filter('the_content', array(&$this, 'TheContent')); //add private tag
            remove_filter( 'posts_clauses', array(&$this,'my_posts_clause_filter'));

            remove_filter( 'get_terms', array(&$this,'SchedTermFilter'));
            remove_filter('pre_get_posts', array(&$this, 'PreGetPost'));
            remove_filter('get_pages', array(&$this, 'GetPages'));
            remove_filter('wp_get_nav_menu_items', array(&$this, 'WpGetNavMenuItems'));
            //used to debug queries
            remove_filter( 'posts_request',array(&$this, 'debug_query'));

            remove_action('wishlistmember3_post_page_options_menu',array(&$this,'wlm3_post_options_menu'));
            remove_action('wishlistmember3_post_page_options_content',array(&$this,'ContentSchedPostsOptions'));
        }

        /**
         * Content Scheduler Table creation
         */
        function Activate(){
            global $wpdb,$WishListMemberInstance;
            //cleanup code -> delete records where membership level does not exist
            $wpm_levels=$WishListMemberInstance->GetOption('wpm_levels');
            $table = $wpdb->prefix ."wlcc_contentsched";
            if(count($wpm_levels)>0){
                $in = "'" .implode("','",array_keys($wpm_levels))."'";
                $query = "DELETE FROM {$table} WHERE mlevel NOT IN ({$in})";
                $wpdb->query($query);
            }
        }

        function wlm3_post_options_menu() {
            echo '<li><a href="#" data-target=".wlm-inside-scheduler" class="wlm-inside-toggle">Scheduler</a></li>';
        }
        /**
         * Content Scheduler Post Option Area
         */
        function ContentSchedPostsOptions(){
            $post_id = $_GET['post'];
            $custom_types = get_post_types(array('public'=> true,"_builtin"=>false));
            $ptypes = array_merge(array("post","page"),$custom_types);
            $post_type = $post_id ? get_post_type($post_id):$_GET['post_type'];
            $post_type = $post_type ? $post_type : 'post';
            if($post_type){
                 if (!in_array($post_type,$ptypes) )return false; //do not display option on pages
            }else{
                return false;
            }

            global $WishListMemberInstance;
            $wpm_levels=$WishListMemberInstance->GetOption('wpm_levels');
            ?>
            <script type='text/javascript' src='<?php echo $WishListMemberInstance->legacy_wlm_url ?>/admin/post_page_options/content-control/js/scheduler.js'></script>
            <div class="wlm-inside wlm-inside-scheduler" style="display: none;">
                <table class="widefat" id='wlcc_ca' style="text-align: left;" cellspacing="0">
                    <thead>
                        <tr style="width:100%;">
                            <th style="width: 40%;"> <?php _e('Membership Level/s'); ?></th>
                            <th style="width: 30%;"> <?php _e('Show After'); ?> </th>
                            <th style="width: 30%;"> <?php _e('Show For'); ?> </th>
                        </tr>
                    </thead>
                </table>
                <div id="wlcclevels_ca" style="text-align:left;overflow:auto;">
                    <table class="widefat" id="wlcc_ca" cellspacing="0" style="text-align:left;">
                        <tbody>
                        <?php foreach ( (array)$wpm_levels AS $id=>$level ) : ?>
                        <?php
                            $post_sched_data = [];
                            if ( $post_id != '' ) $post_sched_data = $this->GetContentSched($post_id,$id,0,0,'',array('publish','draft','pending'));
                        ?>
                            <tr id="tr<?php echo $id;?>" style="width:100%;" class="<?php echo $alt++%2?'':'alternate'; ?>">
                                <td style="width: 40%;border-bottom: 1px solid #eeeeee;"><strong><?php echo $level['name']; ?></strong></td>
                                <td style="width: 30%;border-bottom: 1px solid #eeeeee;">
                                    <input style='text-align:center; width: 100px; display: inline;' size='5' type='number' class="form-control scheddays" name='scheddays[<?php echo $id; ?>]' value='<?php echo $post_sched_data[0]->num_days ? $post_sched_data[0]->num_days : "" ?>' /> days
                                </td>
                                <td style="width: 30%;border-bottom: 1px solid #eeeeee;">
                                    <input style='text-align:center; width: 100px; display: inline;' size='5' type='number' class="form-control hidedays" name='hidedays[<?php echo $id; ?>]' value='<?php echo $post_sched_data[0]->hide_days ? $post_sched_data[0]->hide_days : "" ?>' /> days
                                </td>
                            </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                </div>
                <div style="text-align: right; padding-top: 4px; padding-bottom: 8px;">
                    <div class="wlm-message" style="display: none"><?php _e('Saved', 'wishlist-member'); ?></div>
                    <a href="#" class="wlm-btn -with-icons -success -centered-span wlm-scheduler-save">
                        <i class="wlm-icons"><img src="<?php echo $WishListMemberInstance->pluginURL3; ?>/ui/images/baseline-save-24px.svg" alt=""></i>
                        <span><?php _e('Save Schedule', 'wishlist-member'); ?></span>
                    </a>
                </div>
            </div>
            <?php
        }
    	/**
    	 * Saving Values on Content Scheduler Posts Options
    	 */
        function SaveContentSchedOptions(){
            global $WishListMemberInstance;
            $post_ID = $_POST['post_ID'];

            if ( $post_ID != "" && ( isset( $_POST['scheddays'] ) || isset( $_POST['hidedays'] ) ) ) {//save if theres post id
                global $WishListMemberInstance;
                $wpm_levels=$WishListMemberInstance->GetOption('wpm_levels'); // get the membership levels
                $scheddays = isset( $_POST['scheddays'] ) ? $_POST['scheddays'] : array();
                $hidedays = isset( $_POST['hidedays'] ) ? $_POST['hidedays'] : array();
                $wlm_contentsched_Option = array(); $arr = array();
                foreach((array)$wpm_levels AS $id=>$level){
                    $days_delay = isset( $scheddays[$id] ) ? $scheddays[$id] : 0;
                    $hide_delay = isset( $hidedays[$id] ) ? $hidedays[$id] : 0;
                    if ( $days_delay > 0 ) { // save the sched days greater than zero only
                        $arr[$id] = $scheddays[$id];
                        $this->SaveContentSched($post_ID,$id,$days_delay,$hide_delay);
                    }else{
                        $this->DeleteContentSched($post_ID,$id);
                    }
                }
                if(count($arr) < 1){ //if all levels have no value, delete all the sched value for this post
                    $this->DeleteContentSched($post_ID);
                }
            }
        }

        function add_shortcode( $shortcodes ) {
					//content scheduler custom post types
					$cs_post_types = [
						'all' => array( 'label' => 'All' ),
						'post' => array( 'label' => 'Posts' ),
						'page' => array( 'label' => 'Pages' ),
					];
					;
					foreach ( (array) get_post_types( array( '_builtin' => false), 'objects' ) as $key => $value ) {
						if( isset( $value->label ) ) {
							$cs_post_types[$key] = array( 'label' => $value->label );
						}
					}
					
					$shortcodes['scheduled-contents'] = array(
						'label' => 'Content Scheduler',
						'attributes' => array(
							'title' => array(
								'columns' => 6,
								'label' => 'Title',
								'default' => 'My Upcoming Posts',
							),
							'ptype' => array(
								'columns' => 6,
								'label' => 'Post Type',
								'type' => 'select',
								'options' => $cs_post_types,
								'default' => 'all'
							),
							'showpost' => array(
								'columns' => 4,
								'label' => 'Number of Content',
								'type' => 'number',
								'default' => 10,
							),
							'px' => array(
								'columns' => 4,
								'label' => 'List Spacing',
								'type' => 'number',
								'default' => 4,
							),
							'separator' => array(
								'columns' => 4,
								'label' => 'Date Separator',
								'default' => '@',
							),
							'sort' => array(
								'columns' => 4,
								'label' => 'Order by',
								'type' => 'select',
								'options' => array(
									'title' => array( 'label' => 'Title' ),
									'ID' => array( 'label' => 'ID' ),
									'menu_order' => array( 'label' => 'Menu Order' ),
									'date' => array( 'label' => 'Schedule Date' ),
									'days' => array( 'label' => 'Days' ),
								)
							),
							'showdate' => array(
								'columns' => 4,
								'label' => '&nbsp;',
								'type' => 'checkbox',
								'options' => array(
									'yes' => array( 'label' => 'Show Date' ),
								),
								'default' => 'yes',
							),
							'showtime' => array(
								'columns' => 4,
								'label' => '&nbsp;',
								'type' => 'checkbox',
								'options' => array(
									'yes' => array( 'label' => 'Show Time' ),
								),
								'default' => 'yes',
							),
						),
					);
          return $shortcodes;
        }
    	/**
    	 * Function to get Scheduled Content to hide
             * Return: Array(); days,date,differnce from the current date
    	 */
        function GetSchedContent( $ptype='' ) {
            global $WishListMemberInstance,$wlmpl_post_login;
            static $post_type = "";
            static $sched_posts = null;
            if ( $ptype == $post_type && ! is_null( $sched_posts ) ) {
                return $sched_posts;
            }
            $post_type = $ptype;
            $date_today = date('Y-m-d H:i:s'); // get date today
            $wpm_current_user=wp_get_current_user();// get the current user
            $levels=$WishListMemberInstance->GetMemberActiveLevels($wpm_current_user->ID); // get users membership levels
            $pplevel = array();
            $user_pp_posts = array();
            //remove payper post membership level
            foreach((array)$levels as $id=>$level){
                if(strpos($level, "U") !== false){
                    $pplevel[] = $level;
                    unset($levels[$id]);
                }
            }

            if( method_exists( $WishListMemberInstance, 'GetUser_PayPerPost' ) &&  count($pplevel) > 0 ) {
                $user_pp_posts = $WishListMemberInstance->GetUser_PayPerPost( $pplevel, false , null, true );
            }

            if(count($levels) > 0){
                $mlevel_post = $this->GetContentSched('',$levels,0,0,$ptype); //get all the scheduled contents of the levels
            }else{
                $mlevel_post = $this->GetContentSched(); //if not logged in or dont have membership level, dont show content with sched
            }

            $sched_posts = array(); //holds the posts that is sched
            $has_access  = array(); //holds post that has access, temporary container

            // check all the post
            foreach((array)$mlevel_post as $lvl_post){
                if(count($levels) > 0){ // skip this part if he has no membership level
                    //get the post details
                    $date_diff = "";
                    $date2post =  "";
                    $newpostdate = "";
                    $newpost_diff = "";
                    $hidedate = "";
                    $hide_diff = 0;
                    $post_details = get_post($lvl_post->post_id);
                    $post_date = $post_details->post_date;
                    //get user level timestamp
                    $userlvltimestamps = $WishListMemberInstance->UserLevelTimestamps($wpm_current_user->ID,$lvl_post->mlevel);
                    $userlvltimestamp = $userlvltimestamps[$lvl_post->mlevel];
                    $user_leveldate = 0;
                    if($userlvltimestamp != ""){
                        $user_leveldate = date('Y-m-d H:i:s',$userlvltimestamp);
                    }
                    //get the post date diff and the level timestamp diff
                    $post_diff = $this->date_diff($post_date,$date_today,86400);
                    $level_diff = $this->date_diff($user_leveldate,$date_today,86400);

                    //get the nearest lowest date diff... whichever the latest
                    $date_diff =  $post_diff < $level_diff ? $post_diff:$level_diff;
                    //use the date of whoever has the lowest difference
                    $date2post =  $post_diff < $level_diff ? $post_date:$user_leveldate;
                    $newpostdate = $this->get_sched_date($date2post, $lvl_post->num_days,'Y-m-d H:i:s');
                    $newpost_diff = $post_diff = $this->date_diff($date_today,$newpostdate,86400);
                    if ( $lvl_post->hide_days ) {
                        $hidedate = $this->get_sched_date($newpostdate, $lvl_post->hide_days,'Y-m-d H:i:s');
                        $hide_diff = $post_diff = $this->date_diff($date_today,$hidedate,86400);
                        // var_dump("{$lvl_post->post_id}={$hidedate}-{$newpostdate}={$hide_diff}");
                    }
                    if ( $newpost_diff > 0 && ! array_key_exists( $lvl_post->post_id, $has_access ) ) {
                        //hide post if the calculated post date is greater than today
                        if ( array_key_exists($lvl_post->post_id, $sched_posts ) ) {
                            if ( $sched_posts[$lvl_post->post_id]['newpost_diff'] > $newpost_diff )
                                $sched_posts[$lvl_post->post_id] = array('days'=>$lvl_post->num_days,'date'=>$date2post,'date_diff'=>$date_diff, 'new_date'=> $newpostdate, 'newpost_diff'=> $newpost_diff, "hidedate"=>$hidedate, "hide_diff"=> $hide_diff );
                        } else {
                            $sched_posts[$lvl_post->post_id] = array('days'=>$lvl_post->num_days,'date'=>$date2post,'date_diff'=>$date_diff, 'new_date'=> $newpostdate, 'newpost_diff'=> $newpost_diff, "hidedate"=>$hidedate, "hide_diff"=> $hide_diff );
                        }
                    } else {
                        //show post if the calculated post date is less than today
                        //and not yet hidden
                        if ( array_key_exists( $lvl_post->post_id,$sched_posts ) ) {
                            if ( $sched_posts[$lvl_post->post_id]['hide_diff'] >= 0 && $hide_diff >= 0) {
                                unset($sched_posts[$lvl_post->post_id]);
                            } else {
                                if ( $sched_posts[$lvl_post->post_id]['hide_diff'] > $hide_diff ) {
                                    $sched_posts[$lvl_post->post_id] = array('days'=>$lvl_post->num_days,'date'=>$date2post,'date_diff'=>$date_diff, 'new_date'=> $newpostdate, 'newpost_diff'=> $newpost_diff, "hidedate"=>$hidedate, "hide_diff"=> $hide_diff );
                                }
                            }
                        } else if( $hide_diff < 0 ) {
                            $sched_posts[$lvl_post->post_id] = array('days'=>$lvl_post->num_days,'date'=>$date2post,'date_diff'=>$date_diff, 'new_date'=> $newpostdate, 'newpost_diff'=> $newpost_diff, "hidedate"=>$hidedate, "hide_diff"=> $hide_diff );
                        }

                        $has_access[$lvl_post->post_id] = 1;
                    }
                } else {
                    if(array_key_exists($lvl_post->post_id,$sched_posts)){
                        if($sched_posts[$lvl_post->post_id]['days'] > $lvl_post->num_days){
                            $sched_posts[$lvl_post->post_id] = array('days'=>$lvl_post->num_days,'date'=>$date2post,'date_diff'=>$date_diff, 'new_date'=> $newpostdate, 'newpost_diff'=> $newpost_diff, "hidedate"=>$hidedate, "hide_diff"=> $hide_diff );
                        }
                    }else {
                        $sched_posts[$lvl_post->post_id] = array('days'=>$lvl_post->num_days,'date'=>$date2post,'date_diff'=>$date_diff , 'new_date'=> $newpostdate, 'newpost_diff'=> $newpost_diff, "hidedate"=>$hidedate, "hide_diff"=> $hide_diff );
                    }
                }
            }

            //Used for WL Post Login by Erwin
            if($wlmpl_post_login){
                if($WishListMemberInstance->Protect($wlmpl_post_login)){
                    unset($sched_posts[$wlmpl_post_login]);
                }
            }
            //End of WL Post Login Support

            //remove users pp post from the list
            if ( count( $user_pp_posts ) > 0 ) {
                foreach ( (array) $user_pp_posts as $uppp ) {
                    if( isset( $sched_posts[$uppp] ) ) {
                        unset($sched_posts[$uppp]);
                    }
                }
            }

            if ( $this->debug ) {
                echo "<!-- "; print_r( $sched_posts ); echo "-->";
            }
            return $sched_posts;
        }
    	/**
    	 * Function to Save Post Sched
    	 */
                function SaveContentSched($post_id,$mlevel,$num_days,$hide_days){
                    global $wpdb;
                    $table = $wpdb->prefix ."wlcc_contentsched";

                    if(is_array($post_id)){
                            $post_ids = implode(',',$post_id);
                            $q = "UPDATE $table SET num_days = '" .$num_days ."', hide_days = '" .$hide_days ."' WHERE mlevel=" .$mlevel ." AND post_id IN (" .$post_ids .")";
                    }else if(count($this->GetContentSched($post_id,$mlevel)) > 0){
                            $q = "UPDATE $table SET num_days = '" .$num_days ."', hide_days = '" .$hide_days ."' WHERE mlevel=" .$mlevel ." AND post_id=" .$post_id;
                    }else{
                            $q = "INSERT INTO $table(post_id,mlevel,num_days,hide_days) VALUES('$post_id','$mlevel','$num_days','$hide_days')";
                    }
                    $wpdb->query($q);
                }
    	/**
    	 * Function to get Post Sched
             * Return: Array()
    	 */
                function GetContentSched($post_id='',$mlevel='',$start=0,$limit=0,$ptype='', $pstatus = array('publish') ){
                    global $wpdb;
                    static $query = "";
                    static $result = null;
                    $table1 = $wpdb->prefix."posts as p";
                    $table2 = $wpdb->prefix ."wlcc_contentsched as sched";

                    if(is_array($mlevel)){
                            $q_mlevel = " sched.mlevel IN ('" .implode('\',\'',$mlevel) ."') ";
                    }else{
                            $q_mlevel = " sched.mlevel='" .$mlevel ."' ";
                    }

                    if(is_array($post_id)){
                            $q_post_id = " sched.post_id IN (" .implode(',',$post_id) .") ";
                    }else{
                            $q_post_id = " sched.post_id={$post_id}";
                    }

                    $qlimit = "";
                    if($limit > 0){
                        $qlimit = " LIMIT  " .$start ."," .$limit;
                    }

                    $post_type = $ptype != "" ? "p.post_type='{$ptype}' AND " : "";

                    if ( $pstatus && is_array( $pstatus ) ) {
                        $pstatus =  implode("','", $pstatus);
                        $post_status = "p.post_status IN ('{$pstatus}')";
                    } else {
                        $post_status = "p.post_status='publish'";
                    }

                    if($post_id!='' && $mlevel!=''){
                          $q = "SELECT sched.* FROM {$table2} INNER JOIN {$table1} ON {$post_type} p.ID=sched.post_id  AND {$post_status} AND {$q_post_id} AND {$q_mlevel} ORDER BY p.post_modified DESC" .$qlimit;
                    }else if($post_id!=''){
                          $q = "SELECT sched.* FROM {$table2} INNER JOIN {$table1} ON {$post_type} p.ID=sched.post_id  AND {$post_status} AND {$q_post_id} ORDER BY p.post_modified DESC" .$qlimit;
                    }else if($mlevel!=''){
                          $q = "SELECT sched.* FROM {$table2} INNER JOIN {$table1} ON {$post_type} p.ID=sched.post_id  AND {$post_status} AND {$q_mlevel} ORDER BY p.post_modified DESC" .$qlimit;
                    }else{
                          $q = "SELECT sched.* FROM {$table2} INNER JOIN {$table1} ON {$post_type} p.ID=sched.post_id  AND {$post_status} ORDER BY p.post_modified DESC" .$qlimit;
                    }

                    //if theres no change and the same query, lets return the previous result
                    if ( strcasecmp($query,$q) != 0 || is_null( $result ) ) {
                        $query  = $q;
                        $result = $wpdb->get_results($q);
                    }
                    return $result;
                }
    	/**
    	 * Function to REmove Post Sched
    	 */
                function DeleteContentSched($post_id,$mlevel=''){
                    global $wpdb;
                    $table = $wpdb->prefix."wlcc_contentsched";
                    if(is_array($post_id)){
                                    $post_ids = implode(',',$post_id);
                                    if($mlevel !=''){
                                            $q = "DELETE FROM $table WHERE mlevel='" .$mlevel ."' AND post_id IN (" .$post_ids .")";
                                    }else{
                                            $q = "DELETE FROM $table WHERE post_id IN (" .$post_ids .")";
                                    }
                    }else{
                                    if($mlevel !=''){
                                            $q = "DELETE FROM $table WHERE  mlevel='" .$mlevel ."' AND post_id=" .$post_id;
                                    }else{
                                            $q = "DELETE FROM $table WHERE post_id=" .$post_id;
                                    }
                    }
                    $wpdb->query($q);
                }
    	/**
    	 * Function to get Protected|Scheduled|ALL Posts
             * Return: Array()
    	 */
                function GetPosts($show_post,$ptype,$show_level='',$start=0,$per_page=0,$sort="ID",$asc=1){
                    global $wpdb,$WishListMemberInstance;
                    $table1 = $wpdb->prefix."posts";
                    $table2 = $wpdb->prefix."wlcc_contentsched";
                    $limit = "";
                    if($per_page >0) $limit =  " LIMIT " .$start ."," .$per_page;
                    $order = " ORDER BY " .$sort .($asc == 1 ? " ASC":" DESC");
                    if($show_post == 'all' || $show_post == ''){
                            $q = "SELECT ID,post_author,post_status,post_date,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish'" .$order .$limit;
                    }else if($show_post == 'sched'){
                       if($show_level == ''){
                            $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 INNER JOIN $table2 ON  $table1.ID=$table2.post_id AND $table1.post_type='{$ptype}' AND post_status='publish'" .$order .$limit;
                       }else{
                           $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 INNER JOIN $table2 ON  $table1.ID=$table2.post_id AND $table1.post_type='{$ptype}' AND post_status='publish' AND $table2.mlevel = '$show_level'" .$order .$limit;
                       }
                    }else if($show_post == 'protected'){
                        //get users protected post  for this level
                        //get users unprotected content for this user
                        $wpm_levels=$WishListMemberInstance->GetOption('wpm_levels');
                        $ids = array(); $has_all_access = false;
                        //check if the level has all access to post
                        if($wpm_levels[$show_level]['allposts']){
                            $has_all_access = true;
                        }
                        if($has_all_access){ //if the user has all access to posts
                            $q = "SELECT ID,post_author,post_status,post_date,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish'" .$order .$limit;
                        }else{
                           $x=$WishListMemberInstance->GetMembershipContent($ptype,$show_level);
                           $q = "SELECT ID,post_author,post_status,post_date,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish' AND ID IN(" .implode(',',$x).")" .$order .$limit;
                        }
                    }
                    return $wpdb->get_results($q);
                }

    	/**
    	 * Function to filter Scheduled Post from query during post request
             * Return: WHERE Query String
    	 */
                function WLMSchedContentWhere($where){
                    global $wpdb,$WishListMemberInstance;
                    $wpm_current_user=wp_get_current_user();
                    $table1 = $wpdb->prefix."posts";
                    $table2 = $wpdb->prefix."wlcc_contentsched";
                    $w = $where;
                    if(!$wpm_current_user->caps['administrator']){ // disregard content schedule for admin
                        //filter the post thats not to be shown
                        $arr = $this->GetSchedContent();
                        $sched_posts = array_keys($arr);
                        $qsched = count($sched_posts) > 0 ? " AND $table1.ID NOT IN (" .implode(',',$sched_posts).")" :"";
                        //get permalink structure
                        $permalink_structure =  get_option('permalink_structure');
                        if(is_single() && preg_match('/year|month|day/i',$permalink_structure)){
                            //remove the date in query
                            $w = trim(preg_replace('/\s+/', ' ', $w)); //removes new line and extra whitespaces, it causes regex not to work properly
                            $x = preg_replace("/.*(YEAR|MONTH|DAYOFMONTH|HOUR|MINUTE|SECOND)(.*?)(\s+AND)/","",$w);
                            if ( $x != $w ) {
                                $w = " AND " .$x;
                            }
                        }
                        $w .= $qsched ." ";
                    }
                    return $w;
                }
    	/**
    	 * Function to filter Scheduled Post from query during post request
             * Return: JOIN Query String
    	 */
                function WLMSchedContentJoin($join){
                    global $wpdb,$WishListMemberInstance;
                    $wpm_current_user=wp_get_current_user();
                    $wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');
                    $wpm_levels = array_keys($wpm_levels);
                    $table1 = $wpdb->prefix."posts";
                    $table2 = $wpdb->prefix."wlcc_contentsched";
                    $wpm_current_user=wp_get_current_user();
                    $j = $join;
                    if(!$wpm_current_user->caps['administrator']){  // disregard content schedule for admin
                        $levels=$WishListMemberInstance->GetMemberActiveLevels($wpm_current_user->ID); // get users membership levels
                        $x = array_diff((array)$wpm_levels,(array)$levels);
                        $qlevel = count($x) >0 ? " AND ($table2.mlevel NOT IN ('" .implode('\',\'',$x) ."') OR $table2.mlevel IS NULL)" :"";
                        $j .= " LEFT JOIN $table2 ON  ($table1.ID=$table2.post_id $qlevel ) ";
                    }
                    return $j;
                }
    	/**
    	 * Function to filter Scheduled Post from query during post request
             * Return: ORDER Query String
    	 */
                function WLMSchedContentOrder($order){
                    global $wpdb;
                    $table1 = $wpdb->prefix."posts";
                    $table2 = $wpdb->prefix."wlcc_contentsched";

                    $wpm_current_user=wp_get_current_user();
                    $o = $order;
                    if(!$wpm_current_user->caps['administrator'] && $wpm_current_user->ID > 0){  // disregard content schedule for admin and guest
                        $o = " post_date DESC ";
                    }
                    return $o;
                }
    	/**
    	 * Function to filter Scheduled Post from query during post request
             * Return: GROUP Query String
    	 */
                function WLMSchedContentGroup($group){
                    global $wpdb;
                    $table1 = $wpdb->prefix."posts";
                    $table2 = $wpdb->prefix."wlcc_contentsched";

                    $wpm_current_user=wp_get_current_user();
                     if(!$wpm_current_user->caps['administrator']){  // disregard content schedule for admin
                        $g = " $table1.ID ";
                        return $g;
                    } else {
                        return $group;
                    }

                }
    	/**
    	 * Function to filter Scheduled Post from NEXT query during post request
             * Return: NEXT WHERE Query String
    	 */
                function WLMSchedAdjacentWhereNext($where){
                    global $wpdb,$WishListMemberInstance,$post;
                    $wpm_current_user=wp_get_current_user();
                    $current_post_date = $post->post_date;
                    $current_post_id = $post->ID;
                    $w = $where;
                     if(!$wpm_current_user->caps['administrator']){  // disregard content schedule for admin
                        $p_id = $this->get_next_prev_id($current_post_id); //get the next id
                        $w = " WHERE p.post_status = 'publish' AND p.ID=" .$p_id;
                    }
                    return $w;
                }
    	/**
    	 * Function to filter Scheduled Post from PREVIOUS query during post request
             * Return: PREVIOUS WHERE Query String
    	 */
                function WLMSchedAdjacentWherePrevious($where){
                    global $wpdb,$WishListMemberInstance,$post;
                    $wpm_current_user=wp_get_current_user();
                    $current_post_date = $post->post_date;
                    $current_post_id = $post->ID;
                    $w = $where;
                     if(!$wpm_current_user->caps['administrator']){  // disregard content schedule for admin
                        $p_id = $this->get_next_prev_id($current_post_id,false); //get the previous id
                        $w = " WHERE p.post_status = 'publish' AND p.ID=" .$p_id;
                    }
                    return $w;
                }

    	/**
    	 * Function to update Scheduled post date from the returned array after the wp_query is executed
             * Return: ARray() of Post to be rendered in the site
    	 */
                function posts_pages_list($posts){
                    global $WishListMemberInstance;
                    $date_today = date('Y-m-d H:i:s'); // get date today
                    $wpm_current_user=wp_get_current_user();

                    //this part is important so that new post_date will be used by the post when displaying
                    if(!$wpm_current_user->caps['administrator'] && $wpm_current_user->ID > 0){  // disregard content schedule for admin and non users

                        foreach((array)$posts AS $key=>$post){
                            if ( isset( $posts[$key]->new_postdate ) ) {
                                $posts[$key]->post_date = $posts[$key]->new_postdate;
                            }
                        }
                    }
                    return $posts;
                }
    	/**
    	 * Function to insert upcoming post on the end of each post using a tag
             *
    	 */
                function TheContent($content) {
                    $wpm_current_user=wp_get_current_user();
                        //js functions
                        $js_showGMT = '<script type="text/javascript">
                                function tag_showdateGMT(unixtime){
                                    var currentDate=new Date(unixtime);
                                    var day = currentDate.getDate();
                                    var months = currentDate.getMonth()+1;
                                    var year = currentDate.getFullYear();
                                    if (day < 10){ day = "0" +day;}
                                    if (months < 10){ months = "0" +months;}
                                    var new_date = months +"/" +day +"/" +year;
                                    document.write(new_date);
                                }
                                function tag_showtimeGMT(unixtime){
                                    var currentDate=new Date(unixtime);
                                    var hours = currentDate.getHours();
                                    var minutes = currentDate.getMinutes();
                                    if(hours > 12){
                                      hours = hours - 12;
                                      add = " p.m.";
                                    }else{
                                      hours = hours;
                                      add = " a.m.";
                                    }
                                    if(hours == 12){ add = " p.m.";}
                                    if(hours == 00) {hours = "12";}
                                    if (minutes < 10){ minutes = "0" +minutes;}
                                    var new_time = hours +":" +minutes +" " +add;
                                    document.write(new_time);
                                }
                            </script>';
                        if(preg_match_all('/\[content-scheduler.*?\]/',$content, $matches)) {
                            if((is_single() || is_page()) AND $wpm_current_user->ID){
                                $content = $js_showGMT .$content;
                            }
                            foreach($matches[0] as $key=>$match){
                                $torem = array("content-scheduler","[","]");
                                $str = str_replace($torem,'', $match); //remove content-scheduler
                                $tag_params = explode(",",$str);
                                $new_tag_params = array();
                                foreach($tag_params as $key=>$param_value){
                                    $x = explode("=",$param_value);
                                    $new_tag_params[trim($x[0])] = trim($x[1]);
                                }
                                if(is_single() || is_page()){
                                    $content = str_replace($match,$this->CreateSchedTagContent($new_tag_params), $content);
                                }else{
                                    $content = str_replace($match,"", $content);
                                }
                            }
                        }
                   return $content;
                }
        // Create tag content
                function CreateSchedTagContent($tag_params){
                    global $WishListMemberInstance;
                    $wpm_current_user=wp_get_current_user();
                    $custom_types = get_post_types(array('public'=> true,"_builtin"=>false));
                    $post_type = array_merge(array("post","page"),$custom_types);
                    $ptype = in_array($tag_params['ptype'],$post_type)?$tag_params['ptype']:'';
                    $sched_posts = $this->GetSchedContent($ptype);
                    $ret = '';
                    $sortable = array("ID","date","days","title","new_date","menu_order");
                    //sort and filter(for protected posts) the post to show
                    foreach($sched_posts as $key=>$value){
                        if ( $value['date'] != "" && $value['hide_diff'] >= 0 ) {
                            $x[$key] = array('ID'=>$key,'date'=>$value['date'],'days'=>$value['days'],'new_date'=>$value['new_date']);
                        }
                    }
                    if($wpm_current_user->ID AND count($x)>0){
                        $title = isset( $tag_params['title'] ) ? $tag_params['title'] : '';
                        $sort = isset( $tag_params['sort'] ) ? $tag_params['sort'] : 'new_date';
                        $sort = in_array($sort, $sortable) ? $sort : 'new_date';
                        $px = $tag_params['px']==''?4:$tag_params['px'];
                        $date_today = date('Y-m-d H:i:s'); // get date today
                        if($title != ''){
                            $ret = '<div class="wlcccs-tag-holder">';
                            $ret .= '<p>' .$title .'</p>';
                        }
                        $ctr=$tag_params['showposts'];
                        if(!is_numeric($ctr))$ctr=10;
                        if(!$ctr){
                        	$ctr=10000000000;
                        }

                        foreach($x as $key=>$value) {
                            if(!$ctr){
                                break;
                            }
                            $post_details = get_post($value["ID"]);
                            if( isset($post_details->post_title) && trim($post_details->post_title) != ""){ //dont include posts with no title
                                $value['title'] = $post_details->post_title;
                                $value['menu_order'] = $post_details->menu_order;
                            }
                            $x[$key] = (object) $value;
                            $ctr--;
                        }

                        if(count($x)< 1){
                            $ret .= 'None';
                            $sched_posts = $this->subval_sort($x,$sort,false,false);
                        }else{
                            $sched_posts = $this->subval_sort($x,$sort,false,false);
                            $ret .= '<ul class="wlcccs-tag-ul">';
                        }

                        //end sorting
                        $hide_post_date = $tag_params['showdate']==''?'yes':$tag_params['showdate'];
                        $hide_post_time = $tag_params['showtime']==''?'no':$tag_params['showtime'];
                        $date_time_separator = $tag_params['separator']==''?' @ ':(' ' .$tag_params['separator'] .' ');
                        foreach($sched_posts as $key=>$value){
                            if(count($value) > 0){
                                $ret .= '<li class="wlm-sched-widget-post-title" style="margin-bottom:' .$px .'px;"><span class="wlm-sched-widget-post-title">' .$value->title .'</span>';
                                if($hide_post_date=="yes"){
                                    $ret .= ' on <span class="wlm-sched-widget-post-date"><script type="text/javascript">tag_showdateGMT(' .$this->get_sched_date($value->date, $value->days) .'000);</script></span>';
                                    if($hide_post_time=="yes"){
                                        $ret .= $date_time_separator .'<span class="wlm-sched-widget-post-time"><script type="text/javascript">tag_showtimeGMT(' .$this->get_sched_date($value->date, $value->days) .'000);</script></span>';
                                    }
                                }
                                $ret .= '</li>';
                            }
                        }
                        if(count($x)> 0){
                            $ret .= '</ul>';
                        }
                        if($title != '')$ret .= '</div>';
                    }
                    return $ret;
                }

                function process_shortcode( $atts ) {
                    $params = shortcode_atts( array(
                        'ptype' => '',
                        'title' => '',
                        'showpost' => '10',
                        'px' => '4',
                        'separator' => '@',
                        'sort' => 'title',
                        'showdate' => 'no',
                        'showtime' => 'no',
                    ), $atts );

                    global $WishListMemberInstance;
                    $wpm_current_user=wp_get_current_user();
                    $custom_types = get_post_types( array("_builtin"=>false) );
                    $post_type = array_merge( array("post","page"), $custom_types );
                    $ptype = in_array($params['ptype'],$post_type) ? $params['ptype'] : '';
                    $sched_posts = $this->GetSchedContent( $ptype );
                    $ret = '';
                    $sortable = array("ID","date","days","title","new_date","menu_order");
                    $x = [];
                    //sort and filter(for protected posts) the post to show
                    foreach ( $sched_posts as $key => $value ){
                        if ( $value['date'] != "" && $value['hide_diff'] >= 0 ) {
                            $x[$key] = array('ID'=>$key,'date'=>$value['date'],'days'=>$value['days'],'new_date'=>$value['new_date']);
                        }
                    }
                    if($wpm_current_user->ID AND count($x)>0){
                        $title = isset( $params['title'] ) ? $params['title'] : '';
                        $sort = isset( $params['sort'] ) ? $params['sort'] : 'new_date';
                        $sort = in_array($sort, $sortable) ? $sort : 'new_date';
                        $px = $params['px']==''?4:$params['px'];
                        $date_today = date('Y-m-d H:i:s'); // get date today
                        if($title != ''){
                            $ret = '<div class="wlcccs-tag-holder">';
                            $ret .= '<p>' .$title .'</p>';
                        }
                        $ctr=$params['showposts'];
                        if(!is_numeric($ctr))$ctr=10;
                        if(!$ctr){
                            $ctr=10000000000;
                        }

                        foreach($x as $key=>$value) {
                            if(!$ctr){
                                break;
                            }
                            $post_details = get_post($value["ID"]);
                            if( isset($post_details->post_title) && trim($post_details->post_title) != ""){ //dont include posts with no title
                                $value['title'] = $post_details->post_title;
                                $value['menu_order'] = $post_details->menu_order;
                            }
                            $x[$key] = (object) $value;
                            $ctr--;
                        }

                        if(count($x)< 1){
                            $ret .= 'None';
                            $sched_posts = $this->subval_sort($x,$sort,false,false);
                        }else{
                            $sched_posts = $this->subval_sort($x,$sort,false,false);
                            $ret .= '<ul class="wlcccs-tag-ul">';
                        }

                        //end sorting
                        $hide_post_date = $params['showdate']==''?'yes':$params['showdate'];
                        $hide_post_time = $params['showtime']==''?'no':$params['showtime'];
                        $date_time_separator = $params['separator']==''?' @ ':(' ' .$params['separator'] .' ');
                        foreach($sched_posts as $key=>$value){
                            if ( $value ) {
                                $ret .= '<li class="wlm-sched-widget-post-title" style="margin-bottom:' .$px .'px;"><span class="wlm-sched-widget-post-title">' .$value->title .'</span>';
                                if($hide_post_date=="yes"){
                                    $ret .= ' on <span class="wlm-sched-widget-post-date"><script type="text/javascript">tag_showdateGMT(' .$this->get_sched_date($value->date, $value->days) .'000);</script></span>';
                                    if($hide_post_time=="yes"){
                                        $ret .= $date_time_separator .'<span class="wlm-sched-widget-post-time"><script type="text/javascript">tag_showtimeGMT(' .$this->get_sched_date($value->date, $value->days) .'000);</script></span>';
                                    }
                                }
                                $ret .= '</li>';
                            }
                        }
                        if(count($x)> 0){
                            $ret .= '</ul>';
                        }
                        if($title != '')$ret .= '</div>';
                    }

                    $js_showGMT = '<script type="text/javascript">
                            function tag_showdateGMT(unixtime){
                                var currentDate=new Date(unixtime);
                                var day = currentDate.getDate();
                                var months = currentDate.getMonth()+1;
                                var year = currentDate.getFullYear();
                                if (day < 10){ day = "0" +day;}
                                if (months < 10){ months = "0" +months;}
                                var new_date = months +"/" +day +"/" +year;
                                document.write(new_date);
                            }
                            function tag_showtimeGMT(unixtime){
                                var currentDate=new Date(unixtime);
                                var hours = currentDate.getHours();
                                var minutes = currentDate.getMinutes();
                                if(hours > 12){
                                  hours = hours - 12;
                                  add = " p.m.";
                                }else{
                                  hours = hours;
                                  add = " a.m.";
                                }
                                if(hours == 12){ add = " p.m.";}
                                if(hours == 00) {hours = "12";}
                                if (minutes < 10){ minutes = "0" +minutes;}
                                var new_time = hours +":" +minutes +" " +add;
                                document.write(new_time);
                            }
                        </script>';
                    $ret = $ret ? $js_showGMT .$ret : "";
                    return $ret;
                }

                function my_posts_clause_filter($input){
                    global $wpdb,$WishListMemberInstance;
                    $table1 = $wpdb->prefix."posts";
                    $table2 = $wpdb->prefix."wlcc_contentsched";
                    $wpm_current_user=wp_get_current_user();

                    if ( ! $wpm_current_user->caps['administrator'] ) {  // disregard content schedule for admin
                        //get user level timestamp
                        $levels=$WishListMemberInstance->GetMemberActiveLevels($wpm_current_user->ID); // get users membership levels
                        //remove payper post membership level
                        foreach((array)$levels as $id=>$level){
                            if(strpos($level, "U") !== false){
                                unset($levels[$id]);
                            }
                        }
                        //get user level registration dates
                        $userlvltimestamps = $WishListMemberInstance->UserLevelTimestamps($wpm_current_user->ID);
                        //inject our field query
                        //generate fields with case statement for the post date
                        $case_lvl_date[] = "{$table2}.mlevel IS NULL then '" .date('Y-m-d H:i:s') ."'";
                        foreach( $levels as $ind => $lvl ) {
                            $userlvltimestamp = $userlvltimestamps[$lvl];
                            if($userlvltimestamp != ""){
                                $case_lvl_date[] = "{$table2}.mlevel = '{$lvl}' then '" .date('Y-m-d H:i:s',$userlvltimestamp) ."'";
                            }
                        }
                        $case_lvl_date = implode(" WHEN ", $case_lvl_date );
                        $case_lvl_date = "CASE WHEN {$case_lvl_date} ELSE '" .date('Y-m-d H:i:s') ."' END";
                        $fields = "{$input['fields']},MIN(date_add(IF(IFNULL($table2.num_days,0) > 0,if($table1.post_date < {$case_lvl_date},{$case_lvl_date},$table1.post_date),$table1.post_date), INTERVAL IFNULL($table2.num_days,0) DAY)) as new_postdate";
                        //$fields = "$table1.ID,date_add(IF(MIN($table2.num_days)>0,if($table1.post_date<'$user_leveldate','$user_leveldate',$table1.post_date),$table1.post_date), INTERVAL IFNULL(MIN($table2.num_days),0) DAY) as post_date,$table1.post_author,$table1.post_date as post_date_old,$table1.post_date_gmt,$table1.post_content,$table1.post_title,$table1.post_excerpt,$table1.post_status,$table1.comment_status,$table1.ping_status,$table1.post_password,$table1.post_name,$table1.to_ping,$table1.pinged,$table1.post_modified,$table1.post_modified_gmt,$table1.post_content_filtered,$table1.post_parent,$table1.guid,$table1.menu_order,$table1.post_type,$table1.post_mime_type,$table1.comment_count";
                        //check if contentsched join exist
                        // $w = apply_filters('posts_join',array(&$this,'WLMSchedContentJoin'));
                        // var_dump($w);
                        if(strripos($input['join'],"wlcc_contentsched")){
                            $input['fields'] = $fields;
                            //order the post by our  new post date field
                            $input['orderby'] = str_replace("{$table1}.post_date", "new_postdate", $input['orderby']);
                            $input['orderby'] = str_replace("post_date", "new_postdate", $input['orderby']);
                        }
                    }
                    return $input;
                }

                //debug query function
                function debug_query( $query ) {
                    echo "<!-- "; print_r( $query ); echo "-->";
                    return $query; //if not debugging,lets just return the query
                }

                //term (category and tags) filters
                function SchedTermFilter($terms, $taxonomies, $args) {
                    global $wpdb;
                    if (is_admin()) return $terms;
                    $p = $this->GetSchedContent();
                    if(!$p) return $terms;
                    $p = implode(",",array_keys($p));
                    //lets get the terms with posts
                    $q = "SELECT term_taxonomy_id,COUNT(object_id) as obj FROM {$wpdb->prefix}term_relationships WHERE object_id NOT IN ({$p}) GROUP BY term_taxonomy_id";
                    $res = $wpdb->get_results($q);
                    $not_empty_terms = array();
                    foreach($res as $t){
                        $not_empty_terms[$t->term_taxonomy_id] = $t->obj;
                    }
                    foreach($terms as $key=>$term){
                        if(array_key_exists($term->term_id,$not_empty_terms)){
                            $terms[$key]->count = $not_empty_terms[$term->term_id];
                        }else{
                            if($args['hide_empty']){
                                unset($terms[$key]);
                            }else{
                                if( is_object($terms[$key]) ) {
                                    $terms[$key]->count = 0;
                                }
                            }
                        }
                    }
                    return $terms;
                }

                //redirect user to error page if it is scheduled
                function PreGetPost($query){
						  global $wpdb, $WishListMemberInstance;
						  require_once ABSPATH . WPINC . '/pluggable.php';
                    $is_single = is_single() || is_page() ? true:false;
                    $pid = false;
                    $name = false;
                    $user_is_admin =  current_user_can( 'manage_options' );
                    if($is_single && !$user_is_admin){
                        if(is_page()){
                            $pid = isset($query->query['page_id']) ? $query->query['page_id']:false;
                            $name = !$pid && isset($query->query['pagename']) ? $query->query['pagename']:"";
                        }elseif(is_single()){
                            $pid = isset($query->query['p']) ? $query->query['p']:false;
                            $name = isset($query->query['name']) ? $query->query['name']:"";
                        }
                        $name_array = explode("/", $name);
                        $name = array_slice($name_array, -1, 1); //get the last element
                        $name = $name[0];
                        if($name && !$pid){
                            if ( $query->queried_object && $query->queried_object->post_parent ) {
                                $pid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name='{$name}' AND post_parent={$query->queried_object->post_parent} AND post_status = 'publish'");
                            } else {
                                $pid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name='{$name}' AND post_status = 'publish'");
                            }
                        }else{
                            return $query;
                        }

                        if($pid){
                            $sched_content = $this->GetSchedContent();
                            if(isset($sched_content[$pid])){

                                $wlcc_sched_error_page = $WishListMemberInstance->GetOption( 'scheduler_error_page_type' );
                                $wlcc_sched_error_page = $wlcc_sched_error_page ? $wlcc_sched_error_page : get_option("wlcc_sched_error_page");
                                $wlcc_sched_error_page = $wlcc_sched_error_page ? $wlcc_sched_error_page: "text";

                                if ( $wlcc_sched_error_page == "url" ) {

                                    $wlcc_sched_error_page_url =  $WishListMemberInstance->GetOption( 'scheduler_error_page_url' );
                                    $wlcc_sched_error_page_url = $wlcc_sched_error_page_url ? $wlcc_sched_error_page_url : get_option("wlcc_sched_error_page_url");

                                    if ( $wlcc_sched_error_page_url != "" ){
                                        $url = trim($wlcc_sched_error_page_url);
                                        $p_url = parse_url($url);
                                        if(!isset($p_url['scheme'])) $url = "http://" .$url;
                                    }
                                }elseif ( $wlcc_sched_error_page == "internal" ) {

                                    $wlcc_sched_error_page = $WishListMemberInstance->GetOption( 'scheduler_error_page_internal' );
                                    if ( !$wlcc_sched_error_page) $wlcc_sched_error_page = $wlcc_sched_error_page &&  $wlcc_sched_error_page != "url" && $wlcc_sched_error_page != "internal" && $wlcc_sched_error_page != "text" ? $wlcc_sched_error_page  : false;
                                    $r_pid = (int) $wlcc_sched_error_page;
                                    if(is_int($r_pid) && $r_pid > 0 && !isset($sched_content[$r_pid])){
                                        $url = get_permalink($r_pid);
                                    }
                                } else {
                                    $url = $WishListMemberInstance->MagicPage() ."?sp=" ."scheduler_error_page";
                                }
                                if ( !$url ) $url = $this->MagicPage() ."?sp=" ."scheduler_error_page";
                                wp_redirect($url); exit(0);
                            }
                        }
                    }
                    return $query;
                }

                function GetPages( $pages, $args ) {
                    global $wpdb, $WishListMemberInstance;
                    if ( count( (array) $pages ) <= 0 ) return $pages;
                    $wpm_current_user = wp_get_current_user();
                    if ( ! $wpm_current_user->caps['administrator'] ) { // disregard content schedule for admin and if there is pages
                        $sched_posts = $this->GetSchedContent( $args['post_type'] );
                        if ( count( $sched_posts ) > 0 ) {
                            $sched_post_ids = array_keys( $sched_posts );
                            foreach ( $pages as $pid=>$page ) {
                                if ( in_array( $page->ID, $sched_post_ids ) ) {
                                    unset( $pages[$pid] );
                                }
                            }
                        }
                    }
                    return $pages;
                }

                function WpGetNavMenuItems( $items ) {
                    global $wpdb, $WishListMemberInstance;
                    if ( count( (array) $items ) <= 0 ) return $items;
                    $wpm_current_user = wp_get_current_user();
                 	if ( !wlm_arrval( $wpm_current_user->caps, 'administrator' ) ) { // disregard content schedule for admin
                        $sched_posts = $this->GetSchedContent();
                        if ( count( $sched_posts ) > 0 ) {
                            $sched_post_ids = array_keys( $sched_posts );
                            foreach ( $items as $pid=>$item ) {
                                //only filter out post types
                                if ( $item->type == "post_type" && in_array( $item->object_id, $sched_post_ids ) ) {
                                    unset( $items[$pid] );
                                }
                            }
                        }
                    }
                    return $items;
                }
        /*
         * WIDGET FUNCTIONS
        */
        // Widget on the Front End
                function SchedWidget($args,$return=false){
                    global $WishListMemberInstance;
                    extract($args);
                    $wpm_current_user=wp_get_current_user();
                    $ptype = $WishListMemberInstance->GetOption('wlm_sched_widget_ptype')==''?'':$WishListMemberInstance->GetOption('wlm_sched_widget_ptype');
                    $ptype = $ptype == 'all' ? '':$ptype;
                    $sched_posts = $this->GetSchedContent($ptype);
                    $x = [];
                    //sort and filter(for protected posts) the post to show
                    foreach($sched_posts as $key=>$value){
                        if ( $value['date'] != "" && $value['hide_diff'] >= 0 ) {
                            $sched_posts[$key] = array('ID'=>$key,'date'=>$value['date'],'days'=>$value['days'],'new_date'=>$value['new_date']);
                            $x[$key] = (object) $sched_posts[$key];
                        }
                    }

                    if($WishListMemberInstance->GetOption('widget_nologinbox')!=1 AND $wpm_current_user->ID AND count($x)>0 ) {
                        $title = $WishListMemberInstance->GetOption('wlm_sched_widget_title')==''?'Upcoming Posts':$WishListMemberInstance->GetOption('wlm_sched_widget_title');
                        $px = $WishListMemberInstance->GetOption('wlm_sched_widget_px')==''?4:$WishListMemberInstance->GetOption('wlm_sched_widget_px');
                        $date_today = date('Y-m-d H:i:s'); // get date today
    					echo $before_widget . $before_title;
                        echo $title;
                        echo $after_title;
                        //js functions
                        echo '<script type="text/javascript">
                                function showGMT(unixtime){
                                    var currentDate=new Date(unixtime);
                                    var hours = currentDate.getHours();
                                    var minutes = currentDate.getMinutes();
                                    var day = currentDate.getDate();
                                    var months = currentDate.getMonth()+1;
                                    var year = currentDate.getFullYear();
                                    if(hours > 12){
                                      hours = hours - 12;
                                      add = " p.m.";
                                    }else{
                                      hours = hours;
                                      add = " a.m.";
                                    }
                                    if(hours == 12){ add = " p.m.";}
                                    if(hours == 00) {hours = "12";}
                                    if (minutes < 10){ minutes = "0" +minutes;}
                                    if (day < 10){ day = "0" +day;}
                                    if (months < 10){ months = "0" +months;}
                                    var new_date = months +"/" +day +"/" +year +" @ " +hours +":" +minutes +" " +add;
                                    document.write(new_date);
                                }
                            </script>';
                        $ctr=$WishListMemberInstance->GetOption('wlm_sched_widget_count');
                        if(!is_numeric($ctr))$ctr=10;
                        if(!$ctr){
                        	$ctr=10000000000;
                        }

                        if(count($x)< 1){
                            echo 'None';
                            $sched_posts = $this->subval_sort($x,'new_date',false,false);
                        }else{
                            $sched_posts = $this->subval_sort($x,'new_date',false,false);
                            echo '<ul class="wlm-sched-widget-content">';
                        }
                        //end sorting
                        $hide_post_time=$WishListMemberInstance->GetOption('wlm_sched_hide_post_time');
                        foreach($sched_posts as $key=>$value){
                        	if(!$ctr){
                        		break;
                        	}
                            if(count((array)$value) > 0){
                                $post_details =get_post($value->ID);
                                if(trim($post_details->post_title) != ""){ //dont include posts with no title
                                    echo '<li class="wlm-sched-widget-post-title" style="margin-bottom:' .$px .'px;"><span class="wlm-sched-widget-post-title">' .$post_details->post_title .'</span>';
                                    if(!$hide_post_time){
                                        echo ' on <br /><span class="wlm-sched-widget-post-date">'
                                             .'<script type="text/javascript">showGMT(' .$this->get_sched_date($value->date, $value->days) .'000);</script></span>';
                                    }
                                    echo '</li>';
                                }
                            }
                            $ctr--;
                        }
                        echo $after_widget;
                    }
                }
        // Widget Settings on the Admin
                function SchedWidgetAdmin(){
                    global $WishListMemberInstance;

                    $custom_types = get_post_types(array('public'=> true,"_builtin"=>false),"objects");
                    $post_types = array("page"=>"Pages","post"=>"Posts");
                    foreach($custom_types as $t=>$ctype){
                        $post_types[$t]= $ctype->labels->name;
                    }

                    $title=$WishListMemberInstance->GetOption('wlm_sched_widget_title');
                    $px=$WishListMemberInstance->GetOption('wlm_sched_widget_px');
                    $hide_post_time=$WishListMemberInstance->GetOption('wlm_sched_hide_post_time');
                    $sched_posts_count=$WishListMemberInstance->GetOption('wlm_sched_widget_count');
                    $sched_ptype=$WishListMemberInstance->GetOption('wlm_sched_widget_ptype');
                    $sched_ptype = !$sched_ptype ? "all" : $sched_ptype;
                    echo '<p><label for="wlm-sched-widget">'.__('Widget Title:','wishlist-member').' <input type="text" value="'.$title.'" name="wlm_sched_widget_title" id="wlm-sched-widget-title" class="widefat" /></label></p>';
                    echo '<p><label for="wlm-sched-widget">'.__('List Spacing in Pixels:','wishlist-member').' <input type="text" value="'.$px.'" name="wlm_sched_widget_px" id="wlm-sched-widget-px" class="widefat" /></label></p>';
                    $checked_yes = $hide_post_time?'':' checked="checked "';
                    $checked_no = $hide_post_time?' checked="checked "':'';
                    echo '<p><label for="wlm-sched-widget">'.__('Display Time of Post:','wishlist-member').'</label> &nbsp;
                    <label><input type="radio" value="0" name="wlm_sched_hide_post_time" id="wlm-display-time-post-yes" '.$checked_yes.'/> Yes</label>
                    <label><input type="radio" value="1" name="wlm_sched_hide_post_time" id="wlm-display-time-post-no" '.$checked_no.'/> No</label>
                    </p>';
                    echo '<p><label for="wlm-sched-widget">'.__('How Many Schedule Posts to Display:','wishlist-member').' <input type="text" value="'.$sched_posts_count.'" name="wlm_sched_widget_count" id="wlm-sched-widget-count" class="widefat" /></label></p>';

                    $ptype_all_selected = $sched_ptype == "all"? "selected='selected'":"";
                    echo '<p><label for="wlm-sched-ptype">'.__('Show Post/Page:','wishlist-member').'</label> &nbsp;
                        <select name="wlm_sched_widget_ptype" id="wlm-sched-ptype">
                            <option value="all" ' .$ptype_all_selected .'>Show All</option>';
                    foreach($post_types as $i=>$ptype){
                        $selected = $sched_ptype == $i ? "selected='selected'":"";
                        echo "<option value='{$i}' {$selected}>{$ptype} Only</option>\n";
                    }
                    echo '</select>
                    </p>';
                    if(isset($_POST['wlm_sched_widget_title'])){
                        if(!trim($_POST['wlm_sched_widget_title']))$_POST['wlm_sched_widget_title']=__('Upcoming Posts','wishlist-member');
                        $WishListMemberInstance->SaveOption('wlm_sched_widget_title',$_POST['wlm_sched_widget_title']);
                    }
                    if(isset($_POST['wlm_sched_widget_px'])){
                        if(!is_numeric($_POST['wlm_sched_widget_px']))$_POST['wlm_sched_widget_px']=__(10,'wishlist-member');
                        $WishListMemberInstance->SaveOption('wlm_sched_widget_px',$_POST['wlm_sched_widget_px']);
                    }
                    if(isset($_POST['wlm_sched_widget_px'])){
                        if(!is_numeric($_POST['wlm_sched_widget_px']))$_POST['wlm_sched_widget_px']=__(10,'wishlist-member');
                        $WishListMemberInstance->SaveOption('wlm_sched_widget_px',$_POST['wlm_sched_widget_px']);
                    }
                    if(isset($_POST['wlm_sched_hide_post_time'])){
                        $WishListMemberInstance->SaveOption('wlm_sched_hide_post_time',$_POST['wlm_sched_hide_post_time']);
                    }
                    if(isset($_POST['wlm_sched_widget_count'])){
                        if(!is_numeric($_POST['wlm_sched_widget_count']))$_POST['wlm_sched_widget_count']=__(10,'wishlist-member');
                        $WishListMemberInstance->SaveOption('wlm_sched_widget_count',$_POST['wlm_sched_widget_count']);
                    }
                    if(isset($_POST['wlm_sched_widget_ptype'])){
                        if(!trim($_POST['wlm_sched_widget_ptype']))$_POST['wlm_sched_widget_ptype']=__('all','wishlist-member');
                        $WishListMemberInstance->SaveOption('wlm_sched_widget_ptype',$_POST['wlm_sched_widget_ptype']);
                    }
                }
        /*
            OTHER FUNCTIONS NOT CORE OF CONTENT SCHEDULER GOES HERE
        */
        /*
         * FUNCTION to get NEXT and PREVIOUS Posts ID
        */
            function get_next_prev_id($id,$next=true){
                global $wpdb,$WishListMemberInstance;
                $wlcc_post_arr=$WishListMemberInstance->GetOption('wlcc_post_arr');
                $wlcc_post_arr = is_array( $wlcc_post_arr ) ? $wlcc_post_arr : array();
                $cnt = count($wlcc_post_arr)-1;
                $post_id = "-1";
                if($cnt >= 0){
                    $key = array_search($id, $wlcc_post_arr);
                    if($key >= 0){
                        if($next){
                            if($key > 0 && $key <= $cnt){
                                $post_id = $wlcc_post_arr[$key -1];
                            }
                        }else{
                            if($key >= 0 && $key < $cnt){
                                $post_id = $wlcc_post_arr[$key +1];
                            }
                        }
                    }
                }
                return $post_id;
            }
        /*
         * FUNCTION to Sort Multidimensional Arrays
        */
            function subval_sort($a,$subkey,$sort=true,$asc=true) { //sort the multidimensional array by key
                global $WishListMemberInstance;
                $c = array();
                    if(count((array)$a) > 0){
                        foreach($a as $k=>$v) {
                               $b[$k] = $v->$subkey;
                        }
                        if($asc)
                            arsort($b);
                        else
                            asort($b);
                        foreach($b as $key=>$val) {
                                $c[] = $a[$key];
                                //save the post arrangement
                                $d[] = $a[$key]->ID;
                        }
                        //save this if viewing post
                        if(!is_single() && $sort){
                            $WishListMemberInstance->SaveOption('wlcc_post_arr',$d);
                        }
                    }
                    return $c;
            }
        /*
         * FUNCTION to Save The current selection
         * on the filter at the WL Content Scheduler Dashboard
        */
            function SaveView(){
                $wpm_current_user=wp_get_current_user();
                if(!session_id()){
                    session_start();
                }
                if ( $wpm_current_user->caps['administrator'] ) {
                    if(isset($_POST['frmsubmit'])){
                        $show_level = isset($_POST['show_level']) ? $_POST['show_level']:$_GET['show_level'];
                        $show_post = isset($_POST['show_post']) ? $_POST['show_post']:$_GET['show_post'];
                        $_SESSION['wlccshowlevel'] = $show_level;
                        $_SESSION['wlccshowpost'] = $show_post;
                    }
                }
            }
        /*
         * FUNCTION to cut the String
        */
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
        /*
         * FUNCTION to get the Scheduled Date
        */
            function get_sched_date($post_date, $days,$format=''){
                if($format == ''){
                    $pdate = gmdate('Y-m-d H:i:s', strtotime($post_date));
                    $d1 = date_parse($pdate);
                    $pdate = gmmktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
                    $date = $pdate + ($days*86400);
                }else{
                    $d1 = date_parse($post_date);
                    $pdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
                    $date = date($format,$pdate + ($days*86400));
                }
                return $date;
            }
        /*
         * Function to get date difference needs php5.2
        */
            function date_diff($start, $end, $divisor=0){
                $d1 = date_parse($start);
                $sdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
                $d2 = date_parse($end);
                $edate = mktime($d2['hour'],$d2['minute'],$d2['second'],$d2['month'],$d2['day'],$d2['year']);
                $time_diff = $edate - $sdate;
                return $time_diff/$divisor;
            }

        //function to format the date
            function format_date($date,$format='M j, Y g:i a'){
                $d1 = date_parse($date);
                $pdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
                $date = date($format,$pdate);
                return $date;
            }
	}//End of WLM3_ContentScheduler Class
}
?>
