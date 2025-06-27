/**
 * Обработка вариаций товаров для функциональности "Запросить цену"
 */
document.addEventListener("DOMContentLoaded", function() {
    // Функция для обновления текста кнопки
    function updateButtonText(price, formElement = null) {
        const form = formElement || document.querySelector(".variations_form");
        if (!form) return;
        
        const addToCartButton = form.querySelector(".single_add_to_cart_button");
        const requestPriceContainer = document.querySelector(".variation-request-price-container");
        
        if (price) {
            // Есть цена - показываем кнопку "В корзину"
            if (addToCartButton) {
                addToCartButton.style.display = "inline-block";
                const priceSpan = addToCartButton.querySelector(".variation-price");
                if (priceSpan) {
                    priceSpan.innerHTML = price;
                }
            }
            
            if (requestPriceContainer) {
                requestPriceContainer.style.display = "none";
            }
        } else {
            // Нет цены - показываем кнопку "Запросить цену"
            if (addToCartButton) {
                addToCartButton.style.display = "none";
            }
            
            if (requestPriceContainer) {
                requestPriceContainer.style.display = "block";
            }
        }
    }
    
    // Обработчики вариаций (глобальная область)
    jQuery(document).ready(function($) {
        // Инициализация при загрузке страницы
        if ($(".variations_form").length) {
            const requestPriceContainer = document.querySelector(".variation-request-price-container");
            if (requestPriceContainer) {
                requestPriceContainer.style.display = "none";
            }
            
            $(".variations_form").each(function() {
                updateButtonText(null, this);
            });
        }
        
        // Обработчик изменения вариации
        $(document).on("found_variation", ".variations_form", function(event, variation) {
            const hasPrice = variation && (variation.display_price && variation.display_price > 0);
            
            if (hasPrice) {
                const priceToShow = variation.price_html || variation.price || variation.display_price;
                updateButtonText(priceToShow, this);
            } else {
                updateButtonText(null, this);
            }
        }); 
        
        // Обработчик сброса вариации
        $(document).on("reset_data", "form.variations_form", function() {
            updateButtonText(null, this);
        });
        
        // Обработчик скрытия вариации
        $(document).on("hide_variation", "form.variations_form", function() {
            updateButtonText(null, this);
        });
    });
});
