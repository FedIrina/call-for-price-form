/**
 * Обработка модального окна "Запросить цену"
 */
document.addEventListener('DOMContentLoaded', function() {
    // Получаем модальное окно
    const requestPriceModal = document.getElementById('requestPriceModal');
    
    if (requestPriceModal) {
        // Обработчик открытия модального окна
        requestPriceModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Кнопка, которая открыла модальное окно
            
            // Получаем информацию о товаре из data-атрибутов кнопки
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const productUrl = button.getAttribute('data-product-url');
            const variationId = button.getAttribute('data-variation-id');
            const variationName = button.getAttribute('data-variation-name');
            
            // Находим форму Contact Form 7 внутри модального окна
            const cf7Form = requestPriceModal.querySelector('.wpcf7-form');
            
            if (cf7Form) {
                // Добавляем или обновляем скрытые поля в форме Contact Form 7
                addOrUpdateHiddenField(cf7Form, 'product_id', productId);
                addOrUpdateHiddenField(cf7Form, 'product_name', productName);
                addOrUpdateHiddenField(cf7Form, 'product_url', productUrl);
                
                // Добавляем информацию о вариации, если она есть
                if (variationId) {
                    addOrUpdateHiddenField(cf7Form, 'variation_id', variationId);
                    addOrUpdateHiddenField(cf7Form, 'variation_name', variationName || '');
                } else {
                    // Удаляем поля вариации, если их нет
                    removeHiddenField(cf7Form, 'variation_id');
                    removeHiddenField(cf7Form, 'variation_name');
                }
            }
        });
        
        // Находим форму Contact Form 7 внутри модального окна
        const cf7Form = requestPriceModal.querySelector('.wpcf7-form');
        
        if (cf7Form) {
            // Обработчик успешной отправки формы
            cf7Form.addEventListener('wpcf7mailsent', function(event) {
                // Закрываем модальное окно после успешной отправки
                const modal = bootstrap.Modal.getInstance(requestPriceModal);
                if (modal) {
                    modal.hide();
                }
                
                // Очищаем скрытые поля
                removeHiddenField(cf7Form, 'product_id');
                removeHiddenField(cf7Form, 'product_name');
                removeHiddenField(cf7Form, 'product_url');
                removeHiddenField(cf7Form, 'variation_id');
                removeHiddenField(cf7Form, 'variation_name');
            });
            
            // Обработчик ошибки отправки формы
            cf7Form.addEventListener('wpcf7invalid', function(event) {
                // Можно добавить дополнительную логику для обработки ошибок
                console.log('Form validation failed');
            });
            
            // Обработчик отправки формы
            cf7Form.addEventListener('wpcf7submit', function(event) {
                // Можно добавить дополнительную логику при отправке
                console.log('Form submitted');
            });
        }
    }
});

/**
 * Добавляет или обновляет скрытое поле в форме
 */
function addOrUpdateHiddenField(form, fieldName, fieldValue) {
    let field = form.querySelector(`input[name="${fieldName}"]`);
    
    if (!field) {
        // Создаем новое скрытое поле
        field = document.createElement('input');
        field.type = 'hidden';
        field.name = fieldName;
        form.appendChild(field);
    }
    
    field.value = fieldValue;
}

/**
 * Удаляет скрытое поле из формы
 */
function removeHiddenField(form, fieldName) {
    const field = form.querySelector(`input[name="${fieldName}"]`);
    if (field) {
        field.remove();
    }
} 