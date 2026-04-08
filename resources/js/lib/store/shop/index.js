import { useShopGlobalState } from "./shop";

const {
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
} = useShopGlobalState();

export {
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
}
