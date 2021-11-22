<?php if( 'login' == $action ) : ?>
<script type="text/javascript">
  var span = document.createElement('span');
  span.id = 'wishlist-member-otl';
  var a = document.createElement("a");
  a.href = "<?php echo add_query_arg( 'action', 'wishlistmember-otl', wp_login_url( wlm_arrval( $_REQUEST, 'redirect_to' ) ?: '' ) ); ?>";
  a.append(<?php echo json_encode( wishlistmember_instance()->GetOption( 'onetime_login_link_label' ) ) ?>);
  span.append(a);
  document.getElementsByClassName("submit")[0].append(span);
</script>
<?php endif; ?>

<style media="screen">
  #wishlist-member-otl::before {
    content: '';
    clear: both;
    display: table;
  }

  #wishlist-member-otl {
    clear: both;
    display: block;
    text-align: center;
  }

  #wishlist-member-otl a {
    display: block;
    margin: 1em 0;
  }
</style>