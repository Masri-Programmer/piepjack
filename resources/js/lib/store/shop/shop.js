import { createGlobalState, useStorage, useSessionStorage } from "@vueuse/core";
import { computed } from "vue";
import axios from "axios";
import { apiRequest } from "@lib/helpers.js";
export const useShopGlobalState = createGlobalState(() => {
    const cartState = useStorage("cart-state", {
        open: false,
        cartItems: [],
        promoCode: "",
        discounts: [],
    });
    const productParams = useSessionStorage("product-params-state", {
        active: null,
        category_id: null,
        page: 1,
        per_page: 15,
        search: "",
        sort_direction: "",
        sort_field: "",
    });
    const categoriesParams = useSessionStorage("categories-params-state", {
        active: null,
        show_children: false,
        page: 1,
        per_page: 15,
        search: "",
        sort_direction: "",
        sort_field: "",
    });

    const defaultCheckoutForm = {
        email: "",
        firstName: "",
        lastName: "",
        address: "",
        zip: "",
        land: "DE",
        city: "",
        stateProvince: "",
        phone: "",
        shippingMethodId: null,
        shippingMethod: null,
        billingSameAsShipping: true,
        billing: {
            firstName: "",
            lastName: "",
            address: "",
            zip: "",
            city: "",
            land: "DE",
            stateProvince: "",
        },
    };

    const checkoutform = useStorage(
        "checkout-form",
        defaultCheckoutForm,
        undefined,
        {
            mergeDefaults: true, // This ensures new fields are added to existing storage objects
        },
    );

    // Ensure structure integrity after loading from storage
    if (checkoutform.value && !checkoutform.value.billing) {
        checkoutform.value.billing = { ...defaultCheckoutForm.billing };
    }

    const cartTotalPrice = computed(() => {
        const total = cartState.value.cartItems.reduce((total, product) => {
            const productTotal = (product.items || []).reduce((acc, item) => {
                const price = Number(item.price) || 0;
                const quantity = Number(item.cartQuantity) || 0;
                return acc + price * quantity;
            }, 0);
            return total + productTotal;
        }, 0);

        return parseFloat(total.toFixed(2));
    });

    const cartTotalQuantity = computed(() => {
        return cartState.value.cartItems.reduce((total, product) => {
            const productQuantity = (product.items || []).reduce(
                (acc, item) => {
                    return acc + (Number(item.cartQuantity) || 0);
                },
                0,
            );
            return total + productQuantity;
        }, 0);
    });

    const addToCart = (product) => {
        const existingProduct = cartState.value.cartItems.find(
            (cartProduct) => cartProduct.id === product.id,
        );

        if (existingProduct) {
            product.items.forEach((item) => {
                const existingItem = existingProduct.items.find(
                    (cartItem) => cartItem.id === item.id,
                );
                if (existingItem) {
                    const maxQuantity = item.quantity;
                    existingItem.cartQuantity = Math.min(
                        existingItem.cartQuantity + 1,
                        maxQuantity,
                    );
                } else {
                    existingProduct.items.push({ ...item, cartQuantity: 1 });
                }
            });
        } else {
            const newProduct = {
                ...product,
                items: product.items.map((item) => ({
                    ...item,
                    cartQuantity: 1,
                })),
            };
            cartState.value.cartItems.push(newProduct);
        }
    };

    const updateItemQuantity = ({ productId, itemId, quantity }) => {
        const product = cartState.value.cartItems.find(
            (cartProduct) => cartProduct.id === productId,
        );

        if (product) {
            const item = product.items.find(
                (cartItem) => cartItem.id === itemId,
            );
            if (item) {
                const maxQuantity = item.quantity;
                item.cartQuantity = Math.max(
                    1,
                    Math.min(quantity, maxQuantity),
                );
            }
        }
    };

    const updateProductKey = ({ productId, key, value }) => {
        const product = cartState.value.cartItems.find(
            (cartProduct) => cartProduct.id === productId,
        );
        if (product) {
            product[key] = value;
        }
    };

    const updateItemKey = ({ productId, itemId, key, value }) => {
        const product = cartState.value.cartItems.find(
            (cartProduct) => cartProduct.id === productId,
        );
        if (product) {
            const item = product.items.find(
                (cartItem) => cartItem.id === itemId,
            );
            if (item) {
                item[key] = value;
            }
        }
    };

    const validateCart = async () => {
        if (cartState.value.cartItems.length) {
            for (const cartProduct of cartState.value.cartItems) {
                try {
                    const response = await apiRequest(
                        "get",
                        `/products/${cartProduct.id}`,
                    );
                    const validProduct = response.data;

                    if (!validProduct || validProduct.id !== cartProduct.id) {
                        console.warn(
                            `Product ${cartProduct.id} is invalid and will be removed.`,
                        );
                        cartState.value.cartItems =
                            cartState.value.cartItems.filter(
                                (cartItem) => cartItem.id !== cartProduct.id,
                            );
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
                        cartProduct.items = cartProduct.items
                            .map((cartItem) => {
                                const validItem = validProduct.items.find(
                                    (item) => item.id === cartItem.id,
                                );

                                if (!validItem) {
                                    console.warn(
                                        `Item ${cartItem.id} is invalid and will be removed.`,
                                    );
                                    return null;
                                }

                                return {
                                    ...cartItem,
                                    quantity: validItem.quantity,
                                    price: validItem.price,
                                    image_url: validItem.image_url,
                                    options: validItem.options,
                                };
                            })
                            .filter(Boolean); // Remove null items

                        // If no items are left, remove the product entirely
                        if (cartProduct.items.length === 0) {
                            cartState.value.cartItems =
                                cartState.value.cartItems.filter(
                                    (cp) => cp.id !== cartProduct.id,
                                );
                        }
                    }
                } catch (error) {
                    console.error(
                        `Error validating product ${cartProduct.id}:`,
                        error.message,
                    );
                }
            }
        }
    };

    const removeFromCart = ({ productId, itemId }) => {
        const productIndex = cartState.value.cartItems.findIndex(
            (cartProduct) => cartProduct.id === productId,
        );

        if (productIndex !== -1) {
            const items = cartState.value.cartItems[productIndex].items;
            cartState.value.cartItems[productIndex].items = items.filter(
                (item) => item.id !== itemId,
            );

            if (cartState.value.cartItems[productIndex].items.length === 0) {
                cartState.value.cartItems.splice(productIndex, 1);
            }
        }
    };

    const fetchDiscounts = async () => {
        try {
            const { data } = await apiRequest("get", "discounts");
            cartState.value.discounts = data;
        } catch (error) {
            console.error("Error fetching discounts:", error.message);
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
        fetchDiscounts,
    };
});
