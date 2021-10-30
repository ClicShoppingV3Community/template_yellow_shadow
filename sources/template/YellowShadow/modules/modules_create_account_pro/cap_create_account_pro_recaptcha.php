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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Mail;

  class cap_create_account_pro_recaptcha {
    public string $code;
    public string $group;
    public $title;
    public $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('modules_create_account_pro_recaptcha_title');
      $this->description = CLICSHOPPING::getDef('modules_create_account_pro_recaptcha_description');

      if (\defined('MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_STATUS')) {
        $this->sort_order = (int)MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_SORT_ORDER;
        $this->enabled = (MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_STATUS == 'True');
      }

      if (!\defined('CLICSHOPPING_APP_ANTISPAM_RE_RECAPTCHA_STATUS') || CLICSHOPPING_APP_ANTISPAM_RE_RECAPTCHA_STATUS == 'False' || CLICSHOPPING_APP_ANTISPAM_CREATE_ACCOUNT_PRO == 'False') {
         $this->enabled = false;
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_GET['Account'], $_GET['CreatePro']) && !isset($_GET['Success'])) {
       $content_width = (int)MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_CONTENT_WIDTH;

        $create_account_pro_recaptcha = '<!--  create_account_pro_recaptcha start -->' . "\n";

        $recaptcha = $CLICSHOPPING_Hooks->output('AllShop', 'GoogleRecaptchaDisplay', null, 'display');

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/create_account_pro_recaptcha'));

        $create_account_pro_recaptcha .= ob_get_clean();

        $create_account_pro_recaptcha .= '<!-- create_account_pro_recaptcha end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($create_account_pro_recaptcha, $this->group);
      }

    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return \defined('MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_SORT_ORDER',
          'configuration_value' => '350',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_STATUS',
                 'MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_CONTENT_WIDTH',
                 'MODULES_CREATE_ACCOUNT_PRO_RECAPTCHA_SORT_ORDER'
                );
    }
  }
