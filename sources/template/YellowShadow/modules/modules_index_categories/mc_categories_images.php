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

  class mc_categories_images {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_index_categories_images_title');
      $this->description = CLICSHOPPING::getDef('module_index_categories_images_description');

      if (defined('MODULE_INDEX_CATEGORIES_IMAGES_STATUS')) {
        $this->sort_order = MODULE_INDEX_CATEGORIES_IMAGES_SORT_ORDER;
        $this->enabled = (MODULE_INDEX_CATEGORIES_IMAGES_STATUS == 'True');
      }

    }

    public function execute() {
      $CLICSHOPPING_Category = Registry::get('Category');
      $CLICSHOPPING_rewriteUrl = Registry::get('RewriteUrl');

      $cPath_array = $CLICSHOPPING_Category->getPathArray();

      if (CLICSHOPPING::getBaseNameIndex() && $CLICSHOPPING_Category->getPath()) {
        if ($CLICSHOPPING_Category->getDepth() == 'nested' || $CLICSHOPPING_Category->getDepth() == 'products') {

          $CLICSHOPPING_Db = Registry::get('Db');
          $CLICSHOPPING_Template = Registry::get('Template');
          $CLICSHOPPING_Language = Registry::get('Language');

          $bootstrap_column = (int)MODULE_INDEX_CATEGORIES_IMAGES_BOX_COLUMNS;


          if ($CLICSHOPPING_Category->getPath() && strpos('_', (string)$CLICSHOPPING_Category->getPath())) {

// check to see if there are deeper categories within the current category
            $category_links = array_reverse($cPath_array);

           // for($i=0, $n=count($category_links); $i<$n; $i++) {
             foreach($category_links as $value) {

              $Qcategories = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                        from :table_categories c,
                                                             :table_categories_description cd
                                                        where c.parent_id = :parent_id
                                                        and c.categories_id = cd.categories_id
                                                        and cd.language_id = :language_id
                                                        and c.virtual_categories = 0
                                                        and c.status = 1
                                                        ');

              $Qcategories->bindInt(':parent_id', (int)$value);
              $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

              $Qcategories->execute();

              if ($Qcategories['total'] > 0) {
                $Qcategories = $CLICSHOPPING_Db->prepare('select c.categories_id,
                                                                 c.categories_image,
                                                                 cd.categories_name,
                                                                 c.parent_id
                                                         from :table_categories c,
                                                              :table_categories_description cd
                                                         where c.parent_id = :parent_id
                                                         and c.categories_id = cd.categories_id
                                                         and cd.language_id = :language_id
                                                         and c.virtual_categories = 0
                                                         and c.status = 1
                                                         order by sort_order,
                                                                    cd.categories_name
                                                        ');

                $Qcategories->bindInt(':parent_id', (int)$value);
                $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

                $Qcategories->execute();

                break; // we've found the deepest category the customer is in
              }
            }
          } else {

            $Qcategories = $CLICSHOPPING_Db->prepare('select c.categories_id,
                                                             c.categories_image,
                                                             cd.categories_name,
                                                             c.parent_id
                                                       from :table_categories c,
                                                            :table_categories_description cd
                                                       where c.parent_id = :parent_id
                                                       and c.categories_id = cd.categories_id
                                                       and cd.language_id = :language_id
                                                       and c.virtual_categories = 0
                                                       and c.status = 1
                                                       order by sort_order,
                                                                  cd.categories_name
                                                      ');

            $Qcategories->bindInt(':parent_id', $CLICSHOPPING_Category->getID());
            $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());

            $Qcategories->execute();
          }

          if ($Qcategories->rowCount() > 0 ) {

            $categories_content = '<!-- Categories Images start -->' . "\n";
            $categories_content .= '<div class="separator"></div>';
            $categories_content .= '<div class="d-flex flex-wrap text-md-center">';

            $categories = $Qcategories->fetchAll();

            foreach ($categories as $c) {
              $cPath_new = $CLICSHOPPING_Category->getPathCategories($c['categories_id']);

              $CLICSHOPPING_rewriteUrl->getCategoryTreeTitle($c['categories_name']);
              $categories_url = $CLICSHOPPING_rewriteUrl->getCategoryImageUrl($cPath_new);

              $link_categories_image = HTML::link($categories_url, HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . $c['categories_image'], HTML::outputProtected($c['categories_name']), (int)SUBCATEGORY_IMAGE_WIDTH, (int)SUBCATEGORY_IMAGE_HEIGHT, null, true));
              $link_categories = HTML::link($categories_url, $c['categories_name']);

              ob_start();
              require$CLICSHOPPING_Template->getTemplateModules($this->group . '/content/categories_images');
              $categories_content .= ob_get_clean();
            }

            $categories_content .= '</div>' . "\n";
            $categories_content .= '<!-- Categories Images end -->' . "\n";


            $CLICSHOPPING_Template->addBlock($categories_content, $this->group);
          }
        } // end count
      } // php_self
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_INDEX_CATEGORIES_IMAGES_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_INDEX_CATEGORIES_IMAGES_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'How many columns do you want to display ?',
          'configuration_key' => 'MODULE_INDEX_CATEGORIES_IMAGES_BOX_COLUMNS',
          'configuration_value' => '6',
          'configuration_description' => 'Veuillez selectionner le nombre de colonnes de produits',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous afficher les noms des catégories ?',
          'configuration_key' => 'MODULE_INDEX_CATEGORIES_IMAGES_SHOW_NAME',
          'configuration_value' => 'True',
          'configuration_description' => 'Affiche le nom de la catégorie du blog<br /><br /><i>(Valeur True = Oui - Valeur False = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous afficher les images des catégories ?',
          'configuration_key' => 'MODULE_INDEX_CATEGORIES_IMAGES_SHOW_IMAGES',
          'configuration_value' => 'True',
          'configuration_description' => 'Affiche les images de la catégorie du blog<br /><br /><i>(Valeur True = Oui - Valeur False = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );



      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_INDEX_CATEGORIES_IMAGES_SORT_ORDER',
          'configuration_value' => '30',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULE_INDEX_CATEGORIES_IMAGES_STATUS',
                    'MODULE_INDEX_CATEGORIES_IMAGES_BOX_COLUMNS',
                    'MODULE_INDEX_CATEGORIES_IMAGES_SHOW_NAME',
                    'MODULE_INDEX_CATEGORIES_IMAGES_SHOW_IMAGES',
                    'MODULE_INDEX_CATEGORIES_IMAGES_SORT_ORDER'
                  );
    }
  }
