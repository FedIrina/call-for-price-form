<?php
/**
 * Класс настроек для плагина Call for Price Form
 */

// Если файл вызывается напрямую, прерываем выполнение
if (!defined('ABSPATH')) {
    exit;
}

class Call_For_Price_Form_Settings {
    
    /**
     * Конструктор класса
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'init_settings'));
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_settings_tabs_call_for_price_form', array($this, 'settings_tab'));
        add_action('woocommerce_update_options_call_for_price_form', array($this, 'update_settings'));
    }
    
    /**
     * Добавляет вкладку в настройки WooCommerce
     */
    public function add_settings_tab($settings_tabs) {
        $settings_tabs['call_for_price_form'] = __('Запрос цены', 'call-for-price-form');
        return $settings_tabs;
    }
    
    /**
     * Содержимое вкладки настроек
     */
    public function settings_tab() {
        woocommerce_admin_fields($this->get_settings());
    }
    
    /**
     * Сохранение настроек
     */
    public function update_settings() {
        woocommerce_update_options($this->get_settings());
    }
    
    /**
     * Получает настройки
     */
    public function get_settings() {
        $settings = array(
            array(
                'title' => __('Настройки формы запроса цены', 'call-for-price-form'),
                'type' => 'title',
                'desc' => __('Настройте форму Contact Form 7 для обработки запросов цен.', 'call-for-price-form'),
                'id' => 'call_for_price_form_settings'
            ),
            array(
                'title' => __('Выберите форму Contact Form 7', 'call-for-price-form'),
                'desc' => __('Выберите форму, которая будет использоваться для обработки запросов цен.', 'call-for-price-form'),
                'id' => 'call_for_price_form_id',
                'type' => 'select',
                'options' => $this->get_contact_forms(),
                'default' => 'b8cad0b'
            ),
            array(
                'title' => __('Текст кнопки "Запросить цену"', 'call-for-price-form'),
                'desc' => __('Текст, который будет отображаться на кнопке вместо "Добавить в корзину".', 'call-for-price-form'),
                'id' => 'call_for_price_button_text',
                'type' => 'text',
                'default' => __('Запросить цену', 'call-for-price-form')
            ),
            array(
                'title' => __('Заголовок модального окна', 'call-for-price-form'),
                'desc' => __('Заголовок, который будет отображаться в модальном окне.', 'call-for-price-form'),
                'id' => 'call_for_price_modal_title',
                'type' => 'text',
                'default' => __('Запросить цену', 'call-for-price-form')
            ),
            array(
                'type' => 'sectionend',
                'id' => 'call_for_price_form_settings'
            ),
            array(
                'title' => __('Инструкции по настройке формы', 'call-for-price-form'),
                'type' => 'title',
                'desc' => $this->get_form_instructions(),
                'id' => 'call_for_price_form_instructions'
            ),
            array(
                'type' => 'sectionend',
                'id' => 'call_for_price_form_instructions'
            )
        );
        
        return apply_filters('call_for_price_form_settings', $settings);
    }
    
    /**
     * Получает список форм Contact Form 7
     */
    private function get_contact_forms() {
        $forms = array();
        
        if (function_exists('wpcf7_contact_form')) {
            $contact_forms = get_posts(array(
                'post_type' => 'wpcf7_contact_form',
                'numberposts' => -1,
                'post_status' => 'publish'
            ));
            
            foreach ($contact_forms as $form) {
                $forms[$form->ID] = $form->post_title . ' (ID: ' . $form->ID . ')';
            }
        }
        
        if (empty($forms)) {
            $forms[''] = __('Формы не найдены. Убедитесь, что Contact Form 7 установлен и активирован.', 'call-for-price-form');
        }
        
        return $forms;
    }
    
    /**
     * Получает инструкции по настройке формы
     */
    private function get_form_instructions() {
        $instructions = '<div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa; margin: 10px 0;">';
        $instructions .= '<h4>' . __('Рекомендуемые поля для формы Contact Form 7:', 'call-for-price-form') . '</h4>';
        $instructions .= '<ul style="margin-left: 20px;">';
        $instructions .= '<li><strong>[email* your-email]</strong> - ' . __('Email клиента (обязательное)', 'call-for-price-form') . '</li>';
        $instructions .= '</ul>';
        $instructions .= '<h4>' . __('Скрытые поля (добавляются автоматически):', 'call-for-price-form') . '</h4>';
        $instructions .= '<ul style="margin-left: 20px;">';
        $instructions .= '<li><strong>[hidden product_id]</strong> - ' . __('ID товара', 'call-for-price-form') . '</li>';
        $instructions .= '<li><strong>[hidden product_name]</strong> - ' . __('Название товара', 'call-for-price-form') . '</li>';
        $instructions .= '<li><strong>[hidden product_url]</strong> - ' . __('URL страницы товара', 'call-for-price-form') . '</li>';
        $instructions .= '<li><strong>[hidden variation_id]</strong> - ' . __('ID вариации (если есть)', 'call-for-price-form') . '</li>';
        $instructions .= '<li><strong>[hidden variation_name]</strong> - ' . __('Название вариации (если есть)', 'call-for-price-form') . '</li>';
        $instructions .= '</ul>';
        $instructions .= '<h4>' . __('Пример полной формы:', 'call-for-price-form') . '</h4>';
        $instructions .= '<pre style="background: #fff; padding: 10px; border: 1px solid #ddd; overflow-x: auto;">';
        $instructions .= htmlspecialchars('<label>Ваше имя *
    [text* your-name]
</label>

<label>Email *
    [email* your-email]
</label>

<label>Телефон
    [tel your-phone]
</label>

<label>Сообщение
    [textarea your-message]
</label>

[hidden product_id]
[hidden product_name]
[hidden product_url]
[hidden variation_id]
[hidden variation_name]

[submit "Отправить запрос"]');
        $instructions .= '</pre>';
        $instructions .= '</div>';
        
        return $instructions;
    }
    
    /**
     * Получает значение настройки
     */
    public static function get_option($option_name, $default = '') {
        return get_option($option_name, $default);
    }
} 