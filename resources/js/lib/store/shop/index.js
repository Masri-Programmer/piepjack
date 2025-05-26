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
}
