import { createGlobalState, useStorage, useSessionStorage } from '@vueuse/core';
import axios from 'axios'

export const useShopGlobalState = createGlobalState(() => {
    const cartState = useStorage('cart-state', { 'open': false, 'cartItems': [], 'promoCode': '' });
    const productParams = useSessionStorage('product-params-state', {
        'active': null,
        'category_id': null,
        'page': 1,
        'per_page': 15,
        'search': '',
        'sort_direction': '',
        'sort_field': '',
    })
    const categoriesParams = useSessionStorage('categories-params-state', {
        'active': null,
        'show_children': false,
        'page': 1,
        'per_page': 15,
        'search': '',
        'sort_direction': '',
        'sort_field': '',
    })

    const checkoutform = useStorage("checkout-form", {
        email: "",
        firstName: "",
        lastName: "",
        address: "",
        zip: "",
        land: "",
        city: "",
    });

    function cartTotalPrice() {
        const total = cartState.value.cartItems.reduce((total, product) => {
            return total + product.items.reduce((productTotal, item) => {
                return productTotal + item.price * item.cartQuantity;
            }, 0);
        }, 0);

        return parseFloat(total.toFixed(2));
    };

    function cartTotalQuantity() {
        return cartState.value.cartItems.reduce((total, product) => {
            return total + product.items.reduce((productTotal, item) => {
                return productTotal + item.cartQuantity;
            }, 0);
        }, 0);
    }

    const addToCart = (product) => {
        const existingProduct = cartState.value.cartItems.find(cartProduct => cartProduct.id === product.id);

        if (existingProduct) {
            product.items.forEach(item => {
                const existingItem = existingProduct.items.find(cartItem => cartItem.id === item.id);
                if (existingItem) {
                    const maxQuantity = item.quantity;
                    existingItem.cartQuantity = Math.min(existingItem.cartQuantity + 1, maxQuantity);
                } else {
                    existingProduct.items.push({ ...item, cartQuantity: 1 });
                }
            });
        } else {
            const newProduct = {
                ...product,
                items: product.items.map(item => ({ ...item, cartQuantity: Math.min(1, item.quantity) }))
            };
            cartState.value.cartItems.push(newProduct);
        }
    };

    const updateItemQuantity = ({ productId, itemId, quantity }) => {
        const product = cartState.value.cartItems.find(cartProduct => cartProduct.id === productId);

        if (product) {
            const item = product.items.find(cartItem => cartItem.id === itemId);
            if (item) {
                const maxQuantity = item.quantity;
                item.cartQuantity = Math.min(quantity, maxQuantity);
            }
        }
    };

    const updateProductKey = ({ productId, key, value }) => {
        const product = cartState.value.cartItems.find(cartProduct => cartProduct.id === productId);
        if (product) {
            product[key] = value;
        }
    };

    const updateItemKey = ({ productId, itemId, key, value }) => {
        const product = cartState.value.cartItems.find(cartProduct => cartProduct.id === productId);
        if (product) {
            const item = product.items.find(cartItem => cartItem.id === itemId);
            if (item) {
                item[key] = value;
            }
        }
    };

    const validateCart = async () => {
        if (cartState.value.cartItems.length) {
            for (const cartProduct of cartState.value.cartItems) {
                try {
                    const response = await axios.get(`/products/${cartProduct.id}`);
                    const validProduct = response.data.data;

                    if (!validProduct || validProduct.id !== cartProduct.id) {
                        console.warn(`Product ${cartProduct.id} is invalid and will be removed.`);
                        cartState.value.cartItems = cartState.value.cartItems.filter(cartItem => cartItem.id !== cartProduct.id);
                        continue;
                    }

                    // Update product details
                    Object.assign(cartProduct, {
                        name: validProduct.name,
                        slug: validProduct.slug,
                        image_url: validProduct.image_url,
                        price: validProduct.price,
                        description: validProduct.description,
                        category_id: validProduct.category_id,
                        category_name: validProduct.category_name,
                    });

                    if (cartProduct.items) {
                        cartProduct.items = cartProduct.items.map(cartItem => {
                            const validItem = validProduct.items.find(item => item.id === cartItem.id);

                            if (!validItem) {
                                console.warn(`Item ${cartItem.id} is invalid and will be removed.`);
                                return null;
                            }

                            return {
                                ...cartItem,
                                quantity: validItem.quantity,
                                price: validItem.price,
                                image_url: validItem.image_url,
                                options: validItem.options,
                            };
                        }).filter(Boolean); // Remove null items
                    }
                } catch (error) {
                    console.error(`Error validating product ${cartProduct.id}:`, error.message);
                }
            }
        }
    };

    const removeFromCart = ({ productId, itemId }) => {
        const productIndex = cartState.value.cartItems.findIndex(cartProduct => cartProduct.id === productId);

        if (productIndex !== -1) {
            const items = cartState.value.cartItems[productIndex].items;
            cartState.value.cartItems[productIndex].items = items.filter(item => item.id !== itemId);

            if (cartState.value.cartItems[productIndex].items.length === 0) {
                cartState.value.cartItems.splice(productIndex, 1);
            }
        }
    };

    return {
        cartState,
        checkoutform,
        productParams,
        categoriesParams,
        updateItemQuantity,
        cartTotalQuantity,
        updateProductKey,
        cartTotalPrice,
        removeFromCart,
        updateItemKey,
        validateCart,
        addToCart,
    };
});
