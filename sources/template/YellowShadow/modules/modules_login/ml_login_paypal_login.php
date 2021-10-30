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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Payment\PayPal\PayPal as PayPalApp;
  use ClicShopping\Apps\Payment\PayPal\Classes\PaypalLogin;

  use ClicShopping\Apps\Payment\PayPal\Module\Payment\EC as PaymentModuleEC;


  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\Shop\TemplateEmail;

  class ml_login_paypal_login {
    public string $code;
    public string $group;
    public $title;
    public $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;
    public mixed $app;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_content_paypal_login_title');
      $this->description = CLICSHOPPING::getDef('module_content_paypal_login_description');
      $this->sort_order = \defined('MODULE_PAYPAL_LOGIN_SORT_ORDER') ? MODULE_PAYPAL_LOGIN_SORT_ORDER : 0;

      if (\defined('CLICSHOPPING_APP_PAYPAL_LOGIN_STATUS')) {
        if (\defined('MODULE_PAYPAL_LOGIN_STATUS')) {
          $this->enabled = (MODULE_PAYPAL_LOGIN_STATUS == 'True');
        }

        if (\defined('MODULE_PAYPAL_LOGIN_STATUS') && \defined('CLICSHOPPING_APP_PAYPAL_LOGIN_SANDBOX_CLIENT_ID') && CLICSHOPPING_APP_PAYPAL_LOGIN_SANDBOX_CLIENT_ID == 'Test' ) {
          $this->title .= ' [Sandbox]';
        }

        if ( !function_exists('curl_init')) {
          $this->description .= '<div class="alert alert-info" role="alert">' . $this->app->getDef('module_login_error_curl') . '</div>';

          $this->enabled = false;
        }
      } else {
        $this->enabled = false;
      }

      if (!Registry::exists('PayPal')) {
        Registry::set('PayPal', new PayPalApp());
      }

      $this->app = Registry::get('PayPal');


      If (!Registry::exists('PaypalLogin')) {
        Registry::set('PaypalLogin', new PaypalLogin());
      }

      $this->PaypalLogin = Registry::get('PaypalLogin');


      if ( $this->enabled === true ) {
        if ( ((MODULE_PAYPAL_LOGIN_STATUS == 'True') && (\defined('CLICSHOPPING_APP_PAYPAL_LOGIN_LIVE_CLIENT_ID') || \defined('CLICSHOPPING_APP_PAYPAL_LOGIN_LIVE_SECRET'))) ||
          ((MODULE_PAYPAL_LOGIN_STATUS == 'True') && (\defined('CLICSHOPPING_APP_PAYPAL_LOGIN_SANDBOX_CLIENT_ID') || \defined('CLICSHOPPING_APP_PAYPAL_LOGIN_SANDBOX_SECRET')))) {
          $this->description .= '<div class="alert alert-warning" role="alert">' . $this->app->getDef('module_login_error_credentials') . '</div>';

          $this->enabled = false;
        }
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $content_width = MODULE_PAYPAL_LOGIN_CONTENT_WIDTH;

      if ($CLICSHOPPING_Customer->getID()) {
        return false;
      }

      if (!\defined('CLICSHOPPING_APP_PAYPAL_LOGIN_ATTRIBUTES')) {
        return false;
      }


      if (isset($_GET['Account'], $_GET['LogIn'])) {
        if (isset($_GET['action'])) {
          if ($_GET['action'] == 'paypal_login') {
            $this->preLogin();
          } elseif ($_GET['action'] == 'paypal_login_process') {
            $this->postLogin();
          }
        }

        $scopes = $this->PaypalLogin->mlPaypalLoginGetAttributes();

        $use_scopes = ['openid'];

        foreach ( explode(';', CLICSHOPPING_APP_PAYPAL_LOGIN_ATTRIBUTES) as $a ) {
          foreach ( $scopes as $group => $attributes ) {
            foreach ( $attributes as $attribute => $scope ) {
              if ( $a == $attribute ) {
                if ( !\in_array($scope, $use_scopes)) {
                  $use_scopes[] = $scope;
                }
              }
            }
          }
        }

        $template_paypal = '<!-- Login paypal start -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/login_paypal_login'));
        $template_paypal .= ob_get_clean();

        $template_paypal .= '<!-- Login paypal end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($template_paypal, $this->group);
      }
    }

    public function preLogin() {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

      $return_url = CLICSHOPPING::link(null, 'Account&LogIn');

      if ( isset($_GET['code'])) {
        $_SESSION['paypal_login_customer_id'] = false;

        $params = ['code' => $_GET['code'],
                   'redirect_uri' => str_replace('&amp;', '&', CLICSHOPPING::link(null, 'Account&LogIn&action=paypal_login', false, false))
                  ];

        $response_token = $this->app->getApiResult($this->code, 'GrantToken', $params);

        if ( !isset($response_token['access_token']) && isset($response_token['refresh_token'])) {
          $params = array('refresh_token' => $response_token['refresh_token']);

          $response_token = $this->app->getApiResult($this->code, 'RefreshToken', $params);
        }

        if ( isset($response_token['access_token'])) {
          $params = ['access_token' => $response_token['access_token']];

          $response = $this->app->getApiResult($this->code, 'UserInfo', $params);

          if ( isset($response['email'])) {
            $_SESSION['paypal_login_access_token'] = $response_token['access_token'];

// check if e-mail address exists in database and login or create customer account
            $email_address = HTML::sanitize($response['email']);

// check if mail on newsletter_no_account
/*
              $QcheckEmailNoAccount = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                                 from :table_newsletters_no_account
                                                                 where customers_email_address = :customers_email_address
                                                                ');
              $QcheckEmailNoAccount->bindValue(':customers_email_address', $email_address);
              $QcheckEmailNoAccount->execute();

              if ($QcheckEmailNoAccount->valueInt('total') > 0) {
                $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                                      from :table_newsletters_no_account
                                                      where customers_email_address = :email_address
                                                    ');
                $Qdelete->bindValue(':email_address', $email_address);
                $Qdelete->execute();
              }
*/
// select email from customer table
              $Qcheck = $CLICSHOPPING_Db->prepare('select customers_firstname,
                                                          customers_default_address_id
                                                   from :table_customers
                                                   where customers_email_address = :customers_email_address
                                                   limit 1
                                                  ');
              $Qcheck->bindValue(':customers_email_address', $email_address);
              $Qcheck->execute();

              if ($Qcheck->fetch() !== false) {
                $_SESSION['paypal_login_customer_id'] = $Qcheck->valueInt('customers_id');
              } else {
                $customers_firstname = HTML::sanitize($response['given_name']);
                $customers_lastname = HTML::sanitize($response['family_name']);
//                $customer_password = Hash::getRandomString(max(ENTRY_PASSWORD_MIN_LENGTH, 8));

                $sql_data_array = ['customers_firstname' => $customers_firstname,
                                    'customers_lastname' => $customers_lastname,
                                    'customers_email_address' => $email_address,
                                    'customers_telephone' => null,
                                    'customers_newsletter' => '0',
                                    'languages_id' => (int)$CLICSHOPPING_Language->getId(),
//                                        'customers_password' => Hash::encrypt($customer_password),
  //                                        'customers_password' => 'none',
                                    'member_level' => 1
                                   ];


                if ($this->PaypalLogin->hasAttribute('phone') && isset($response['phone_number']) && !empty($response['phone_number'])) {
                  $customers_telephone = HTML::sanitize($response['phone_number']);

                  $sql_data_array['customers_telephone'] = $customers_telephone;
                }

                $CLICSHOPPING_Db->save('customers', $sql_data_array);

                $_SESSION['paypal_login_customer_id'] = $CLICSHOPPING_Db->lastInsertId();

                $CLICSHOPPING_Db->save('customers_info', ['customers_info_id' => $_SESSION['paypal_login_customer_id'],
                                                          'customers_info_number_of_logons' => '0',
                                                          'customers_info_date_account_created' => 'now()'
                                                         ]
                                      );

// email template

                $name = $customers_firstname . ' ' . $customers_lastname;

                $template_email_welcome_catalog = TemplateEmail::getTemplateEmailWelcomeCatalog();

                if (!empty(COUPON_CUSTOMER)) {
                  $email_coupon_catalog = TemplateEmail::getTemplateEmailCouponCatalog();
                  $email_coupon = $email_coupon_catalog . COUPON_CUSTOMER;
                }

                $template_email_signature = TemplateEmail::getTemplateEmailSignature();
                $template_email_footer = TemplateEmail::getTemplateEmailTextFooter();
                $email_subject = utf8_encode(html_entity_decode(CLICSHOPPING::getDef('email_subject')));
                $email_text = $template_email_welcome_catalog .'<br /><br />'. sprintf(CLICSHOPPING::getDef('module_content_login_email_password'), $email_address, $_SESSION['paypal_login_customer_id']) .'<br /><br />'. $email_coupon .'<br /><br />' .   $template_email_signature . '<br /><br />' . $template_email_footer;


// Envoi du mail avec gestion des images pour Fckeditor et Imanager.
                $message = html_entity_decode($email_text);
                $message = str_replace('src="/', 'src="' . HTTP::getShopUrlDomain(), $message);
                $CLICSHOPPING_Mail->addHtmlCkeditor($message);
                ;
                $from = STORE_OWNER_EMAIL_ADDRESS;

                $CLICSHOPPING_Mail->send($name, $email_address, null, $from, $email_subject);

// e-mail de notification a l'administrateur
                $admin_email_welcome = utf8_decode(html_entity_decode(ADMIN_EMAIL_WELCOME));
                $admin_email_text = utf8_decode(html_entity_decode(ADMIN_EMAIL_TEXT));

                if (EMAIL_INFORMA_ACCOUNT_ADMIN == 'True') {
                  $admin_email_text .= $admin_email_welcome . $admin_email_text;
                  $CLICSHOPPING_Mail->clicMail(STORE_NAME, STORE_OWNER_EMAIL_ADDRESS, $email_subject, $admin_email_text, $name, $email_address, '');
                }

                Registry::get('Session')->recreate();

//***************************************
// odoo web service
//***************************************
/*
                if (\defined('CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_WEB_SERVICE') && (CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_WEB_SERVICE == 'True' && CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_CUSTOMERS_CATALOG == 'True')) {
                  require_once(DIR_EXT .'odoo_xmlrpc/xml_rpc_catalog_create_account_paypal.php');
                }
*/
//***************************************
// End odoo web service
//***************************************
            }

// check if paypal shipping address exists in the address book
            $ship_firstname = HTML::sanitize($response['given_name']);
            $ship_lastname = HTML::sanitize($response['family_name']);
            $ship_address = HTML::sanitize($response['address']['street_address']);
            $ship_city = HTML::sanitize($response['address']['locality']);
            $ship_zone = HTML::sanitize($response['address']['region']);
            $ship_zone_id = 0;
            $ship_postcode = HTML::sanitize($response['address']['postal_code']);
            $ship_country = HTML::sanitize($response['address']['country']);
            $ship_country_id = 0;
            $ship_address_format_id = 1;

            $Qcountry = $CLICSHOPPING_Db->get('countries', ['countries_id', 'address_format_id'], ['countries_iso_code_2' => $ship_country], null, 1);

            if ($Qcountry->fetch() !== false) {
              $ship_country_id = $Qcountry->valueInt('countries_id');
              $ship_address_format_id = $Qcountry->valueInt('address_format_id');
            }

            if ($ship_country_id > 0) {

              $Qzone = $CLICSHOPPING_Db->prepare('select zone_id
                                                  from :table_zones
                                                  where zone_country_id = :zone_country_id
                                                  and (zone_name = :zone_name
                                                       or zone_code = :zone_code
                                                       )
                                                  limit 1
                                                  ');
              $Qzone->bindInt(':zone_country_id', $ship_country_id);
              $Qzone->bindValue(':zone_name', $ship_zone);
              $Qzone->bindValue(':zone_code', $ship_zone);
              $Qzone->execute();

              if ($Qzone->fetch() !== false) {
                $ship_zone_id = $Qzone->valueInt('zone_id');
              }
            }

            $Qcheck = $CLICSHOPPING_Db->prepare('select address_book_id
                                                  from :table_address_book
                                                  where customers_id = :customers_id
                                                  and entry_firstname = :entry_firstname
                                                  and entry_lastname = :entry_lastname
                                                  and entry_street_address = :entry_street_address
                                                  and entry_postcode = :entry_postcode
                                                  and entry_city = :entry_city
                                                  and (entry_state = :entry_state
                                                       or entry_zone_id = :entry_zone_id
                                                       )
                                                  and  entry_country_id = :entry_country_id
                                                  limit 1
                                                  ');

            $Qcheck->bindInt(':customers_id', $_SESSION['paypal_login_customer_id']);
            $Qcheck->bindValue(':entry_firstname', $ship_firstname);
            $Qcheck->bindValue(':entry_lastname', $ship_lastname);
            $Qcheck->bindValue(':entry_street_address', $ship_address);
            $Qcheck->bindValue(':entry_postcode', $ship_postcode);
            $Qcheck->bindValue(':entry_city', $ship_city);
            $Qcheck->bindValue(':entry_state', $ship_zone);
            $Qcheck->bindInt(':entry_zone_id', $ship_zone_id);
            $Qcheck->bindInt(':entry_country_id', $ship_country_id);
            $Qcheck->execute();

            if ($Qcheck->fetch() !== false) {
              $_SESSION['sendto'] = $Qcheck->valueInt('address_book_id');
            } else {

              $sql_data_array = ['customers_id' => (int)$_SESSION['paypal_login_customer_id'],
                                  'entry_firstname' => $ship_firstname,
                                  'entry_lastname' => $ship_lastname,
                                  'entry_street_address' => $ship_address,
                                  'entry_postcode' => $ship_postcode,
                                  'entry_city' => $ship_city,
                                  'entry_country_id' => $ship_country_id
                                  ];

              if (ACCOUNT_STATE == 'True') {
                if ($ship_zone_id > 0) {
                  $sql_data_array['entry_zone_id'] = (int)$ship_zone_id;
                  $sql_data_array['entry_state'] = '';
                } else {
                  $sql_data_array['entry_zone_id'] = '0';
                  $sql_data_array['entry_state'] = $ship_zone;
                }
              }

              $CLICSHOPPING_Db->save('address_book', $sql_data_array);

              $address_id = $CLICSHOPPING_Db->lastInsertId();

              $_SESSION['sendto'] = $address_id;

              if (!$CLICSHOPPING_Customer->getDefaultAddressID() || !isset($_SESSION['customer_default_address_id'])) {
                $CLICSHOPPING_Db->save('customers', ['customers_default_address_id' => $address_id], ['customers_id' => $_SESSION['paypal_login_customer_id']]);

                $_SESSION['customer_default_address_id'] = $address_id;
/*
              if ($customer_default_address_id < 1) {

                $Qupdate = $CLICSHOPPING_Db->prepare('update :table_customers
                                                     set customers_default_address_id = :address_id
                                                     where customers_id = :customers_id
                                                  ');
                $Qupdate->bindInt(':address_id', (int)$_SESSION['sendto']);
                $Qupdate->bindInt(':customers_id', $customers_id);
*/
              }
            }

            $_SESSION['billto'] = $_SESSION['sendto'];

            $return_url = CLICSHOPPING::link(null, 'Account&LogIn&action=paypal_login_process', false, false);
            }
          }
        }

      echo '<script>window.opener.location.href="' . str_replace('&amp;', '&', $return_url) . '";window.close();</script>';

      exit;
    }

    function postLogin() {

      if ( isset($_SESSION['paypal_login_customer_id'])) {
        if ( $_SESSION['paypal_login_customer_id'] !== false ) {
          $_SESSION['login_customer_id'] = $_SESSION['paypal_login_customer_id'];

// Register PayPal Express Checkout as the default payment method
          if ( !isset($_SESSION['payment']) || ($_SESSION['payment'] != 'Payment\PayPal\EC')) {
            if (\defined('MODULE_PAYMENT_INSTALLED') && !empty(MODULE_PAYMENT_INSTALLED)) {
              if ( \in_array('Payment\PayPal\EC', explode(';', MODULE_PAYMENT_INSTALLED))) {
                $ppe = new PaymentModuleEC();

                if ( $ppe->enabled ) {
                  $_SESSION['payment'] = 'Payment\PayPal\EC';
                }
              }
            }
          }
        }

        unset($_SESSION['paypal_login_customer_id']);
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return \defined('MODULE_PAYPAL_LOGIN_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_PAYPAL_LOGIN_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_PAYPAL_LOGIN_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_PAYPAL_LOGIN_SORT_ORDER',
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
      return ['MODULE_PAYPAL_LOGIN_STATUS',
              'MODULE_PAYPAL_LOGIN_CONTENT_WIDTH',
              'MODULE_PAYPAL_LOGIN_SORT_ORDER'
             ];
    }
  }
