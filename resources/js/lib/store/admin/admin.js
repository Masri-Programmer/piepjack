import { createGlobalState, useSessionStorage } from '@vueuse/core'

export const useShopGlobalState = createGlobalState(() => {
    const user = useSessionStorage('user-state', null)

    const productParams = useSessionStorage('product-params-state', {
        active: null,
        category_id: null,
        page: 1,
        per_page: 15,
        search: '',
        sort_direction: '',
        sort_field: '',
    })

    const categoriesParams = useSessionStorage('categories-params-state', {
        active: null,
        show_children: false,
        page: 1,
        per_page: 15,
        search: '',
        sort_direction: '',
        sort_field: '',
    })

    const ordersParams = useSessionStorage('orders-params-state', {
        page: 1,
        per_page: 15,
        search: '',
        sort_direction: '',
        sort_field: '',
    })

    const returnsParams = useSessionStorage('returns-params-state', {
        page: 1,
        per_page: 15,
        search: '',
        sort_direction: '',
        sort_field: '',
    })

    const usersParams = useSessionStorage('users-params-state', {
        page: 1,
        per_page: 15,
        search: '',
        sort_direction: 'desc',
        sort_field: 'created_at',
    })
    
    return {
        user,
        productParams,
        categoriesParams,
        ordersParams,
        returnsParams,
        usersParams,
    }
})
