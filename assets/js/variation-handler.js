/**
 * Обработка вариаций товаров для функциональности "Запросить цену"
 * Только переключение видимости кнопок. Вывод цены на кнопке — зона ответственности темы.
 */
document.addEventListener("DOMContentLoaded", function() {
    var requestPriceContainerTimeout = null;

    // Переключение видимости кнопок: "Добавить в корзину" / "Запросить цену"
    function updateButtonVisibility(showAddToCart, formElement = null) {
        const form = formElement || document.querySelector(".variations_form");
        if (!form) return;
        
        const addToCartButton = form.querySelector(".single_add_to_cart_button");
        const requestPriceContainer = document.querySelector(".variation-request-price-container");
        
        if (requestPriceContainerTimeout) {
            clearTimeout(requestPriceContainerTimeout);
            requestPriceContainerTimeout = null;
        }
        
        if (showAddToCart) {
            if (addToCartButton) {
                addToCartButton.style.display = "";
            }
            if (requestPriceContainer) {
                requestPriceContainer.style.display = "none";
            }
        } else {
            if (addToCartButton) {
                addToCartButton.style.display = "none";
            }
            if (requestPriceContainer) {
                requestPriceContainerTimeout = setTimeout(function() {
                    requestPriceContainer.style.display = "flex";
                    requestPriceContainerTimeout = null;
                }, 300);
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
                updateButtonVisibility(false, this);
            });
        }
        
        // Обработчик изменения вариации
        $(document).on("found_variation", ".variations_form", function(event, variation) {
            const hasPrice = variation && (variation.display_price && variation.display_price > 0);
            updateButtonVisibility(hasPrice, this);
        }); 
        
        // Обработчик сброса вариации
        $(document).on("reset_data", "form.variations_form", function() {
            updateButtonVisibility(false, this);
        });
        
        // Обработчик скрытия вариации
        $(document).on("hide_variation", "form.variations_form", function() {
            updateButtonVisibility(false, this);
        });
    });
});
