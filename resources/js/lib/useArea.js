// composables/useArea.js
import { ref, readonly, watch } from 'vue';
import { useSessionStorage } from '@vueuse/core';

const VITE_API_URL = (import.meta.env.VITE_API_URL || '').replace(/\/$/, "");
const initialArea = sessionStorage.getItem('area') || 'shop';
const _area = useSessionStorage('area', initialArea);
const _apiUrl = ref('');

const _updateApiUrl = (currentAreaValue) => {
    let newApiUrl = VITE_API_URL;
    if (currentAreaValue === 'shop') {
        newApiUrl += '/shop';
    } else if (currentAreaValue === 'admin') {
        newApiUrl += '/admin';
    }
    _apiUrl.value = newApiUrl;
};

_updateApiUrl(_area.value);

watch(_area, (newArea, oldArea) => {
    if (newArea !== oldArea) {
        _updateApiUrl(newArea);
    }
});

export const setAppArea = (newAreaValue) => {
    if (['shop', 'admin'].includes(newAreaValue)) {
        if (_area.value !== newAreaValue) {
            _area.value = newAreaValue;
        }
    } else {
        console.warn(`[useArea] Attempted to set an unknown area: ${newAreaValue}. Allowed: 'shop', 'admin'.`);
    }
};

const getApiUrl = () => {
    return _apiUrl.value;
};

const getApiArea = () => {
    return _area.value;
};

export function useArea() {
    return {
        area: readonly(_area),
        apiUrl: readonly(_apiUrl),
        updateArea: setAppArea,
        getApiUrl,
        getApiArea,
    };
}