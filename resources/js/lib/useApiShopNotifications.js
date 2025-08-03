import { useToast } from 'vue-toastification';
import { i18n } from '@lib/config.js';

const toast = useToast();

export function useApiShopNotifications() {
    const handleError = (error, resource, action = 'error') => {
        if (error.errors) {
            Object.values(error?.errors).flat().forEach((err) => {
                toast.error(err);
            });
        } else {
            toast.error(error?.message || i18n.global.t(`api.${action}.error`, { resource }));
        }
    };

    const handleSuccess = (resource, action) => {
        toast.success(i18n.global.t(`validation.api.${action}.success`, { resource }));
    };

    return {
        handleError,
        handleSuccess,
    };
}