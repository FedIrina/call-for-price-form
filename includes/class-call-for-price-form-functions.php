<?php
/**
 * Класс функций для плагина Call for Price Form
 */

// Если файл вызывается напрямую, прерываем выполнение
if (!defined('ABSPATH')) {
    exit;
}

class Call_For_Price_Form_Functions {
    
    /**
     * Конструктор класса
     */
    public function __construct() {
        // Добавляем хуки для переопределения шаблонов
        add_filter('woocommerce_locate_template', array($this, 'locate_template'), 10, 3);
        add_filter('woocommerce_locate_core_template', array($this, 'locate_template'), 10, 3);
        
        // Добавляем модальное окно в футер
        add_action('wp_footer', array($this, 'add_modal'));
        
        // Добавляем обертку для кнопок
        add_action('woocommerce_single_variation', array($this, 'open_buttons_wrapper'), 15);
        add_action('woocommerce_single_variation', array($this, 'close_buttons_wrapper'), 25);
        
        // Добавляем кнопку "Запросить цену"
        add_action('woocommerce_after_add_to_cart_form', array($this, 'add_request_price_button'));
    }
    
    /**
     * Переопределяет шаблоны WooCommerce
     */
    public function locate_template($template, $template_name, $template_path) {
        // Проверяем, нужны ли нам эти шаблоны
        $custom_templates = array(
            'single-product/add-to-cart/simple.php',
            'single-product/add-to-cart/variation-add-to-cart-button.php'
        );
        
        if (!in_array($template_name, $custom_templates)) {
            return $template;
        }
        
        // Ищем наш кастомный шаблон
        $custom_template = CALL_FOR_PRICE_FORM_PLUGIN_DIR . 'templates/' . $template_name;
        
        if (file_exists($custom_template)) {
            return $custom_template;
        }
        
        return $template;
    }
    
    /**
     * Добавляет модальное окно в футер
     */
    public function add_modal() {
        // Добавляем только на страницах товаров
        if (!is_product()) {
            return;
        }
        
        // Проверяем, есть ли Contact Form 7
        if (!function_exists('wpcf7_contact_form')) {
            return;
        }
        
        // Получаем ID формы из настроек
        $form_id = Call_For_Price_Form_Settings::get_option('call_for_price_form_id', 'b8cad0b');
        
        // Получаем заголовок модального окна из настроек
        $modal_title = Call_For_Price_Form_Settings::get_option('call_for_price_modal_title', __('Запросить цену', 'call-for-price-form'));
        
        echo '<div class="modal fade" id="requestPriceModal" tabindex="-1" aria-labelledby="requestPriceModalLabel" aria-hidden="true">';
        echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h5 class="modal-title" id="requestPriceModalLabel">' . esc_html($modal_title) . '</h5>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        echo '</div>';
        echo '<div class="modal-body">';
        
        // Выводим форму Contact Form 7
        if (function_exists('wpcf7_contact_form')) {
            echo do_shortcode('[contact-form-7 id="' . $form_id . '" html_class="form-default"]');
        }
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Формирует название вариации из названия товара и атрибутов
     * 
     * @param WC_Product_Variable $product Родительский товар
     * @param array $variation_attributes Атрибуты вариации
     * @param WC_Product_Variation|null $variation_object Объект вариации (опционально)
     * @return string Название вариации
     */
    public static function get_variation_name($product, $variation_attributes = array(), $variation_object = null) {
        if (!$product || !is_a($product, 'WC_Product_Variable')) {
            return '';
        }
        
        // Получаем название родительского товара
        $product_name = $product->get_name();
        
        // Если нет атрибутов, возвращаем только название товара
        if (empty($variation_attributes)) {
            return $product_name;
        }
        
        // Собираем названия атрибутов
        $attribute_names = array();
        
        foreach ($variation_attributes as $attribute_name => $attribute_value) {
            // Убираем префикс 'attribute_' если есть
            $clean_attribute_name = str_replace('attribute_', '', $attribute_name);
            
            // Получаем объект атрибута
            $attribute = $product->get_attribute($clean_attribute_name);
            
            if ($attribute) {
                // Если атрибут имеет таксономию, получаем название термина
                $taxonomy = wc_attribute_taxonomy_name($clean_attribute_name);
                if (taxonomy_exists($taxonomy)) {
                    $term = get_term_by('slug', $attribute_value, $taxonomy);
                    if ($term && !is_wp_error($term)) {
                        $attribute_names[] = $term->name;
                    } else {
                        $attribute_names[] = $attribute_value;
                    }
                } else {
                    // Для пользовательских атрибутов используем значение как есть
                    $attribute_names[] = $attribute_value;
                }
            } else {
                // Если атрибут не найден, используем значение как есть
                $attribute_names[] = $attribute_value;
            }
        }
        
        // Добавляем артикул вариации, если он есть
        if ($variation_object && is_a($variation_object, 'WC_Product_Variation')) {
            $sku = $variation_object->get_sku();
            if (!empty($sku)) {
                $attribute_names[] = 'арт. ' . $sku;
            }
        }
        
        // Формируем итоговое название
        if (!empty($attribute_names)) {
            return $product_name . ' - ' . implode(', ', $attribute_names);
        }
        
        return $product_name;
    }
    
    /**
     * Открывает обертку для кнопок
     */
    public function open_buttons_wrapper() {
        echo '<div class="product__buttons-wrapper">';
    }
    
    /**
     * Закрывает обертку для кнопок
     */
    public function close_buttons_wrapper() {
        echo '</div>';
    }
    
    /**
     * Добавляет кнопку "Запросить цену" для товаров без цены
     */
    public function add_request_price_button() {
        global $product;
        
        // Проверяем, что мы на странице товара и товар существует
        if (!is_product() || !$product) {
            return;
        }
        
        // Проверяем, что у товара нет цены
        if ('' !== $product->get_price()) {
            return;
        }
        
        // Проверяем, что товар в наличии
        if (!$product->is_in_stock()) {
            return;
        }
        ?>
        <div class="product__buttons-wrapper">
        <!-- Кнопка "Запросить цену" для товаров без цены -->
            <button type="button" class="btn btn-primary request-price-button" data-bs-toggle="modal" data-bs-target="#requestPriceModal" data-product-id="<?php echo esc_attr($product->get_id()); ?>" 
            data-product-name="<?php echo esc_attr($product->get_name()); ?>" data-product-url="<?php echo esc_attr($product->get_permalink()) ; ?>">
            <?php echo esc_html(Call_For_Price_Form_Settings::get_option('call_for_price_button_text', __('Запросить цену', 'call-for-price-form'))) ?>
            </button>
        <?php echo do_shortcode('[addtoany]'); ?>
        </div>
        <?php
    }
} 