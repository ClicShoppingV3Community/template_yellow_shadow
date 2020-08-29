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

  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class fp_upcoming_products {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_front_page_upcoming_products_title');
      $this->description = CLICSHOPPING::getDef('module_front_page_upcoming_products_description');

      if (defined('MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_STATUS')) {
        $this->sort_order = MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_SORT_ORDER;
        $this->enabled = (MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Category = Registry::get('Category');
      $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');

      if (CLICSHOPPING::getBaseNameIndex() && !$CLICSHOPPING_Category->getPath()) {
// Get the module contents to display on the front page
          if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
            $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                           pd.products_name,
                                                           p.products_date_available as date_expected,
                                                           g.customers_group_price
                                                   from :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                                                        :table_products_description pd,
                                                        :table_products_to_categories p2c,
                                                        :table_categories c
                                                   where to_days(p.products_date_available) >= to_days(now())
                                                   and p.products_id = pd.products_id
                                                   and pd.language_id = :language_id
                                                   and g.customers_group_id = :customers_group_id
                                                   and g.products_group_view = 1
                                                   and p.products_archive = 0
                                                   and p.products_id = p2c.products_id
                                                   and p2c.categories_id = c.categories_id
                                                   and c.virtual_categories = 0
                                                   and c.status = 1
                                                   order by ' . MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_FIELD . '  ' . MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_SORT . '
                                                   limit :limit
                                                  ');

            $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $Qproducts->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
            $Qproducts->bindInt(':limit', MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_MAX_DISPLAY);

            $Qproducts->execute();

          } else {

            $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                           pd.products_name,
                                                           p.products_date_available as date_expected
                                                   from :table_products p,
                                                        :table_products_description pd,
                                                        :table_products_to_categories p2c,
                                                        :table_categories c
                                                   where to_days(p.products_date_available) >= to_days(now())
                                                   and p.products_id = pd.products_id
                                                   and pd.language_id = :language_id
                                                   and p.products_view = 1
                                                   and p.products_archive = 0
                                                   and p.products_id = p2c.products_id
                                                   and p2c.categories_id = c.categories_id
                                                   and c.virtual_categories = 0
                                                   and c.status = 1
                                                   order by ' . MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_FIELD . ' ' . MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_SORT . '
                                                   limit :limit
                                                 ');

            $Qproducts->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $Qproducts->bindInt(':limit', MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_MAX_DISPLAY);

            $Qproducts->execute();
          }

         if ($Qproducts->fetch() !== false) {
           $upcoming_prods_content = '<!-- Upcoming Products start -->' . "\n";
           $upcoming_prods_content .= '<div class="clearfix"></div>';
           $upcoming_prods_content .= '<div class="contentContainer">';
           $upcoming_prods_content .= '<div class="contentText">';
           $upcoming_prods_content .= '<div class="separator"></div>';

// Start the table to display the product data
           $upcoming_prods_content .= '<div class="contentText">';
           $upcoming_prods_content .= '<div class="float-md-left headingUpcomingProducts">&nbsp;' . CLICSHOPPING::getDef('table_heading_upcoming_products') . '&nbsp;</div>';
           $upcoming_prods_content .= '<div class="float-md-right headingDateExpected">&nbsp;' . CLICSHOPPING::getDef('table_heading_date_expected') . '&nbsp;</div>';
           $upcoming_prods_content .= '<div class="clearfix"></div>';
           $upcoming_prods_content .= '<div class="hr"></div>';
           $upcoming_prods_content .= '<div class="separator"></div>';
           $upcoming_prods_content .= '<div itemscope itemtype="https://schema.org/ItemList">';
           $upcoming_prods_content .= '<meta itemprop="itemListOrder" content="https://schema.org/ItemListUnordered" />';

           do {
             $products_name_url = $CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($Qproducts->valueInt('products_id'));

              $upcoming_prods_content .= '<div class="float-md-left">&nbsp;' . HTML::link($products_name_url, '<span itemprop="itemListElement"><strong>' . $Qproducts->value('products_name') . '</strong></span>') . '</div>';
              $upcoming_prods_content .= '<div class="float-md-right">' . DateTime::toShort($Qproducts->value('date_expected')) . '</div>';
              $upcoming_prods_content .= '<div class="clearfix"></div>' . "\n";
           } while ($Qproducts->fetch());

           $upcoming_prods_content .= '</div>';
           $upcoming_prods_content .= '</div>';
           $upcoming_prods_content .= '</div>';
           $upcoming_prods_content .= '</div>' . "\n";
           $upcoming_prods_content .= '<!-- Upcoming Products  -->' . "\n";

// Add the contents as a module
           $CLICSHOPPING_Template->addBlock($upcoming_prods_content, $this->group);
        } // end num row
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer un ordre de tri d\'affichage des champs',
          'configuration_key' => 'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_FIELD',
          'configuration_value' => 'date_expected',
          'configuration_description' => 'Veuillez indiquer un ordre de tri d\'affichage des champs.<br><br><i>(Valeur date_expected = par date - Valeur products_name = nom du produit)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'products_name\', \'date_expected\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer un ordre de tri d\'affichage par colonne',
          'configuration_key' => 'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_SORT',
          'configuration_value' => 'desc',
          'configuration_description' => 'Veuillez indiquer un ordre de tri d\'affichage par colonne<br><br><i>(Valeur desc = descendant - Valeur asc = ascendant)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'asc\', \'desc\') ',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez un indiquer un nom maximal de produits en arrivage à afficher',
          'configuration_key' => 'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_MAX_DISPLAY',
          'configuration_value' => '5',
          'configuration_description' => 'Veuillez indiquer le nombre maximal de produit à afficher',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_SORT_ORDER',
          'configuration_value' => '150',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array(
        'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_STATUS',
        'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_FIELD',
        'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_SORT',
        'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_MAX_DISPLAY',
        'MODULE_FRONT_PAGE_UPCOMING_PRODUCTS_SORT_ORDER'
      );
    }
  }

