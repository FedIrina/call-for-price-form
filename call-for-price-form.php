<?php
/**
 * Plugin Name: Call for Price Form
 * Plugin URI: https://casalusso.ru
 * Description: Добавляет функциональность "Запросить цену" для товаров WooCommerce без цены
 * Version: 1.0.0
 * Author: Casalusso
 * Author URI: https://casalusso.ru
 * Text Domain: call-for-price-form
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

// Если файл вызывается напрямую, прерываем выполнение
if (!defined('ABSPATH')) {
    exit;
}

// Определяем константы плагина
define('CALL_FOR_PRICE_FORM_VERSION', '1.0.0');
define('CALL_FOR_PRICE_FORM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CALL_FOR_PRICE_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Основной класс плагина
 */
class Call_For_Price_Form {
    
    /**
     * Конструктор класса
     */
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    /**
     * Инициализация плагина
     */
    public function init() {
        // Проверяем, активирован ли WooCommerce
        if (!$this->is_woocommerce_active()) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Подключаем функциональность
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Проверяет, активирован ли WooCommerce
     */
    private function is_woocommerce_active() {
        return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
    }
    
    /**
     * Уведомление об отсутствии WooCommerce
     */
    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>';
        echo __('Call for Price Form требует активированный плагин WooCommerce для работы.', 'call-for-price-form');
        echo '</p></div>';
    }
    
    /**
     * Подключает необходимые файлы
     */
    private function includes() {
        // Подключаем функции
        require_once CALL_FOR_PRICE_FORM_PLUGIN_DIR . 'includes/class-call-for-price-form-functions.php';
        
        // Подключаем шаблоны
        require_once CALL_FOR_PRICE_FORM_PLUGIN_DIR . 'includes/class-call-for-price-form-templates.php';
        
        // Подключаем настройки
        require_once CALL_FOR_PRICE_FORM_PLUGIN_DIR . 'includes/class-call-for-price-form-settings.php';
    }
    
    /**
     * Инициализирует хуки
     */
    private function init_hooks() {
        // Подключаем скрипты и стили
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Инициализируем классы
        new Call_For_Price_Form_Functions();
        new Call_For_Price_Form_Templates();
        new Call_For_Price_Form_Settings();
    }
    
    /**
     * Подключает скрипты и стили
     */
    public function enqueue_scripts() {
        // Подключаем только на страницах товаров
        if (is_product()) {
            // JavaScript для модального окна
            wp_enqueue_script(
                'call-for-price-modal',
                CALL_FOR_PRICE_FORM_PLUGIN_URL . 'assets/js/request-price-modal.js',
                array('jquery'),
                CALL_FOR_PRICE_FORM_VERSION,
                true
            );
            
            // JavaScript для обработки вариаций
            wp_enqueue_script(
                'call-for-price-variations',
                CALL_FOR_PRICE_FORM_PLUGIN_URL . 'assets/js/variation-handler.js',
                array('jquery'),
                CALL_FOR_PRICE_FORM_VERSION,
                true
            );
            
            // CSS для модального окна
            wp_enqueue_style(
                'call-for-price-modal',
                CALL_FOR_PRICE_FORM_PLUGIN_URL . 'assets/css/request-price-modal.css',
                array(),
                CALL_FOR_PRICE_FORM_VERSION
            );
        }
    }
}

// Инициализируем плагин
new Call_For_Price_Form();

/**
 * Активация плагина
 */
register_activation_hook(__FILE__, 'call_for_price_form_activate');

function call_for_price_form_activate() {
    // Проверяем наличие WooCommerce при активации
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('Этот плагин требует активированный WooCommerce для работы.', 'call-for-price-form'));
    }
}

/**
 * Деактивация плагина
 */
register_deactivation_hook(__FILE__, 'call_for_price_form_deactivate');

function call_for_price_form_deactivate() {
    // Очистка при деактивации, если необходимо
} 