<template>
    <PageLayout :data="links" :title="$t('admin.sidebar.logs', 'System Logs')">
        <div class="dashboard grid col-span-full h-[85vh]">
            <iframe src="/admin/logs" class="w-full h-full border-0"></iframe>
        </div>
    </PageLayout>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import PageLayout from "@layouts/admin/PageLayout.vue";

const { t } = useI18n();

const links = computed(() => [
    {
        title: t("common.menu.home", "Home"),
        link: "",
    },
    {
        title: t("admin.sidebar.logs", "System Logs"),
        current: true,
        link: "",
    },
]);
</script>
