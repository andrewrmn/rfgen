<?php
/**
 * Template Name: Register Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>


<div class="section">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-10">
<div class="user-form">
<h3>Register</h3>
<div class="content">
<form action="#" method="post">
<div class="row">
<div class="col-sm-6 mb-4"><input type="text" class="form-control" placeholder="First Name"></div>										    
<div class="col-sm-6 mb-4"><input type="text" class="form-control" placeholder="Last Name"></div>
<div class="col-sm-6 mb-4"><input type="text" class="form-control" placeholder="Username"></div>										    
<div class="col-sm-6 mb-4"><input type="text" class="form-control" placeholder="Organization"></div>
<div class="col-sm-6 mb-4"><input type="email" class="form-control" placeholder="Email"></div>											    
<div class="col-sm-6 mb-4"><input type="email" class="form-control" placeholder="Confirm Email"></div>
<div class="col-sm-6 mb-4"><input type="password" class="form-control" placeholder="Password"></div>											    
<div class="col-sm-6 mb-4"><input type="password" class="form-control" placeholder="Confirm Password"></div>
<div class="col-sm-6 mb-4"><img src="images/recapcha.png" alt=""></div>
<div class="col-sm-6 mb-4 text-right"><input type="submit" class="btn btn-primary" value="Signup Now"></div>
</div>
</form>
</div>
<div class="user-form-option">
<div class="media">
<div class="media-body"><h4>Already Registered?</h4></div>
<a href="#" class="btn btn-primary">Login</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<?php get_footer(); ?>
