/**
 * Обработка вариаций товаров для функциональности "Запросить цену"
 * Только переключение видимости кнопок. Вывод цены на кнопке — зона ответственности темы.
 */
document.addEventListener("DOMContentLoaded", function() {
    // Переключение видимости кнопок: "Добавить в корзину" / "Запросить цену"
    function updateButtonVisibility(showAddToCart, formElement = null) {
        const form = formElement || document.querySelector(".variations_form");
        if (!form) return;
        
        const addToCartButton = form.querySelector(".single_add_to_cart_button");
        const requestPriceContainer = document.querySelector(".variation-request-price-container");
        const addToCartWrap = form.querySelector(".woocommerce-variation-add-to-cart");
        const addToAnyInAddToCart = addToCartWrap ? addToCartWrap.querySelector(".addtoany_shortcode") : null;
        
        if (showAddToCart) {
            if (addToCartWrap) {
                addToCartWrap.classList.remove("woocommerce-variation-add-to-cart-disabled");
            }
            if (addToAnyInAddToCart) {
                addToAnyInAddToCart.style.display = "";
            }
            if (addToCartButton) {
                addToCartButton.style.display = "";
            }
            if (requestPriceContainer) {
                requestPriceContainer.style.display = "none";
            }
        } else {
            if (addToCartWrap) {
                addToCartWrap.classList.add("woocommerce-variation-add-to-cart-disabled");
            }
            if (addToCartButton) {
                addToCartButton.style.display = "none";
            }
            if (addToAnyInAddToCart) {
                addToAnyInAddToCart.style.display = "none";
            }
            if (requestPriceContainer) {
                requestPriceContainer.style.display = "flex";
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
        
        // Обработчик изменения вариации (show_variation — тот же, что у WooCommerce для класса woocommerce-variation-add-to-cart-disabled)
        $(document).on("show_variation", ".variations_form", function(event, variation, purchasable) {
            updateButtonVisibility(purchasable, this);
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
