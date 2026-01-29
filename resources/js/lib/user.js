import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { useStorage, useSessionStorage } from "@vueuse/core";
import { useMutation, useQueryClient, useQuery } from "@tanstack/vue-query";
import {apiRequest} from '@lib/helpers.js'

const toast = useToast();
const userStorage = useStorage("user", {});
const userSessionStorage = useSessionStorage("user", {});

export const login = async (params) => {
    await getCookie();
    return await apiRequest('post', `/login`, params);
};

export const logout = async (params) => {
    return await apiRequest('post', `/logout`, params);
};

export const register = async (params) => {
    return await apiRequest('post', `/register`, params);
};

export const getCookie = async () => {
    return await apiRequest('get', `/sanctum/csrf-cookie`);
};

export const useLogin = () => {
    const router = useRouter();
    return useMutation({
        mutationFn: login,
        onSuccess: ({ user, token, remember }) => {
            if (remember == 1) {
                userStorage.value = { ...user, token,remember };
                userSessionStorage.value = {};
            } else if (remember == 0){
                userSessionStorage.value = { ...user, token,remember };
                userStorage.value = {};
            }
            router.push("/admin/dashboard");
        },
        onError: (error) => {
            toast.error(error.message || "An error occurred");
            router.push("/");
        },
    });
};

export const useLogout = () => {
    const router = useRouter();

    return useMutation({
        mutationFn: () => {
            const currentUser = userStorage.value.token ? userStorage.value : userSessionStorage.value;
            return logout({ user: currentUser, token: currentUser.token });
        },
        onSuccess: () => {
            userStorage.value = {};
            userSessionStorage.value = {};
            router.push("/admin/login");
        },
        onError: (error) => {
            userStorage.value = {};
            userSessionStorage.value = {};
            router.push("/admin/login");
            toast.error(error.message || "An error occurred");
        },
    });
};
