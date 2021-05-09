<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
?>
<br /><br /><br /><br />
<strong>Twitter connexion</strong>

  <legend>Or use another service</legend>
  <?php echo HTML::link(CLICSHOPPING::link(null, 'Account&SocialLogIn&action=social_login&provider=Google'), 'google'); ?><br />
  <a href="#" >Facebook</a><br />
  <a href="#">twiter</a>

<br /><br /><br /><br />


