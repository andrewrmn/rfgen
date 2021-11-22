<?php

get_header();
// Set the Current Author Variable $curauth
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
?>
<div class="container">
<div class="author-profile-card"><br/>
    <h2>About: <?php echo $curauth->nickname; ?></h2>
    <div class="author-photo">
    <?php echo get_avatar( $curauth->user_email , '90 '); ?>
    </div>
    <p><strong>Website:</strong> <a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a><br />
    <strong>Bio:</strong> <?php echo $curauth->user_description; ?></p>
</div>
<br/><br/>     
<h2>Posts by <?php echo $curauth->nickname; ?>:</h2>
 
 
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<h3>
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>">
<?php the_title(); ?></a>
</h3>
<p class="posted-on">Posted on: <?php the_time('d M Y'); ?></p>
 
<?php the_excerpt(); ?>
 
<?php endwhile; 
 
// Previous/next page navigation.
the_posts_pagination();
 
 
else: ?>
<p><?php _e('No posts by this author.'); ?></p>
 
<?php endif; ?>
</div><br/>
<?php get_footer(); ?>