<?php

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class ml_login_social_network_twitter {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_login_social_network_twitter');
      $this->description = CLICSHOPPING::getDef('module_login_social_network_twitter_description');


      if (defined('MODULE_LOGIN_SOCIAL_NETWORK_TWITTER_STATUS')) {
        $this->sort_order = MODULE_LOGIN_SOCIAL_NETWORK_TWITTER_SORT_ORDER;
        $this->enabled = (MODULE_LOGIN_SOCIAL_NETWORK_TWITTER_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');

      if (isset($_GET['Account']) && isset($_GET['LogIn'])) {
        $template = '<!-- login_social_network_ start -->' . "\n";

/*

      if ( isset($_GET['action'])) {
        var_dump($_GET['action']);
        exit;

        if ( $_GET['action'] == 'social_login' ) {
          $this->preLogin();
        } elseif ( $_GET['action'] == 'social_login_process' ) {
          var_dump( $this->postLogin());
          exit;
        }
      }
*/




/*
      if($user_profile && isset($user_profile->identifier)) {


                  echo '<b>firstName</b> :'.$user_profile->firstName.'<br>';
                  echo '<b>Date</b> :'. $user_profile->birthDay . '/' . $user_profile->birthMonth . '/' . $user_profile->birthYear.'<br>';
                  echo '<b>Email</b> :'.$user_profile->email.'<br>';
                  echo '<b>Email Verication</b> :'.$user_profile->email.'<br>';
                  echo '<b>Language</b> :'.$user_profile->language.'<br>';

      }
*/

/*
 * Config
 * https://console.developers.google.com
 * API &Authentification  / API / enable google + api
 * API &Authentification  / Identifiants
 *
 * retour : http://clicshopping.no-ip.biz/test/social/hybridauth/index.php?hauth.done=Google
 * http://clicshopping.no-ip.biz/test/social/hybridauth/?hauth.done=Google
 * http://www.yiiframework.com/wiki/459/integrating-hybridauth-directly-into-yii-without-an-extension/
 * https://myaccount.google.com/?hl=fr&pli=1
*/




          ob_start();
          require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/login_social_network_twitter'));

          $template .= ob_get_clean();
          $template .= '<!-- login_social_network end-->' . "\n";

          $CLICSHOPPING_Template->addBlock($template, $this->group);
      }
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_LOGIN_SOCIAL_NETWORK_TWITTER_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_LOGIN_SOCIAL_NETWORK_TWITTER_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_LOGIN_SOCIAL_NETWORK_TWITTER_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array(
        'MODULE_LOGIN_SOCIAL_NETWORK_TWITTER_STATUS',
        'MODULE_LOGIN_SOCIAL_NETWORK_TWITTER_SORT_ORDER'
      );
    }





/*
  public function preLogin() {

    if (!isset($_GET['provider']))  {
      CLICSHOPPING::redirect(null, null);
    }

    // HybridAuth
    Registry::set('HybridAuthIdentity', new HybridAuthIdentity());
    $CLICSHOPPING_HybridAuthIdentity = Registry::get('HybridAuthIdentity');
    $provider = $_GET['provider'];
    $provider = @trim(strip_tags( $provider ));

    if (!$CLICSHOPPING_HybridAuthIdentity->validateProviderName($provider)) {
      var_dump('500 Invalid Action. Please try again.');
      return CLICSHOPPING::redirect(null, null);
    }

    return CLICSHOPPING::redirect(null, 'Account&LogIn&action=social_login_process&provider=' .$provider);

  }


  public function postLogin() {

// HybridAuth
    Registry::set('HybridAuthIdentity', new HybridAuthIdentity());
    $CLICSHOPPING_HybridAuthIdentity = Registry::get('HybridAuthIdentity');
    $CLICSHOPPING_HybridAuthIdentity->adapter = $CLICSHOPPING_HybridAuthIdentity->hybridAuth->authenticate($_GET['provider'] );
    $user_profile = $CLICSHOPPING_HybridAuthIdentity->adapter->getUserProfile();

    return $user_profile;

  }
*/
}


