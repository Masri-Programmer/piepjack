import { useShopGlobalState } from "./admin";

const {
    user,
    productParams,
    categoriesParams,
    ordersParams,
    returnsParams,
    usersParams,
    searchTerm,
    page,
    handlePageChange,
} = useShopGlobalState();

export {
    user,
    productParams,
    categoriesParams,
    ordersParams,
    returnsParams,
    usersParams,
    searchTerm,
    page,
    handlePageChange,
}
