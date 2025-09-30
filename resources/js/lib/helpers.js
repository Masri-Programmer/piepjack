import axios from "axios";
import { useStorage, useSessionStorage } from "@vueuse/core";
import { useQuery, useInfiniteQuery } from "@tanstack/vue-query";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { useDateFormat } from "@vueuse/core";
import { i18n } from "@lib/config.js";
import { useApiNotifications } from "@lib/useApiNotifications";
import { useApiShopNotifications } from "@lib/useApiShopNotifications";
import { useArea } from "@lib/useArea.js";
const { getApiUrl } = useArea();

const userStorage = useStorage("user", {});
const userSessionStorage = useSessionStorage("user", {});

axios.defaults.headers.post["Content-Type"] = "application/json";
axios.defaults.headers.common["Accept-Language"] = i18n.global.locale;
// axios.defaults.withCredentials = true;
// axios.defaults.withXSRFToken = true;

export const apiRequest = async (method, url, data = {}, params = {}) => {
    axios.defaults.headers.common["Authorization"] = `Bearer ${
        userSessionStorage.value.token || userStorage.value.token || ""
    }`;
    axios.defaults.baseURL = getApiUrl();

    const resource = url.split("/").pop();

    const methodToMessage = {
        get: i18n.global.t("validation.api.get.error", { resource }),
        post: i18n.global.t("validation.api.post.error", { resource }),
        put: i18n.global.t("validation.api.put.error", { resource }),
        delete: i18n.global.t("validation.api.delete.error", { resource }),
    };

    try {
        const response = await axios({
            method,
            url,
            data,
            params,
        });
        return response.data;
    } catch (error) {
        throw new Error(
            error.response?.data?.message ||
                methodToMessage[method] ||
                i18n.global.t("validation.api.error")
        );
    }
};

export const createApiResource = (resource) => ({
    get: (params = {}) => apiRequest("get", `/${resource}`, {}, params),
    getById: (id) => apiRequest("get", `/${resource}/${id}`),
    store: (data) => apiRequest("post", `/${resource}`, data),
    update: (id, data) => apiRequest("put", `/${resource}/${id}`, data),
    delete: (id) => apiRequest("delete", `/${resource}/${id}`),
});

export const apiQuery = (resource) => {
    const api = createApiResource(resource);
    const queryClient = useQueryClient();
    const { handleError: handleAdminError, handleSuccess: handleAdminSuccess } =
        useApiNotifications();
    const { handleError: handleShopError, handleSuccess: handleShopSuccess } =
        useApiShopNotifications();

    const area = localStorage.getItem("area");
    const handleError = area === "admin" ? handleAdminError : handleShopError;
    const handleSuccess =
        area === "admin" ? handleAdminSuccess : handleShopSuccess;

    return {
        useGet: (params) =>
            useQuery({
                queryKey: [resource, params],
                queryFn: () => api.get(params?.value),
                keepPreviousData: true,
                staleTime: 1000 * 60 * 5,
                refetchOnReconnect: "always",
                refetchOnWindowFocus: "always",
                refetchIntervalInBackground: false,
            }),

        useGetById: (id) =>
            useQuery({
                queryKey: [resource, id],
                queryFn: () => api.getById(id.value),
                cacheTime: 0,
                staleTime: 0,
                enabled: !!id,
            }),

        useStore: () =>
            useMutation({
                mutationFn: (params) => api.store(params),
                onSuccess: () => {
                    handleSuccess(resource, "post");
                    queryClient.invalidateQueries([resource]);
                },
                onError: (error) => {
                    console.error(error);
                    handleError(error, resource, "post");
                },
            }),

        useDelete: () =>
            useMutation({
                mutationFn: (id) => api.delete(id),
                onSuccess: () => {
                    const confirmed = window.confirm(
                        i18n.global.t("validation.api.delete.confirm")
                    );
                    if (!confirmed) {
                        throw new Error(
                            i18n.global.t("validation.api.delete.cancel")
                        );
                    }
                    handleSuccess(resource, "delete");
                    queryClient.invalidateQueries([resource]);
                },
                onError: (error) => {
                    console.error(error);
                    handleError(error, resource, "delete");
                },
            }),

        useUpdate: () =>
            useMutation({
                mutationFn: ({ id, data }) => api.update(id, data),
                onSuccess: () => {
                    handleSuccess(resource, "put");
                    queryClient.invalidateQueries([resource]);
                },
                onError: (error) => {
                    console.error(error);
                    handleError(error, resource, "put");
                },
            }),
    };
};

export function formatDate(date) {
    return useDateFormat(new Date(date), "MMMM DD, YYYY, h:mm A").value;
}
