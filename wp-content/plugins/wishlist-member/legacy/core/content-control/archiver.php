<?php
/*
 * Content Archiver Module
 * Version: 1.1.34
 * SVN: 34
 * @version $Rev: 30 $
 * $LastChangedBy: feljun $
 * $LastChangedDate: 2016-01-21 05:41:36 -0500 (Thu, 21 Jan 2016) $
 *
 */
if(!class_exists('WLM3_ContentArchiver')){
	/**
	 * Content Archiver Core Class
	 */
	class WLM3_ContentArchiver{
    //activate module
            function load_hooks(){
                //save Content Archiver Options when savign the post
                add_action('wp_insert_post',array(&$this,'SaveContentArchOptions'));
                //post filters
                add_filter('posts_where',array(&$this,'PostExpirationWhere'));
                add_filter('get_next_post_where',array(&$this,'PostExpirationAdjacentWhere'));
                add_filter('get_previous_post_where',array(&$this,'PostExpirationAdjacentWhere'));

                //filter for get_pages function because it does not use WP_Query
                add_filter('get_pages', array(&$this, 'GetPages'),9999,2);
                add_filter('pre_get_posts', array(&$this, 'PreGetPost'));

                add_action('wishlistmember3_post_page_options_menu',array(&$this,'wlm3_post_options_menu'));
                add_action('wishlistmember3_post_page_options_content',array(&$this,'ContentArchOptions'));
            }
    //deactivate module
            function remove_hooks(){ //remove filters and actions
                //save Content Archiver Options when savign the post
                remove_action('wp_insert_post',array(&$this,'SaveContentArchOptions'));
                //post filters
                remove_filter('posts_where',array(&$this,'PostExpirationWhere'));
                remove_filter('get_next_post_where',array(&$this,'PostExpirationAdjacentWhere'));
                remove_filter('get_previous_post_where',array(&$this,'PostExpirationAdjacentWhere'));
                remove_filter('get_pages', array(&$this, 'GetPages'));
                remove_filter('pre_get_posts', array(&$this, 'PreGetPost'));

                remove_action('wishlistmember3_post_page_options_menu',array(&$this,'wlm3_post_options_menu'));
                remove_action('wishlistmember3_post_page_options_content',array(&$this,'ContentArchOptions'));
            }

            function wlm3_post_options_menu() {
                echo '<li><a href="#" data-target=".wlm-inside-archiver" class="wlm-inside-toggle">Archiver</a></li>';
            }
     //page options
            function ContentArchOptions(){
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

                global $WishListMemberInstance,$WishListContentControl;
                $wpm_levels=$WishListMemberInstance->GetOption('wpm_levels');

                //default date
                $wlccexpdate = date_parse(date('Y-m-d H:i:s'));
                $wlccexpdate = date('Y-m-d H:i:s',mktime(0,0,0,(int)$wlccexpdate["month"],(int)$wlccexpdate["day"],(int)$wlccexpdate["year"]));
                $wlcc_expdate = $this->format_date($wlccexpdate,'m/d/Y h:i A');
                ?>
                <script type='text/javascript' src='<?php echo $WishListMemberInstance->legacy_wlm_url ?>/admin/post_page_options/content-control/js/archiver.js'></script>
                <div class="wlm-inside wlm-inside-archiver" style="display: none;">
                    <table class="widefat" id='wlcc_ca' style="text-align: left;" cellspacing="0">
                        <thead>
                        <tr style="width:100%;">
                            <th style="width: 60%;"> <?php _e('Membership Level/s'); ?></th>
                            <th style="width: 40%;"> <?php _e('Archive Date'); ?> </th>
                        </tr>
                        </thead>
                    </table>
                    <div id="wlcclevels_ca" style="text-align:left;overflow:auto;">
                        <table class="widefat" id="wlcc_ca" cellspacing="0" style="text-align:left;">
                            <tbody>
                            <?php foreach ( (array)$wpm_levels AS $id=>$level ) : ?>
                            <?php
                                $date = "";
                                $post_expiry = $this->GetPostExpiryDate( $post_id, $id );
                                $post_expiry =  is_array($post_expiry) ? $post_expiry : [];
                                if ( count($post_expiry) > 0 && $post_id ) {
                                    $date = $this->format_date($post_expiry[0]->exp_date,'m/d/Y h:i A');
                                }
                            ?>
                                <tr id="tr<?php echo $id;?>" style="width:100%;" class="<?php echo $alt++%2?'':'alternate'; ?>">
                                    <td style="width: 60%;border-bottom: 1px solid #eeeeee;"><strong><?php echo $level['name']; ?></strong></td>
                                     <td style="width: 40%;border-bottom: 1px solid #eeeeee;">
                                         <input style="width: 200px;" type="text" class="form-control wlm-datetimepicker" id="wlcc_expiry<?php echo $id;?>" name="wlcc_expiry[<?php echo $id;?>]" value="<?php echo $date; ?>" >
                                     </td>
                                </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                    </div>
                    <div style="text-align: right; padding-top: 4px; padding-bottom: 8px;">
                        <div class="wlm-message" style="display: none"><?php _e('Saved', 'wishlist-member'); ?></div>
                        <a href="#" class="wlm-btn -with-icons -success -centered-span wlm-archiver-save">
                            <i class="wlm-icons"><img src="<?php echo $WishListMemberInstance->pluginURL3; ?>/ui/images/baseline-save-24px.svg" alt=""></i>
                            <span><?php _e('Save Schedule', 'wishlist-member'); ?></span>
                        </a>
                    </div>
                </div>
                <input type='hidden' name='wlccca_save_marker' value='1'>
            <?php
            }
    //save content archiver options
            function SaveContentArchOptions(){
                global $WishListMemberInstance,$WishListContentControl;
                $post_ID = $_POST['post_ID'];

                $wlccca_save_marker = $_POST['wlccca_save_marker'];
                if ( $wlccca_save_marker != 1 ) return false;

                $wpm_levels = $WishListMemberInstance->GetOption('wpm_levels');
                $wlcc_expiry = $_POST['wlcc_expiry'];
                foreach ( (array)$wpm_levels AS $id => $level ) {
                    $wlccexpiry = $wlcc_expiry[$id] == "" || empty($wlcc_expiry[$id]) ? 0 : $wlcc_expiry[$id];
                    $wlccexpdate = date_parse($wlccexpiry);
                    if ( (isset($wlccexpdate['error_count']) && $wlccexpdate['error_count'] > 0) || !$wlccexpdate["year"] ) {
                       $this->DeletePostExpiryDate($post_ID,$id);
                    } else {
                        $date = date('Y-m-d H:i:s',mktime((int)$wlccexpdate["hour"],(int)$wlccexpdate["minute"],0,(int)$wlccexpdate["month"],(int)$wlccexpdate["day"],(int)$wlccexpdate["year"]));
                        $this->SavePostExpiryDate($post_ID,$id,$date);
                    }
                }
            }
    //save post expiry date
             function SavePostExpiryDate($post_id,$mlevel,$d){
                global $wpdb;
                $table = $wpdb->prefix ."wlcc_contentarchiver";
                $exp = $this->GetPostExpiryDate($post_id,$mlevel);
                $exp = is_array($exp) ? $exp : [];
                if ( count($exp) > 0 ) {
                        $q = "UPDATE $table SET exp_date = '" .$d ."' WHERE mlevel='" .$mlevel ."' AND post_id=" .$post_id;
                }else{
                        $q = "INSERT INTO $table(post_id,mlevel,exp_date) VALUES('" .$post_id ."','" .$mlevel."','".$d ."')";
                }
                $wpdb->query($q);
            }
    //get post expiry date
            function GetPostExpiryDate($post_id='',$mlevel='',$start=0,$limit=0){
                global $wpdb;
                $table = $wpdb->prefix ."wlcc_contentarchiver";
                if(is_array($mlevel)){
                        $q_mlevel = " mlevel IN ('" .implode('\',\'',$mlevel) ."') ";
                }else{
                        $q_mlevel = " mlevel='" .$mlevel ."' ";
                }

                if($post_id!='' && $mlevel!=''){
                        $q = "SELECT * FROM $table WHERE post_id=" .$post_id ." AND $q_mlevel";
                }else if($post_id!=''){
                        if($limit > 0){
                                $q = "SELECT * FROM $table WHERE post_id=" .$post_id ." ORDER BY date_added DESC LIMIT  " .$start ."," .$limit;
                        }else{
                                $q = "SELECT * FROM $table WHERE post_id=" .$post_id;
                        }
                }else if($mlevel!=''){
                        if($limit > 0){
                                $q = "SELECT * FROM $table WHERE $q_mlevel ORDER BY date_added DESC LIMIT  " .$start ."," .$limit;
                        }else{
                                $q = "SELECT * FROM $table WHERE $q_mlevel";
                        }
                }else if($limit > 0){
                        $q = "SELECT * FROM $table ORDER BY date_added DESC LIMIT  " .$start ."," .$limit;
                }else{
                        $q = "SELECT * FROM $table ORDER BY date_added DESC";
                }
                return $wpdb->get_results($q);
            }
    //delete post expiry date
            function DeletePostExpiryDate($post_id,$mlevel=''){
                global $wpdb;
                $table = $wpdb->prefix."wlcc_contentarchiver";
                if(is_array($post_id)){
                                $post_ids = implode(',',$post_id);
                                if($mlevel !=''){
                                        $q = "DELETE FROM $table WHERE mlevel='" .$mlevel ."' AND post_id IN (" .$post_ids .")";
                                }else{
                                        $q = "DELETE FROM $table WHERE post_id IN (" .$post_ids .")";
                                }
                }else{
                    if($post_id != ""){
                        if($mlevel !=''){
                                $q = "DELETE FROM $table WHERE  mlevel='" .$mlevel ."' AND post_id=" .$post_id;
                        }else{
                                $q = "DELETE FROM $table WHERE post_id=" .$post_id;
                        }
                    }
                }
                $wpdb->query($q);
            }
	/**
	 * Function to get Protected|Expired|ALL Posts
         * Return: Array()
	 */
            function GetPosts($show_post,$ptype,$show_level='',$start=0,$per_page=0,$sort="ID",$asc=1){
                global $wpdb,$WishListMemberInstance;
                $table1 = $wpdb->prefix."posts";
                $table2 = $wpdb->prefix."wlcc_contentarchiver";
                $limit = "";
                if($per_page >0) $limit =  " LIMIT " .$start ."," .$per_page;
                $order = " ORDER BY " .$sort .($asc == 1 ? " ASC":" DESC");
                if($show_post == 'all' || $show_post == ''){
                        $q = "SELECT ID,post_author,post_status,post_date,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish'" .$order .$limit;
                }else if($show_post == 'expiry'){
                   if($show_level == ''){
                        $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 INNER JOIN $table2 ON  $table1.ID=$table2.post_id AND $table1.post_type='{$ptype}' AND $table1.post_status='publish'" .$order .$limit;
                   }else{
                       $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 INNER JOIN $table2 ON  $table1.ID=$table2.post_id AND $table1.post_type='{$ptype}' AND $table1.post_status='publish' AND $table2.mlevel = '$show_level'" .$order .$limit;
                   }
                }else if($show_post == 'noexpiry'){
                   if($show_level == ''){
                       $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 WHERE $table1.ID NOT IN (SELECT post_id FROM $table2) AND $table1.post_type='{$ptype}' AND $table1.post_status='publish'" .$order .$limit;
                   }else{
                       $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 WHERE $table1.ID NOT IN (SELECT post_id FROM $table2 WHERE $table2.mlevel = '$show_level') AND $table1.post_type='{$ptype}' AND $table1.post_status='publish'" .$order .$limit;
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
                        $q = "SELECT ID,post_author,post_date,post_status,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish'" .$order .$limit;
                    }else{
                       $x=$WishListMemberInstance->GetMembershipContent($ptype,$show_level);
                       $q = "SELECT ID,post_author,post_date,post_status,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish' AND ID IN('" .implode(',',$x)."')" .$order .$limit;
                    }
                }
                return $wpdb->get_results($q);
            }

     //function to get the expired post for the member
            function GetExpiredPost(){
                global $WishListMemberInstance;
                $date_today       = date('Y-m-d H:i:s'); // get date today
                $wpm_current_user = wp_get_current_user();
                $levels           = array();
                $pplevel          = array();
                $user_pp_posts    = array();
                $expired_posts    = array();
                $unexpired_posts  = array();

                if ( $wpm_current_user->ID > 0 ) {
                  $levels = $this->get_users_level( $wpm_current_user->ID ); // get users membership levels
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
                }
                //get the post with expiration date
                if ( count( $levels ) > 0 ) {
                    $mlevel_post = $this->GetPostExpiryDate('',$levels); //get all the post with expiry date
                } else {
                    $mlevel_post = $this->GetPostExpiryDate(); //if not logged in or dont have membership level
                }

                // start checking the posts with expiration date if the user has access
                foreach((array)$mlevel_post as $lvl_post){

                  $postdate_diff = $this->date_diff( $lvl_post->exp_date , $date_today, 86400 ); //+ result means expired
						if ( count( $levels ) <= 0 ) { //non users, or non members
							if ( $postdate_diff > 0 ) { // check if the post itself is expired.
								$expired_posts[] = $lvl_post->post_id;
							}
                  } else {
                    //get level registration date of the user
                    //$user_leveldate = date('Y-m-d H:i:s',$WishListMemberInstance->UserLevelTimeStamp($wpm_current_user->ID,$lvl_post->mlevel));
                    $user_leveldate = gmdate('Y-m-d H:i:s', $WishListMemberInstance->UserLevelTimestamp($wpm_current_user->ID, $lvl_post->mlevel) + $WishListMemberInstance->GMT);
                    $leveldate_diff = $this->date_diff( $lvl_post->exp_date , $user_leveldate, 86400 ); //+ result means user cannot access this post

                    if ( $postdate_diff > 0 ) { // check if the post is expired and if the user has previous access to the post.
                      if ( $leveldate_diff > 0 ) {
                        $expired_posts[] = $lvl_post->post_id;
                      } else {
                        $unexpired_posts[] = $lvl_post->post_id;
                      }
                    } else {
                        $unexpired_posts[] = $lvl_post->post_id;
                    }
                  }

                }

                $unexpired_posts = array_unique( $unexpired_posts ); //remove duplicate post id from unexpired post
                $expired_posts = array_diff( $expired_posts, $unexpired_posts ); // take out post if the user still has access on it using different membership level
                $expired_posts = array_unique( $expired_posts ); //remove duplicate post id from expired post

                //remove users pp post from the list
                if ( count( $user_pp_posts ) > 0 ) {
                  $expired_posts = array_diff( $expired_posts, $user_pp_posts );
                }

                return $expired_posts;
            }

            //redirect user to error page if it is scheduled
            function PreGetPost($query){
                global $wpdb, $WishListMemberInstance;
                $is_single = is_single() || is_page() ? true:false;
                //if this is not a single post or page or its in the admin area, dont try redirect
                if ( ! $is_single || current_user_can( 'manage_options' ) ) return $query;

                //retrieve the post id and post name (if needed)
                $pid = false;
                $name = false;
                if ( is_page() ) {
                    $pid = isset($query->query['page_id']) ? $query->query['page_id']:false;
                    $name = !$pid && isset($query->query['pagename']) ? $query->query['pagename']:"";
                } elseif ( is_single() ) {
                    $pid = isset($query->query['p']) ? $query->query['p']:false;
                    $name = isset($query->query['name']) ? $query->query['name']:"";
                } else {
                  $pid = false;
                  $name = "";
                }

                //get the post id based from the post name we got
                $name_array = explode("/", $name);
                $name = array_slice($name_array, -1, 1); //get the last element
                $name = $name[0];
                if ( $name ) {
                    $pid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name='{$name}'");
                } else {
                    return $query;
                }

                //if theres a postid, lets redirect
                if ( $pid ) {
                    $archived_content = $this->GetExpiredPost();

                    if(in_array($pid,$archived_content)){
                        //get settings
                        $wlcc_archived_error_page = $WishListMemberInstance->GetOption( 'archiver_error_page_type' );
                        $wlcc_archived_error_page = $wlcc_archived_error_page ? $wlcc_archived_error_page : get_option("wlcc_archived_error_page");
                        $wlcc_archived_error_page = $wlcc_archived_error_page ? $wlcc_archived_error_page: "text";

                        if ( $wlcc_archived_error_page == "url" ) {

                            $wlcc_archived_error_page_url =  $WishListMemberInstance->GetOption( 'archiver_error_page_url' );
                            $wlcc_archived_error_page_url = $wlcc_archived_error_page_url ? $wlcc_archived_error_page_url : get_option("wlcc_archived_error_page_url");

                            if ( $wlcc_archived_error_page_url != "" ){
                                $url = trim($wlcc_archived_error_page_url);
                                $p_url = parse_url($url);
                                if(!isset($p_url['scheme'])) $url = "http://" .$url;
                            }
                        }elseif ( $wlcc_archived_error_page == "internal" ) {

                            $wlcc_archived_error_page = $WishListMemberInstance->GetOption( 'archiver_error_page_internal' );
                            if ( !$wlcc_archived_error_page) $wlcc_archived_error_page = $wlcc_archived_error_page &&  $wlcc_archived_error_page != "url" && $wlcc_archived_error_page != "internal" && $wlcc_archived_error_page != "text" ? $wlcc_archived_error_page  : false;
                            $r_pid = (int) $wlcc_archived_error_page;
                            if(is_int($r_pid) && $r_pid > 0 && !isset($archived_content[$r_pid])){
                                $url = get_permalink($r_pid);
                            }
                        } else {
                            $url = $WishListMemberInstance->MagicPage() ."?sp=" ."archiver_error_page";
                        }
                        if ( !$url ) $url = $this->MagicPage() ."?sp=" ."archiver_error_page";
                        wp_redirect($url); exit(0);
                    }
                }

                return $query;
            }
    /*
        FUNCTIONS FOR FILTERING POSTS
    */
    //functions used to filter the posts
            function PostExpirationWhere($where){
                global $wpdb,$WishListMemberInstance;
                $wpm_current_user=wp_get_current_user();
                $table = $wpdb->prefix."posts";
                $levels = array();
                $utype = "non_users";
                $w = $where;
                if ( $wpm_current_user->caps['administrator'] ) return $w;
                //determine the user type
                if ( $wpm_current_user->ID > 0 ) {
                  $levels = $this->get_users_level( $wpm_current_user->ID ); // get users membership levels
                  //remove payper post membership level
                  foreach((array)$levels as $id=>$level){
                      if(strpos($level, "U") !== false){
                          unset($levels[$id]);
                      }
                  }

                  if ( count( $levels ) > 0 ) {
                    $utype = "members";
                  } else {
                    $utype = "non_members";
                  }
                }

                $is_single = is_single() || is_page() ? true:false;
                if ( ! $is_single ) {
                    $archiver_hide_post_listing = $WishListMemberInstance->GetOption('archiver_hide_post_listing');
                    if ( $archiver_hide_post_listing ) {
                        $expired_posts = $this->GetExpiredPost();
                    } else {
                        $expired_posts = array();
                    }
                } else {
                    $expired_posts = $this->GetExpiredPost();
                }

                //filter the post thats not to be shown
                if ( count($expired_posts) > 0 ) {
                    $w .= " AND $table.ID NOT IN (" .implode(',',$expired_posts) .")";
                }

                return $w;
            }
    //functions used to filter the next and previous links
            function PostExpirationAdjacentWhere($where){
                global $wpdb,$WishListMemberInstance,$post;
                $wpm_current_user=wp_get_current_user();
                $current_post_date = $post->post_date;
                $w = $where;
                if ( ! $wpm_current_user->caps['administrator'] ) { // disregard content expiry for admin
                    $expired_posts = $this->GetExpiredPost();
                    //filter the post thats not to be shown
                    if ( count($expired_posts) > 0 ) {
                        $postids = implode(',',$expired_posts) .',' .$post->ID;
                        $w .= " AND p.ID NOT IN (" .$postids .") ";
                    }
                }
                return $w;
            }
    //functions used to filter the get_pages function
            function GetPages( $pages, $args ) {
                global $wpdb, $WishListMemberInstance;
                if ( count( (array) $pages ) <= 0 ) return $pages;
                $wpm_current_user = wp_get_current_user();
                $levels = array();
                $utype = "non_users";
                if ( ! $wpm_current_user->caps['administrator'] ) { // disregard archive content for admin

                    //determine the user type
                    if ( $wpm_current_user->ID > 0 ) {
                      $levels = $this->get_users_level( $wpm_current_user->ID ); // get users membership levels
                      //remove payper post membership level
                      foreach((array)$levels as $id=>$level){
                          if(strpos($level, "U") !== false){
                              unset($levels[$id]);
                          }
                      }

                      if( count( $levels ) > 0 ) {
                        $utype = "members";
                      } else {
                        $utype = "non_members";
                      }
                    }

                    $is_single = false; //post listing always
                    $expired_posts = array();
                    if ( ! $is_single ) {
                        $archiver_hide_post_listing = $WishListMemberInstance->GetOption('archiver_hide_post_listing');
                        if ( $archiver_hide_post_listing ) {
                            $expired_posts = $this->GetExpiredPost();
                        }
                    }

                    if ( count( $expired_posts ) > 0 ) {
                        foreach ( $pages as $pid=>$page ) {
                            if ( in_array( $page->ID, $expired_posts ) ) {
                                unset( $pages[$pid] );
                            }
                        }
                    }
                }
                return $pages;
            }
    /*
        OTHER FUNCTIONS NOT CORE OF CONTENT ARCHIVER GOES HERE
    */
        /*
         * FUNCTION to users membership levels
        */
        function get_users_level( $uid ) {
          global $WishListMemberInstance;
          static $levels = false;
          static $user_id = false;
          if ( $user_id && $user_id == $uid && is_array( $levels ) ) {
            return $levels;
          }

          $user_id = $uid;
          if ( $user_id > 0 ) {
            if ( method_exists( $WishListMemberInstance, 'GetMemberActiveLevels' ) ) {
              $levels = $WishListMemberInstance->GetMemberActiveLevels( $user_id ); // get users membership levels
            } else {
              $levels = $WishListMemberInstance->GetMembershipLevels( $user_id , false, true ); // get users membership levels
            }
          } else {
            $levels = array();
          }

          return $levels;
        }
        /*
         * FUNCTION to Save The current selection
         * on the filter at the WL Content Archiver Dashboard
        */
            function SaveView(){
                $wpm_current_user=wp_get_current_user();
                if(!session_id()){
                    session_start();
                }
                if($wpm_current_user->caps['administrator']){
                    if(isset($_POST['frmsubmit'])){
                        $show_level = isset($_POST['show_level']) ? $_POST['show_level']:$_GET['show_level'];
                        $show_post = isset($_POST['show_post']) ? $_POST['show_post']:$_GET['show_post'];
                        $_SESSION['wlcceshowlevel'] = $show_level;
                        $_SESSION['wlcceshowpost'] = $show_post;
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
    //function to format the date
        function format_date($date,$format='M j, Y g:i a'){
            $d1 = date_parse($date);
            $pdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
            $date = date($format,$pdate);
            return $date;
        }
//function to get date difference needs php5.2
        function isvalid_date($date,$pid=0){
            $ret = false;
            if($pid <= 0){
                if($date > date('Y-m-d H:i:s')){
                    $ret = true;
                }
            }else if($this->validateint($pid)){
               $post_details = get_post($pid);
               $post_date = $post_details->post_date;
               $post_date_arr = date_parse($post_date);
               $pdate = date('Y-m-d H:i:s',mktime((int)$post_date_arr["hour"],(int)$post_date_arr["minute"],0,(int)$post_date_arr["month"],(int)$post_date_arr["day"],(int)$post_date_arr["year"]));
                if($date > $pdate){
                    $ret = true;
                }
            }
            return $ret;
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
    //function to get date difference needs php5.2
            function date_diff($start, $end, $divisor=0){
                $d1 = date_parse($start);
                $sdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
                $d2 = date_parse($end);
                $edate = mktime($d2['hour'],$d2['minute'],$d2['second'],$d2['month'],$d2['day'],$d2['year']);
                $time_diff = $edate - $sdate;
                return $time_diff/$divisor;
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
