<?php
/**
 * Класс шаблонов для плагина Call for Price Form
 */

// Если файл вызывается напрямую, прерываем выполнение
if (!defined('ABSPATH')) {
    exit;
}

class Call_For_Price_Form_Templates {
    
    /**
     * Конструктор класса
     */
    public function __construct() {
        // Добавляем хуки для переопределения шаблонов
        add_filter('woocommerce_locate_template', array($this, 'locate_template'), 10, 3);
        add_filter('woocommerce_locate_core_template', array($this, 'locate_template'), 10, 3);
    }
    
    /**
     * Переопределяет шаблоны WooCommerce
     */
    public function locate_template($template, $template_name, $template_path) {
        // Проверяем, нужны ли нам эти шаблоны
        $custom_templates = array(
            /* 'single-product/add-to-cart/simple.php', */
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
} 