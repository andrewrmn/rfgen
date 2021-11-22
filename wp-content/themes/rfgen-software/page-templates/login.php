<?php
/**
 * Template Name: Login Page Template
 */

get_header(); ?>


<?php get_template_part( 'content', 'banner' ); ?>


<div class="section">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="user-form">
<h3>Please Log In</h3>
<div class="content">
<form action="#" method="post">
<div class="row">
<div class="col-sm-12 mb-4"><input type="text" class="form-control" placeholder="Username"></div>			
<div class="col-sm-12 mb-4"><input type="password" class="form-control" placeholder="Password"></div>		
<div class="col-sm-6 mb-4"><input type="checkbox" id="rem" name="rem"><label for="rem"><span></span>Remember Me </label></div>
<div class="col-sm-6 mb-4 text-right"><input type="submit" class="btn btn-primary" value="Login"></div>
</div>
</form>
<div class="forgot-pass">
<a data-toggle="collapse" href="#collapseForgot" role="button" aria-expanded="false" aria-controls="collapseForgot" class="link collapsed">Forget Username or Password?</a>
<div class="collapse" id="collapseForgot" style="">
<div class="card card-body">
<h5>Forget Username or Password?</h5>
<p>Enter your email address and we'll send it to you.</p>
<form action="#" method="post">
<div class="row">
<div class="col-sm-12 mb-4"><input type="text" class="form-control" placeholder="Email"></div>			
<div class="col-sm-12 mb-4 text-right"><input type="submit" class="btn btn-primary" value="Get Password"></div>
</div>
</form>
</div>
</div>
</div>
</div>
<div class="user-form-option">
<div class="media">
<div class="media-body"><h4>Not Registered?</h4></div>
<a href="#" class="btn btn-primary">Signup Now</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<?php get_footer(); ?>
