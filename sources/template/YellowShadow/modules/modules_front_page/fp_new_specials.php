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

  class fp_new_specials {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_front_page_specials_products_title');
      $this->description = CLICSHOPPING::getDef('module_front_page_specials_products_description');

      if (defined('MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_STATUS')) {
        $this->sort_order = MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SORT_ORDER;
        $this->enabled = (MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Category = Registry::get('Category');
      $CLICSHOPPING_ProductsAttributes = Registry::get('ProductsAttributes');
      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      $new_products_category_id =  $CLICSHOPPING_Category->getID();

      if (CLICSHOPPING::getBaseNameIndex() && !$CLICSHOPPING_Category->getPath()) {
        if (MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_MAX_DISPLAY != 0) {

          if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
            if ( (!isset($new_products_category_id)) || ($new_products_category_id == 0)) {
// Requetes SQL pour afficher les nouveaux produits groupe B2B lors que l'on ne ce trouve pas dans une categorie
              $Qproduct = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                            p.products_quantity as in_stock
                                                      from :table_products p,
                                                          :table_products_description pd,
                                                          :table_specials s  left join :table_products_groups g on s.products_id = g.products_id,
                                                          :table_products_to_categories p2c,
                                                          :table_categories c
                                                      where (p.products_status = 1
                                                             and g.price_group_view = 1
                                                            )
                                                            or (p.products_status = 1
                                                               and g.price_group_view <> 1
                                                            )
                                                      and g.customers_group_id = :customers_group_id
                                                      and g.products_group_view = 1
                                                      and s.products_id = p.products_id
                                                      and p.products_id = pd.products_id
                                                      and pd.language_id = :language_id
                                                      and s.status = 1
                                                      and p.products_archive = 0
                                                      and (s.customers_group_id = :customers_group_id or s.customers_group_id = 99)
                                                      and p.products_id = p2c.products_id
                                                      and p2c.categories_id = c.categories_id
                                                      and c.virtual_categories = 0
                                                      and c.status = 1
                                                      group by p.products_id
                                                      order by rand(),
                                                             s.specials_date_added DESC
                                                       limit :products_limit
                                                     ');

              $Qproduct->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
              $Qproduct->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
              $Qproduct->bindint(':products_limit', (int)MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_MAX_DISPLAY);
              $Qproduct->execute();

            } else {
// Requetes SQL pour afficher les nouveaux produits groupe B2B lorsque l'on se trouve dans une categorie
              $Qproduct = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                             p.products_quantity as in_stock
                                                     from :table_products p,
                                                          :table_products_description pd,
                                                          :table_specials s  left join :table_products_groups g on s.products_id = g.products_id,
                                                          :table_products_to_categories p2c,
                                                          :table_categories c
                                                     where (p.products_status = 1
                                                            and g.price_group_view = 1
                                                          )
                                                         or (p.products_status = 1
                                                             and g.price_group_view <> 1
                                                            )
                                                     and g.customers_group_id = :customers_group_id
                                                     and g.products_group_view = 1
                                                     and s.products_id = p.products_id
                                                     and p.products_id = pd.products_id
                                                     and pd.language_id = :language_id
                                                     and s.status = 1
                                                     and p.products_archive = 0
                                                     and (s.customers_group_id = :customers_group_id or s.customers_group_id = 99
                                                     and p.products_id = p2c.products_id
                                                     and p2c.categories_id = c.categories_id
                                                     and c.virtual_categories = 0
                                                     and c.status = 1
                                                     and c.parent_id = :parent_id
                                                     group by p.products_id
                                                     order by rand(),
                                                             s.specials_date_added DESC
                                                      limit :products_limit
                                                   ');

              $Qproduct->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
              $Qproduct->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
              $Qproduct->bindInt(':parent_id', (int)$new_products_category_id);
              $Qproduct->bindInt(':products_limit', (int)MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_MAX_DISPLAY);
              $Qproduct->execute();
            }
          } else {
            if ( (!isset($new_products_category_id)) || ($new_products_category_id == 0)) {
// Requetes SQL pour afficher les nouveaux produits lors que l'on ne se trouve pas dans une categorie

              $Qproduct = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                           p.products_quantity as in_stock
                                                     from :table_products p,
                                                          :table_products_description pd,
                                                          :table_specials s,
                                                          :table_products_to_categories p2c,
                                                          :table_categories c
                                                     where p.products_status = 1
                                                     and s.products_id = p.products_id
                                                     and p.products_id = pd.products_id
                                                     and pd.language_id = :language_id
                                                     and s.status = 1
                                                     and p.products_view = 1
                                                     and (s.customers_group_id = 0 or s.customers_group_id = 99)
                                                     and p.products_archive = 0
                                                     and p.products_id = p2c.products_id
                                                     and p2c.categories_id = c.categories_id
                                                     and c.virtual_categories = 0
                                                     and c.status = 1
                                                     order by rand(),
                                                               s.specials_date_added DESC
                                                     limit :products_limit
                                                    ');

              $Qproduct->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
              $Qproduct->bindint(':products_limit', (int)MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_MAX_DISPLAY);
              $Qproduct->execute();
            } else {
// Requetes SQL pour afficher les nouveaux produits lors que l'on ce trouve dans une categorie

              $Qproduct = $CLICSHOPPING_Db->prepare('select p.products_id
                                                      from  :table_products_description pd,
                                                            :table_specials s,
                                                            :table_products p
                                                            :table_products_to_categories p2c,
                                                            :table_categories c,
                                                       where p.products_status = 1
                                                       and s.products_id = p.products_id
                                                       and p.products_id = pd.products_id
                                                       and pd.language_id = :language_id
                                                       and s.status = 1
                                                       and p.products_view = 1
                                                       and (s.customers_group_id = 0 or s.customers_group_id = 99)
                                                       and p.products_id = p2c.products_id
                                                       and p2c.categories_id = c.categories_id
                                                       and c.parent_id = :parent_id
                                                       and p.products_archive = 0
                                                       and c.virtual_categories = 0
                                                       and c.status = 1
                                                       order by rand(),
                                                                 s.specials_date_added DESC
                                                       limit :products_limit
                                                      ');

              $Qproduct->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
              $Qproduct->bindInt(':parent_id', (int)$new_products_category_id);
              $Qproduct->bindint(':products_limit', (int)MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_MAX_DISPLAY);
              $Qproduct->execute();
            }
          }

          if ($Qproduct->rowCount() > 0 ) {

// display number of short description
            $products_short_description_number = (int)MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SHORT_DESCRIPTION;
// delete words
            $delete_word = (int)MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SHORT_DESCRIPTION_DELETE_WORLDS;
// nbr of column to display  boostrap
            $bootstrap_column = (int)MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_COLUMNS;
// initialisation des boutons
            $size_button = $CLICSHOPPING_ProductsCommon->getSizeButton('md');

// Template define
            $filename = $CLICSHOPPING_Template-> getTemplateModulesFilename($this->group .'/template_html/' . MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_TEMPLATE);

            $new_prods_content = '<!-- Specials Start -->' . "\n";
            $new_prods_content .= '<div class="clearfix"></div>';

            if (MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_FRONT_TITLE == 'True') {
              $new_prods_content .= '<div class="page-title ModuleFrontPageSpecialsHeading"><h2>' . sprintf(CLICSHOPPING::getDef('module_front_page_specials_products_heading_title') , strftime('%B')) . '</h2></div>';
            }

            $new_prods_content .= '<div class="d-flex flex-wrap ModuleFrontPageboxContainerSpecials">';

            while ($Qproduct->fetch()) {
              $products_id = $Qproduct->valueInt('products_id');
              $_POST['products_id'] = $products_id;

              $in_stock = $Qproduct->valueInt('in_stock');

              $products_name_url = $CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($products_id);
//product name
              $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($products_id);
//Stock (good, alert, out of stock).
              $products_stock = $CLICSHOPPING_ProductsFunctionTemplate->getStock(MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_DISPLAY_STOCK, $products_id);
//Flash discount
              $products_flash_discount = $CLICSHOPPING_ProductsFunctionTemplate->getFlashDiscount($products_id, '<br />');
// Minimum quantity to take an order
              $min_order_quantity_products_display = $CLICSHOPPING_ProductsFunctionTemplate->getMinOrderQuantityProductDisplay($products_id);
// display a message in public function the customer group applied - before submit button
              $submit_button_view = $CLICSHOPPING_ProductsFunctionTemplate->getButtonView($products_id);
// button buy
              $buy_button = HTML::button(CLICSHOPPING::getDef('button_buy_now'), null, null, 'primary', null, 'sm');
              $CLICSHOPPING_ProductsCommon->getBuyButton($buy_button);
// Display an input allowing for the customer to insert a quantity
              $input_quantity = $CLICSHOPPING_ProductsFunctionTemplate->getDisplayInputQuantity(MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_DELETE_BUY_BUTTON, $products_id);
// display the differents prices before button
              $product_price = $CLICSHOPPING_ProductsCommon->getCustomersPrice($products_id);
//Short description
              $products_short_description = $CLICSHOPPING_ProductsCommon->getProductsShortDescription($products_id, $delete_word, $products_short_description_number);

// **************************
// display the differents buttons before minorder qty
// **************************
              $submit_button = '';
              $form = '';
              $endform = '';

              if (MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_DELETE_BUY_BUTTON == 'False') {
                if ($CLICSHOPPING_ProductsCommon->getProductsMinimumQuantity($products_id) != 0 && $CLICSHOPPING_ProductsCommon->getProductsQuantity($products_id) != 0) {
                  if ($CLICSHOPPING_ProductsAttributes->getHasProductAttributes($products_id) === false) {
                    $form = HTML::form('cart_quantity', CLICSHOPPING::link(null, 'Cart&Add' ),'post','class="justify-content-center"', ['tokenize' => true]). "\n";
                    $form .= HTML::hiddenField('products_id', $products_id);
                    $endform = '</form>';
                    $submit_button = $CLICSHOPPING_ProductsCommon->getProductsBuyButton($products_id);
                  }
                }
              }

// Quantity type
              $products_quantity_unit = $CLICSHOPPING_ProductsFunctionTemplate->getProductQuantityUnitType($products_id);


// **************************************************
// Button Free - Must be above getProductsExhausted
// **************************************************
              if ($CLICSHOPPING_ProductsCommon->getProductsOrdersView($products_id) != 1 && NOT_DISPLAY_PRICE_ZERO == 'false') {
                $submit_button = HTML::button(CLICSHOPPING::getDef('text_products_free'), '', $products_name_url, 'danger');
                $min_quantity = 0;
                $form = '';
                $endform = '';
                $input_quantity ='';
                $min_order_quantity_products_display = '';
              }

// **************************
// Display an information if the stock is exhausted for all groups
// **************************
              if (!empty($CLICSHOPPING_ProductsCommon->getProductsExhausted($products_id))) {
                $submit_button = $CLICSHOPPING_ProductsCommon->getProductsExhausted($products_id);
                $min_quantity = 0;
                $input_quantity = '';
                $min_order_quantity_products_display = '';
              }

// See the button more view details
              $button_small_view_details = $CLICSHOPPING_ProductsFunctionTemplate->getButtonViewDetails(MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_DELETE_BUY_BUTTON, $products_id);
// Display the image
              $products_image = $CLICSHOPPING_ProductsFunctionTemplate->getImage(MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_IMAGE_MEDIUM, $products_id);
// Ticker Image
              $products_image .= $CLICSHOPPING_ProductsFunctionTemplate->getTicker(MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_TICKER, $products_id, 'ModulesFrontPageTickerBootstrapTickerSpecial', 'ModulesFrontPageTickerBootstrapTickerFavorite', 'ModulesFrontPageTickerBootstrapTickerFeatured', 'ModulesFrontPageTickerBootstrapTickerNew');

              $ticker = $CLICSHOPPING_ProductsFunctionTemplate->getTickerPourcentage(MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_POURCENTAGE_TICKER, $products_id, 'ModulesFrontPageTickerBootstrapTickerPourcentage');

//******************************************************************************************************************
//            Options -- activate and insert code in template and css
//******************************************************************************************************************

// products model
              $products_model = $CLICSHOPPING_ProductsFunctionTemplate->getProductsModel($products_id);
// manufacturer
                $products_manufacturers = $CLICSHOPPING_ProductsFunctionTemplate->getProductsManufacturer($products_id);
// display the price by kilo
                $product_price_kilo = $CLICSHOPPING_ProductsFunctionTemplate->getProductsPriceByWeight($products_id);
// display date available
                $products_date_available =  $CLICSHOPPING_ProductsFunctionTemplate->getProductsDateAvailable($products_id);
// display products only shop
              $products_only_shop = $CLICSHOPPING_ProductsFunctionTemplate->getProductsOnlyTheShop($products_id);
// display products only shop
              $products_only_web = $CLICSHOPPING_ProductsFunctionTemplate->getProductsOnlyOnTheWebSite($products_id);
// display products packaging
                $products_packaging = $CLICSHOPPING_ProductsFunctionTemplate->getProductsPackaging($products_id);
// display shipping delay
                $products_shipping_delay =  $CLICSHOPPING_ProductsFunctionTemplate->getProductsShippingDelay($products_id);
// display products tag
                $tag = $CLICSHOPPING_ProductsFunctionTemplate->getProductsHeadTag($products_id);

                $products_tag = '';
                if (isset($tag) && is_array($tag)) {
                  foreach ($tag as $value) {
                    $products_tag .= '#<span class="productTag">' . HTML::link(CLICSHOPPING::link(null, 'Search&keywords='. HTML::outputProtected(utf8_decode($value) .'&search_in_description=1&categories_id=&inc_subcat=1'), 'rel="nofollow"'), $value) . '</span> ';
                  }
                }
// display products volume
            $products_volume = $CLICSHOPPING_ProductsFunctionTemplate->getProductsVolume($products_id);
// display products weight
            $products_weight = $CLICSHOPPING_ProductsFunctionTemplate->getProductsWeight($products_id);
// Reviews
              $avg_reviews = '<span class="ModulesReviews">' . HTML::stars($CLICSHOPPING_Reviews->getAverageProductReviews($products_id)) . '</span>';
// Json ltd
              $jsonLtd = $CLICSHOPPING_ProductsFunctionTemplate->getProductJsonLd($products_id);

//******************************************************************************************************************
//            End Options -- activate and insert code in template and css
//******************************************************************************************************************

// *************************
//      Template call
// **************************

              if (is_file($filename)) {
                ob_start();
                require($filename);
                $new_prods_content .= ob_get_clean();
              } else {
                echo CLICSHOPPING::getDef('template_does_not_exist') . '<br /> ' . $filename;
                exit;
              }
            } //while

            $new_prods_content .= '</div>' . "\n";

             $new_prods_content .= '<!-- Specials End -->' . "\n";

            $CLICSHOPPING_Template->addBlock($new_prods_content, $this->group);

          }
        } // max display
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Quel type de template souhaitez-vous voir affiché ?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_TEMPLATE',
          'configuration_value' => 'template_bootstrap_column_5.php',
          'configuration_description' => 'Veuillez indiquer le type de template que vous souhaitez voir affiché.<br /><br /><b>Note</b><br /> - Si vous avez opté pour une configuration en ligne, veuillez choisir un type de nom de template comme <u>template_line</u>.<br /><br /> - Si vous avez opté pour un affichage en colonne, veuillez choisir un type de nom de template comme <u>template_column</u> puis veuillez configurer le nombre de colonnes.<br />',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_multi_template_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to display the title?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_FRONT_TITLE',
          'configuration_value' => 'True',
          'configuration_description' => 'Displays the title of the module in the catalog',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indiquer le nombre de nouveaux produits à afficher sur la page d\'accueil',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_MAX_DISPLAY',
          'configuration_value' => '6',
          'configuration_description' => 'Veuillez indiquer le nombre maximum de nouveaux produits à afficher.',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the number of product columns you would like displayed?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_COLUMNS',
          'configuration_value' => '6',
          'configuration_description' => 'Please indicate the number of product columns to display per line. <br /> <br /> Note: <br /> <br /> - Between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to display a short description of the products on the page?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SHORT_DESCRIPTION',
          'configuration_value' => '0',
          'configuration_description' => 'Please indicate the length of this description. <br /> <br /> <i> - 0 for no description <br> - 50 for the first 50 characters </i>',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to delete a certain length of descriptive text?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SHORT_DESCRIPTION_DELETE_WORLDS',
          'configuration_value' => '0',
          'configuration_description' => 'Please indicate the number of words to delete. This system is useful with the tab module <br /> <br /> <i> - 0 for no deletion <br /> - 50 for the first 50 characters </i>',
          'configuration_group_id' => '6',
          'sort_order' => '8',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous afficher un message Nouveauté / Promotion /  Sélection / Coups de coeur?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => 'Afficher un message Nouveauté / Promotion / Sélection / Coups de coeur en surimpression sur l\'image du produit ?<br /><br />la durée est paramétrable dans le Menu configuration / ma boutique / Valeurs minimales / maximales<br><br><i>(Valeur true = Oui - Valeur false = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '9',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Would you like to display the percentage reduction of the price (special) ?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_POURCENTAGE_TICKER',
          'configuration_value' => 'False',
          'configuration_description' => 'Show the percentage reduction of the price',
          'configuration_group_id' => '6',
          'sort_order' => '9',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\') ',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Would you like to display an image regarding the stock status of the product ?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_DISPLAY_STOCK',
          'configuration_value' => 'none',
          'configuration_description' => 'Do you want to display an image indicating information on the stock of the product (In stock, practically sold out, out of stock) ?',
          'configuration_group_id' => '6',
          'sort_order' => '10',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'none\', \'image\', \'number\')',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please choose an image display size',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_IMAGE_MEDIUM',
          'configuration_value' => 'Small',
          'configuration_description' => 'What image size do you want to display?',
          'configuration_group_id' => '6',
          'sort_order' => '11',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Small\', \'Medium\')',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to delete the Buy and Retail button ?',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_DELETE_BUY_BUTTON',
          'configuration_value' => 'False',
          'configuration_description' => 'Remove buy and retail button',
          'configuration_group_id' => '6',
          'sort_order' => '11',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SORT_ORDER',
          'configuration_value' => '130',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '12',
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
      return array('MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_STATUS',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_TEMPLATE',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_FRONT_TITLE',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_MAX_DISPLAY',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_COLUMNS',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SHORT_DESCRIPTION',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SHORT_DESCRIPTION_DELETE_WORLDS',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_TICKER',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_POURCENTAGE_TICKER',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_DISPLAY_STOCK',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_IMAGE_MEDIUM',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_DELETE_BUY_BUTTON',
                    'MODULE_FRONT_PAGE_SPECIALS_PRODUCTS_SORT_ORDER'
                  );
    }
  }
